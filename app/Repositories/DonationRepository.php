<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Core\Settings;
use PDO;
use Throwable;

final class DonationRepository
{
    public function activeFestival(): ?array
    {
        try {
            $row = $this->pdo()->query(
                'SELECT * FROM festivals WHERE is_active = 1 ORDER BY year DESC LIMIT 1'
            )->fetch();

            return $row ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->pdo()->prepare(
                'SELECT d.*, u.name AS donor_name, u.mobile AS donor_mobile, u.locality AS donor_locality,
                        f.name AS festival_name, f.year AS festival_year,
                        a.name AS approver_name
                 FROM donations d
                 JOIN users u ON u.id = d.donor_id
                 JOIN festivals f ON f.id = d.festival_id
                 LEFT JOIN users a ON a.id = d.approved_by
                 WHERE d.id = :id LIMIT 1'
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            return $row ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    public function findForDonor(int $id, int $donorId): ?array
    {
        $row = $this->find($id);

        return $row && (int) $row['donor_id'] === $donorId ? $row : null;
    }

    /** @return array<int, array<string, mixed>> */
    public function forDonor(int $donorId, int $limit = 50): array
    {
        try {
            $stmt = $this->pdo()->prepare(
                'SELECT d.*, f.name AS festival_name, f.year AS festival_year, c.name AS collector_name
                 FROM donations d
                 JOIN festivals f ON f.id = d.festival_id
                 LEFT JOIN users c ON c.id = d.collected_by
                 WHERE d.donor_id = :donor
                 ORDER BY d.created_at DESC, d.id DESC
                 LIMIT ' . max(1, min(200, $limit))
            );
            $stmt->execute(['donor' => $donorId]);

            return $stmt->fetchAll();
        } catch (Throwable) {
            return [];
        }
    }

    /** @return array{approved_total: float, pending_total: float, pending_count: int, approved_count: int, rank: int|null} */
    public function statsForDonor(int $donorId): array
    {
        $stats = [
            'approved_total' => 0.0,
            'pending_total'  => 0.0,
            'pending_count'  => 0,
            'approved_count' => 0,
            'rank'           => null,
        ];

        try {
            $pdo = $this->pdo();
            $festival = $this->activeFestival();
            $festivalId = (int) ($festival['id'] ?? 0);

            $stmt = $pdo->prepare(
                "SELECT
                    COALESCE(SUM(CASE WHEN status = 'approved' THEN amount END), 0) AS approved_total,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approved_count,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount END), 0) AS pending_total,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending_count
                 FROM donations
                 WHERE donor_id = :donor AND festival_id = :festival"
            );
            $stmt->execute(['donor' => $donorId, 'festival' => $festivalId]);
            $row = $stmt->fetch() ?: [];

            $stats['approved_total'] = (float) ($row['approved_total'] ?? 0);
            $stats['approved_count'] = (int) ($row['approved_count'] ?? 0);
            $stats['pending_total']  = (float) ($row['pending_total'] ?? 0);
            $stats['pending_count']  = (int) ($row['pending_count'] ?? 0);

            if ($festivalId > 0 && $stats['approved_total'] > 0) {
                $rankStmt = $pdo->prepare(
                    "SELECT COUNT(*) + 1 AS rank FROM (
                        SELECT donor_id, SUM(amount) AS total
                        FROM donations
                        WHERE festival_id = :festival AND status = 'approved'
                        GROUP BY donor_id
                    ) ranked
                    WHERE ranked.total > :mine"
                );
                $rankStmt->execute(['festival' => $festivalId, 'mine' => $stats['approved_total']]);
                $stats['rank'] = (int) $rankStmt->fetchColumn();
            }
        } catch (Throwable) {
            // Return the zeroed defaults.
        }

        return $stats;
    }

