<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Settings
{
    /** @var array<string, string|null> */
    private static array $cache = [];

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key] ?? $default;
        }

        $pdo = Database::connection();
        if (!$pdo) {
            return $default;
        }

        try {
            $stmt = $pdo->prepare('SELECT svalue FROM settings WHERE skey = :k LIMIT 1');
            $stmt->execute(['k' => $key]);
            $value = $stmt->fetchColumn();

            self::$cache[$key] = $value === false ? null : (string) $value;

            return self::$cache[$key] ?? $default;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);

        return $value === null ? $default : in_array($value, ['1', 'true', 'yes', 'on'], true);
    }
}
