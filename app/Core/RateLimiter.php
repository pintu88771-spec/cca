<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class RateLimiter
{
    private static function maxAttempts(): int
    {
        return max(1, (int) Config::get('security.login_max_attempts', 5));
    }

    private static function lockMinutes(): int
    {
        return max(1, (int) Config::get('security.login_lock_minutes', 15));
    }

    public static function allowed(string $identifier, string $ip): bool
    {
        $row = self::row($identifier, $ip);
        if (!$row) {
            return true;
        }

        if ((int) $row['attempts'] < self::maxAttempts()) {
            return true;
        }

        return strtotime((string) $row['last_attempt']) < time() - (self::lockMinutes() * 60);
    }

    public static function minutesLeft(string $identifier, string $ip): int
    {
        $row = self::row($identifier, $ip);
        if (!$row || (int) $row['attempts'] < self::maxAttempts()) {
            return 0;
        }

        $unlockAt = strtotime((string) $row['last_attempt']) + (self::lockMinutes() * 60);

        return max(1, (int) ceil(($unlockAt - time()) / 60));
    }

    public static function hit(string $identifier, string $ip): void
    {
        $pdo = Database::connection();
        if (!$pdo) {
            return;
        }

        try {
            $row = self::row($identifier, $ip);

            if (!$row) {
                $stmt = $pdo->prepare(
                    'INSERT INTO login_attempts (identifier, ip, attempts, last_attempt)
                     VALUES (:identifier, :ip, 1, NOW())'
                );
                $stmt->execute(['identifier' => $identifier, 'ip' => $ip]);
                return;
            }

            $stale = strtotime((string) $row['last_attempt']) < time() - (self::lockMinutes() * 60);
            $attempts = $stale ? 1 : (int) $row['attempts'] + 1;

            $stmt = $pdo->prepare(
                'UPDATE login_attempts SET attempts = :attempts, last_attempt = NOW()
                 WHERE identifier = :identifier AND ip = :ip'
            );
            $stmt->execute([
                'attempts'   => $attempts,
                'identifier' => $identifier,
                'ip'         => $ip,
            ]);
        } catch (Throwable) {
            // Never block login because the limiter table is unavailable.
        }
    }

    public static function clear(string $identifier, string $ip): void
    {
        $pdo = Database::connection();
        if (!$pdo) {
            return;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE identifier = :identifier AND ip = :ip');
            $stmt->execute(['identifier' => $identifier, 'ip' => $ip]);
        } catch (Throwable) {
            // Ignore.
        }
    }

    /** @return array<string, mixed>|null */
    private static function row(string $identifier, string $ip): ?array
    {
        $pdo = Database::connection();
        if (!$pdo) {
            return null;
        }

        try {
            $stmt = $pdo->prepare(
                'SELECT attempts, last_attempt FROM login_attempts
                 WHERE identifier = :identifier AND ip = :ip LIMIT 1'
            );
            $stmt->execute(['identifier' => $identifier, 'ip' => $ip]);
            $row = $stmt->fetch();

            return $row ?: null;
        } catch (Throwable) {
            return null;
        }
    }
}
