<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

final class Database
{
    private static ?PDO $pdo = null;
    private static bool $attempted = false;

    /**
     * Returns a PDO connection, or null when the database is unavailable.
     * The public honour board degrades gracefully to demo data.
     */
    public static function connection(): ?PDO
    {
        if (self::$attempted) {
            return self::$pdo;
        }

        self::$attempted = true;

        $host    = (string) Config::get('db.host', '127.0.0.1');
        $port    = (string) Config::get('db.port', '3306');
        $name    = (string) Config::get('db.name', 'cca');
        $charset = (string) Config::get('db.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            self::$pdo = new PDO(
                $dsn,
                (string) Config::get('db.user', 'root'),
                (string) Config::get('db.pass', ''),
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (Throwable) {
            self::$pdo = null;
        }

        return self::$pdo;
    }

    public static function isConnected(): bool
    {
        return self::connection() instanceof PDO;
    }
}
