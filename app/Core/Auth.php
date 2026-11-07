<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function user(): ?array
    {
        Session::start();

        if (self::$loaded) {
            return self::$user;
        }

        self::$loaded = true;
        $id = (int) ($_SESSION['user_id'] ?? 0);

        if ($id <= 0) {
            return null;
        }

        $pdo = Database::connection();
        if (!$pdo) {
            return null;
        }

        try {
            $stmt = $pdo->prepare(
                'SELECT id, role, name, mobile, email, photo, locality, message,
                        show_public, is_anonymous_default, status
                 FROM users WHERE id = :id LIMIT 1'
            );
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();

            if (!$user || $user['status'] !== 'active') {
                self::logout();
                return null;
            }

            self::$user = $user;

            return $user;
        } catch (Throwable) {
            return null;
        }
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): int
    {
        return (int) (self::user()['id'] ?? 0);
    }

    public static function role(): string
    {
        return (string) (self::user()['role'] ?? '');
    }

    public static function isStaff(): bool
    {
        return in_array(self::role(), ['super_admin', 'admin'], true);
    }

    /** @param array<string, mixed> $user */
    public static function login(array $user): void
    {
        Session::start();
        Session::regenerate();

        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['_csrf']);

        self::$user = $user;
        self::$loaded = true;

        $pdo = Database::connection();
        if ($pdo) {
            try {
                $stmt = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
                $stmt->execute(['id' => (int) $user['id']]);
            } catch (Throwable) {
                // Login must not fail because of this bookkeeping update.
            }
        }
    }

    public static function logout(): void
    {
        Session::start();

        unset($_SESSION['user_id']);
        self::$user = null;
        self::$loaded = true;

        Session::regenerate();
    }

    public static function homeFor(?string $role = null): string
    {
        $role = $role ?: self::role();

        return match ($role) {
            'super_admin', 'admin' => url('/dashboard'),
            default                => url('/profile'),
        };
    }

    public static function requireLogin(?string $next = null): void
    {
        if (self::check()) {
            return;
        }

        $target = url('/login');
        if ($next) {
            $target .= '?next=' . rawurlencode($next);
        }

        redirect($target);
    }

    /** @param array<int, string> $roles */
    public static function requireRole(array $roles, ?string $next = null): void
    {
        self::requireLogin($next);

        if (!in_array(self::role(), $roles, true)) {
            http_response_code(403);
            echo view('errors.error', [
                'code'    => 403,
                'title'   => 'Access denied',
                'message' => 'Your account does not have permission to open this page.',
                'back'    => url('/'),
            ]);
            exit;
        }
    }
}
