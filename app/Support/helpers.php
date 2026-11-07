<?php

declare(strict_types=1);

use App\Core\Config;

/** Read a dot-notation config value. */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/** HTML-escape any output (XSS protection). */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Absolute path under the served base (works in sub-folders). */
function url(string $path = ''): string
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

/** Public asset URL with cache-busting version. */
function asset(string $path): string
{
    $full = ROOT_PATH . '/public/' . ltrim($path, '/');
    $version = is_file($full) ? (string) filemtime($full) : '1';

    return url($path) . '?v=' . $version;
}

/** Render a view file and return its HTML. */
function view(string $template, array $data = []): string
{
    $file = ROOT_PATH . '/resources/views/' . str_replace('.', '/', $template) . '.php';

    if (!is_file($file)) {
        throw new RuntimeException("View not found: {$template}");
    }

    extract($data, EXTR_SKIP);
    ob_start();
    require $file;

    return (string) ob_get_clean();
}

/** Indian-style currency, e.g. 245300 → ₹2,45,300 */
function inr(float|int|string|null $amount, bool $withSymbol = true): string
{
    $value = (int) round((float) $amount);
    $negative = $value < 0;
    $digits = (string) abs($value);

    if (strlen($digits) > 3) {
        $last3 = substr($digits, -3);
        $rest = substr($digits, 0, -3);
        $rest = (string) preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        $digits = $rest . ',' . $last3;
    }

    return ($negative ? '-' : '') . ($withSymbol ? '₹' : '') . $digits;
}

/** Initials for avatar placeholders (supports Hindi). */
function initials(string $name, int $max = 2): string
{
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $initials = '';

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }
        $initials .= mb_substr($part, 0, 1, 'UTF-8');
        if (mb_strlen($initials, 'UTF-8') >= $max) {
            break;
        }
    }

    return $initials !== '' ? $initials : '🙏';
}

/** Stable avatar colour tone (0–4) from a name. */
function avatar_tone(string $name): int
{
    return (int) (crc32($name) % 5);
}

/* ------------------------------------------------------------------
 * HTTP / session helpers
 * ------------------------------------------------------------------ */

/** Redirect to an app path (or absolute URL) and stop execution. */
function redirect(string $to): never
{
    if (!preg_match('~^https?://~i', $to)) {
        $to = url($to);
    }

    header('Location: ' . $to, true, 302);
    exit;
}

/** Hidden CSRF input for forms. */
function csrf_field(): string
{
    return \App\Core\Csrf::field();
}

/** Queue a flash message for the next request. */
function flash(string $type, string $message): void
{
    \App\Core\Session::flash($type, $message);
}

/** @return array<int, array{type: string, message: string}> */
function flashes(): array
{
    return \App\Core\Session::pullFlashes();
}

/** Remember submitted input so a failed form can be re-filled. */
function remember_old(array $input): void
{
    unset($input['_token'], $input['password'], $input['password_confirm'], $input['current_password'], $input['new_password']);
    \App\Core\Session::start();
    $_SESSION['_old'] = $input;
}

/** Read old input, clearing it once the form is rendered. */
function old(string $key, mixed $default = ''): mixed
{
    \App\Core\Session::start();
    $value = $_SESSION['_old'][$key] ?? $default;

    return is_string($value) ? $value : $default;
}

/** Drop remembered input after a successful submission. */
function clear_old(): void
{
    \App\Core\Session::start();
    unset($_SESSION['_old']);
}

/** Only allow local absolute paths as ?next= targets (blocks open redirects). */
function safe_next(?string $next, string $fallback = '/'): string
{
    $next = trim((string) $next);

    if ($next === '' || !str_starts_with($next, '/') || str_starts_with($next, '//')) {
        return url($fallback);
    }

    if (preg_match('~^(?:/\\\\|/%2f)~i', $next)) {
        return url($fallback);
    }

    return url($next);
}

/* ------------------------------------------------------------------
 * Labels / formatting
 * ------------------------------------------------------------------ */

function role_label(string $role): string
{
    return match ($role) {
        'super_admin' => 'Super Admin',
        'admin'       => 'Admin',
        'volunteer'   => 'Volunteer',
        'bhaktpremi'  => 'Bhaktpremi',
        default       => ucfirst(str_replace('_', ' ', $role)),
    };
}

function status_label(string $status): string
{
    return match ($status) {
        'pending'   => 'Awaiting approval',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
        'cancelled' => 'Cancelled',
        default     => ucfirst($status),
    };
}

/** Tone used by .pill--{tone} in internal views. */
function status_tone(string $status): string
{
    return match ($status) {
        'approved'  => 'success',
        'pending'   => 'warning',
        'rejected'  => 'danger',
        'cancelled' => 'muted',
        default     => 'muted',
    };
}

function mode_label(string $mode): string
{
    return match ($mode) {
        'cash'    => 'Cash',
        'upi'     => 'UPI',
        'gateway' => 'Gateway',
        default   => ucfirst($mode),
    };
}

function format_date(?string $value, string $format = 'd M Y, g:i A'): string
{
    if (!$value) {
        return '—';
    }

    try {
        return (new DateTimeImmutable($value))->format($format);
    } catch (Throwable) {
        return '—';
    }
}

function mask_mobile(string $mobile): string
{
    return strlen($mobile) === 10
        ? substr($mobile, 0, 3) . '••••' . substr($mobile, -3)
        : $mobile;
}

/** Currently authenticated user row (cached per request). */
function current_user(): ?array
{
    return \App\Core\Auth::user();
}

function is_staff(): bool
{
    return \App\Core\Auth::isStaff();
}