    /** True when this UTR is already used by a live (pending/approved) donation. */
    public function utrExists(string $utr): bool
    {
        try {
            $stmt = $this->pdo()->prepare(
                "SELECT 1 FROM donations
                 WHERE utr_no = :utr AND status IN ('pending','approved') LIMIT 1"
            );
            $stmt->execute(['utr' => $utr]);

            return (bool) $stmt->fetchColumn();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param array{amount: float, mode: string, utr: ?string, screenshot: ?string, is_anonymous: bool} $data
     */
    public function create(int $donorId, int $festivalId, array $data, ?int $collectedBy = null): int
    {
        $stmt = $this->pdo()->prepare(
            "INSERT INTO donations
                (festival_id, donor_id, amount, mode, utr_no, screenshot, collected_by,
                 status, is_anonymous, created_at)
             VALUES
                (:festival, :donor, :amount, :mode, :utr, :screenshot, :collector,
                 'pending', :anonymous, NOW())"
        );
        $stmt->execute([
            'festival'   => $festivalId,
            'donor'      => $donorId,
            'amount'     => $data['amount'],
            'mode'       => $data['mode'],
            'utr'        => $data['utr'] ?: null,
            'screenshot' => $data['screenshot'] ?: null,
            'collector'  => $collectedBy,
            'anonymous'  => $data['is_anonymous'] ? 1 : 0,
        ]);

        return (int) $this->pdo()->lastInsertId();
    }

    /** @return array<int, array<string, mixed>> */
    public function pending(int $limit = 60): array
    {
        try {
            $stmt = $this->pdo()->prepare(
                'SELECT d.*, u.name AS donor_name, u.mobile AS donor_mobile, u.locality AS donor_locality,
                        f.name AS festival_name
                 FROM donations d
                 JOIN users u ON u.id = d.donor_id
                 JOIN festivals f ON f.id = d.festival_id
                 WHERE d.status = \'pending\'
                 ORDER BY d.created_at ASC, d.id ASC
                 LIMIT ' . max(1, min(200, $limit))
            );
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function recentApproved(int $limit = 8): array
    {
        try {
            $stmt = $this->pdo()->prepare(
                'SELECT d.*, u.name AS donor_name, u.mobile AS donor_mobile, a.name AS approver_name
                 FROM donations d
                 JOIN users u ON u.id = d.donor_id
                 LEFT JOIN users a ON a.id = d.approved_by
                 WHERE d.status = \'approved\'
                 ORDER BY d.approved_at DESC, d.id DESC
                 LIMIT ' . max(1, min(100, $limit))
            );
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (Throwable) {
            return [];
        }
    }

    /** @return array{pending_count: int, pending_total: float, approved_total: float, approved_count: int, donor_count: int, today_total: float} */
    public function dashboardStats(): array
    {
        $stats = [
            'pending_count'  => 0,
            'pending_total'  => 0.0,
            'approved_total' => 0.0,
            'approved_count' => 0,
            'donor_count'    => 0,
            'today_total'    => 0.0,
        ];

        try {
            $pdo = $this->pdo();
            $festival = $this->activeFestival();
            $festivalId = (int) ($festival['id'] ?? 0);

            $stmt = $pdo->prepare(
                "SELECT
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending_count,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount END), 0) AS pending_total,
                    COALESCE(SUM(CASE WHEN status = 'approved' THEN amount END), 0) AS approved_total,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approved_count,
                    COUNT(DISTINCT CASE WHEN status = 'approved' THEN donor_id END) AS donor_count,
                    COALESCE(SUM(CASE WHEN status = 'approved' AND DATE(approved_at) = CURDATE() THEN amount END), 0) AS today_total
                 FROM donations
                 WHERE festival_id = :festival"
            );
            $stmt->execute(['festival' => $festivalId]);
            $row = $stmt->fetch() ?: [];

            $stats['pending_count']  = (int) ($row['pending_count'] ?? 0);
            $stats['pending_total']  = (float) ($row['pending_total'] ?? 0);
            $stats['approved_total'] = (float) ($row['approved_total'] ?? 0);
            $stats['approved_count'] = (int) ($row['approved_count'] ?? 0);
            $stats['donor_count']    = (int) ($row['donor_count'] ?? 0);
            $stats['today_total']    = (float) ($row['today_total'] ?? 0);
        } catch (Throwable) {
            // Return the zeroed defaults.
        }

        return $stats;
    }

    /** @return array{ok: bool, receipt: ?string, error: ?string} */
    public function approve(int $id, int $adminId): array
    {
        $pdo = $this->pdo();

        try {
            $pdo->beginTransaction();

            $donation = $this->lockDonation($pdo, $id);
            if (!$donation) {
                $pdo->rollBack();
                return ['ok' => false, 'receipt' => null, 'error' => 'Donation not found.'];
            }

            if ($donation['status'] !== 'pending') {
                $pdo->rollBack();
                return ['ok' => false, 'receipt' => null, 'error' => 'Only pending donations can be approved.'];
            }

            $festival = $this->lockFestival($pdo, (int) $donation['festival_id']);
            $receipt = $this->nextReceiptNo($pdo, (int) $donation['festival_id'], (int) ($festival['year'] ?? date('Y')));

            $stmt = $pdo->prepare(
                "UPDATE donations
                 SET status = 'approved', approved_by = :admin, approved_at = NOW(),
                     receipt_no = :receipt, reason = NULL
                 WHERE id = :id AND status = 'pending'"
            );
            $stmt->execute(['admin' => $adminId, 'receipt' => $receipt, 'id' => $id]);

            $pdo->commit();

            return ['ok' => true, 'receipt' => $receipt, 'error' => null];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['ok' => false, 'receipt' => null, 'error' => 'Could not approve this donation. Please retry.'];
        }
    }

    /** @return array{ok: bool, error: ?string} */
    public function reject(int $id, int $adminId, string $reason): array
    {
        return $this->close($id, $adminId, $reason, 'rejected', ['pending']);
    }

    /** @return array{ok: bool, error: ?string} */
    public function cancel(int $id, int $adminId, string $reason): array
    {
        return $this->close($id, $adminId, $reason, 'cancelled', ['pending', 'approved']);
    }

    /**
     * @param array<int, string> $allowedFrom
     * @return array{ok: bool, error: ?string}
     */
    private function close(int $id, int $adminId, string $reason, string $status, array $allowedFrom): array
    {
        $pdo = $this->pdo();

        try {
            $pdo->beginTransaction();

            $donation = $this->lockDonation($pdo, $id);
            if (!$donation) {
                $pdo->rollBack();
                return ['ok' => false, 'error' => 'Donation not found.'];
            }

            if (!in_array($donation['status'], $allowedFrom, true)) {
                $pdo->rollBack();
                return ['ok' => false, 'error' => 'This donation can no longer be ' . $status . '.'];
            }

            $stmt = $pdo->prepare(
                'UPDATE donations
                 SET status = :status, reason = :reason, approved_by = :admin, approved_at = NOW()
                 WHERE id = :id'
            );
            $stmt->execute([
                'status' => $status,
                'reason' => $reason,
                'admin'  => $adminId,
                'id'     => $id,
            ]);

            $pdo->commit();

            return ['ok' => true, 'error' => null];
        } catch (Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['ok' => false, 'error' => 'Could not update this donation. Please retry.'];
        }
    }

    private function nextReceiptNo(PDO $pdo, int $festivalId, int $year): string
    {
        $prefix = trim((string) Settings::get('receipt_prefix', 'CH')) ?: 'CH';
        $pattern = $prefix . $year . '-%';

        $stmt = $pdo->prepare(
            'SELECT receipt_no FROM donations
             WHERE festival_id = :festival AND receipt_no LIKE :pattern
             ORDER BY receipt_no DESC LIMIT 1'
        );
        $stmt->execute(['festival' => $festivalId, 'pattern' => $pattern]);
        $last = (string) ($stmt->fetchColumn() ?: '');

        $sequence = 0;
        if ($last !== '' && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = (int) $matches[1];
        }

        do {
            $sequence++;
            $candidate = $prefix . $year . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            $check = $pdo->prepare('SELECT 1 FROM donations WHERE receipt_no = :receipt LIMIT 1');
            $check->execute(['receipt' => $candidate]);
        } while ((bool) $check->fetchColumn());

        return $candidate;
    }

    private function lockDonation(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM donations WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function lockFestival(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM festivals WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function pdo(): PDO
    {
        $pdo = Database::connection();

        if (!$pdo instanceof PDO) {
            throw new \RuntimeException('Database connection is unavailable.');
        }

        return $pdo;
    }
}
