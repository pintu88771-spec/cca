<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class AuditLog
{
    /** @param array<string, mixed> $details */
    public static function write(?int $userId, string $action, array $details = []): void
    {
        $pdo = Database::connection();
        if (!$pdo) {
            return;
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO audit_logs (user_id, action, details, ip, created_at)
                 VALUES (:user_id, :action, :details, :ip, NOW())'
            );
            $stmt->execute([
                'user_id' => $userId ?: null,
                'action'  => $action,
                'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
                'ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Throwable) {
            // Audit logging must never break the user flow.
        }
    }
}
