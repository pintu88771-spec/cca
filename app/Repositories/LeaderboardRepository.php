<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Config;
use App\Core\Database;
use App\Support\Badge;
use App\Support\DemoData;
use PDO;
use Throwable;

final class LeaderboardRepository
{
    public function snapshot(): array
    {
        if (!(bool) Config::get('app.demo_mode', true) || !Database::isConnected()) {
            return $this->fromDatabase() ?? $this->emptyState();
        }

        return $this->fromDatabase() ?? DemoData::snapshot();
    }

    private function fromDatabase(): ?array
    {
        $pdo = Database::connection();
        if (!$pdo instanceof PDO) {
            return null;
        }

        try {
            $festival = $pdo->query(
                'SELECT * FROM festivals WHERE is_active = 1 ORDER BY year DESC LIMIT 1'
            )->fetch();

            if (!$festival) {
                return null;
            }

            $festivalId = (int) $festival['id'];

            $totalStmt = $pdo->prepare(
                "SELECT COALESCE(SUM(amount), 0) AS total, COUNT(DISTINCT donor_id) AS donors
                 FROM donations
                 WHERE festival_id = :fid AND status = 'approved'"
            );
            $totalStmt->execute(['fid' => $festivalId]);
            $totals = $totalStmt->fetch() ?: ['total' => 0, 'donors' => 0];

            $rowsStmt = $pdo->prepare(
                "SELECT d.donor_id, u.name, u.locality, u.message, u.photo, u.show_public,
                        SUM(d.amount) AS total,
                        MIN(d.approved_at) AS first_approved,
                        MIN(d.is_anonymous) AS all_anonymous
                 FROM donations d
                 JOIN users u ON u.id = d.donor_id
                 WHERE d.festival_id = :fid
                   AND d.status = 'approved'
                   AND u.status = 'active'
                 GROUP BY d.donor_id, u.name, u.locality, u.message, u.photo, u.show_public
                 ORDER BY total DESC, first_approved ASC"
            );
            $rowsStmt->execute(['fid' => $festivalId]);
            $rows = $rowsStmt->fetchAll();

            if (!$rows) {
                return null;
            }

            $cutoffs = $this->badgeCutoffs($pdo);
            $leaders = [];
            $rank = 0;

            foreach ($rows as $row) {
                // Anonymous / hidden donors are counted in totals but never displayed.
                if ((int) $row['all_anonymous'] === 1 || (int) $row['show_public'] === 0) {
                    continue;
                }

                $rank++;
                $leaders[] = [
                    'rank'      => $rank,
                    'donor_id'  => (int) $row['donor_id'],
                    'name'      => (string) $row['name'],
                    'locality'  => (string) ($row['locality'] ?? ''),
                    'message'   => (string) ($row['message'] ?? ''),
                    'amount'    => (float) $row['total'],
                    'photo'     => $row['photo'] ?: null,
                    'badge'     => Badge::forRank($rank, $cutoffs),
                    'is_anonymous' => false,
                ];
            }

            $target = (float) $festival['target_amount'];
            $total = (float) $totals['total'];
            $endDate = $festival['start_date'] ?: $festival['end_date'];

            return [
                'festival' => [
                    'name'         => (string) $festival['name'],
                    'committee'    => $this->setting($pdo, 'committee_name', (string) Config::get('app.name', 'Chanda Collection')),
                    'year'         => (int) $festival['year'],
                    'target'       => $target,
                    'total'        => $total,
                    'percent'      => $target > 0 ? min(100, round($total / $target * 100, 1)) : 0,
                    'days_left'    => $this->daysUntil((string) $endDate),
                    'donors_count' => (int) $totals['donors'],
                    'start_date'   => (string) $festival['start_date'],
                ],
                'leaders'    => $leaders,
                'updated_at' => date(DATE_ATOM),
                'demo'       => false,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function badgeCutoffs(PDO $pdo): array
    {
        $raw = $this->setting($pdo, 'badge_cutoffs', null);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;

        return is_array($decoded) ? $decoded : Badge::DEFAULTS;
    }

    private function setting(PDO $pdo, string $key, ?string $default): ?string
    {
        try {
            $stmt = $pdo->prepare('SELECT svalue FROM settings WHERE skey = :k LIMIT 1');
            $stmt->execute(['k' => $key]);
            $value = $stmt->fetchColumn();

            return $value === false ? $default : (string) $value;
        } catch (Throwable) {
            return $default;
        }
    }

    private function daysUntil(string $date): int
    {
        if ($date === '') {
            return 0;
        }

        try {
            $target = new \DateTimeImmutable($date);
            $today = new \DateTimeImmutable('today');
            $diff = (int) $today->diff($target)->format('%a');

            return $target >= $today ? $diff : 0;
        } catch (Throwable) {
            return 0;
        }
    }

    private function emptyState(): array
    {
        return [
            'festival' => [
                'name'         => 'Chhath Puja 2026',
                'committee'    => (string) Config::get('app.name', 'Chanda Collection'),
                'year'         => 2026,
                'target'       => 500000,
                'total'        => 0,
                'percent'      => 0,
                'days_left'    => 0,
                'donors_count' => 0,
                'start_date'   => '2026-11-17',
            ],
            'leaders'    => [],
            'updated_at' => date(DATE_ATOM),
            'demo'       => false,
        ];
    }
}
