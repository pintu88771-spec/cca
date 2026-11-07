<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        Session::start();

        if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        Session::start();
        $expected = $_SESSION['_csrf'] ?? null;

        return is_string($token)
            && is_string($expected)
            && hash_equals($expected, $token);
    }

    public static function require(): void
    {
        if (self::verify($_POST['_token'] ?? null)) {
            return;
        }

        http_response_code(419);
        echo view('errors.error', [
            'code'    => 419,
            'title'   => 'Session expired',
            'message' => 'Your session expired for security reasons. Go back, reload the page and try again.',
            'back'    => url('/'),
        ]);
        exit;
    }
}
