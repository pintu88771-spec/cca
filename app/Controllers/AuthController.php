<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\RateLimiter;
use App\Repositories\LeaderboardRepository;
use App\Repositories\UserRepository;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect(Auth::homeFor());
        }

        $mode = ($_GET['mode'] ?? '') === 'staff' ? 'staff' : 'donor';
        $next = (string) ($_GET['next'] ?? '');

        echo view('layouts.auth', [
            'content'   => view('auth.login', [
                'mode' => $mode,
                'next' => $next,
            ]),
            'state'     => (new LeaderboardRepository())->snapshot(),
            'pageTitle' => 'Sign in — Chanda Collection',
            'mode'      => $mode,
        ]);
    }

    public function login(): void
    {
        Csrf::require();

        $mobile   = preg_replace('/\D+/', '', (string) ($_POST['mobile'] ?? '')) ?? '';
        $password = (string) ($_POST['password'] ?? '');
        $mode     = ($_POST['mode'] ?? '') === 'staff' ? 'staff' : 'donor';
        $next     = (string) ($_POST['next'] ?? '');
        $ip       = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

        if ($mobile === '' || $password === '') {
            flash('danger', 'Enter your mobile number and password.');
            remember_old(['mobile' => $mobile, 'mode' => $mode, 'next' => $next]);
            redirect('/login?mode=' . $mode . ($next !== '' ? '&next=' . rawurlencode($next) : ''));
        }

        if (!RateLimiter::allowed($mobile, $ip)) {
            $minutes = RateLimiter::minutesLeft($mobile, $ip);
            flash('danger', "Too many failed attempts. Try again in {$minutes} minute" . ($minutes === 1 ? '' : 's') . '.');
            remember_old(['mobile' => $mobile, 'mode' => $mode, 'next' => $next]);
            redirect('/login?mode=' . $mode);
        }

        $users = new UserRepository();
        $user = $users->findByMobile($mobile);

        $valid = $user !== null
            && $user['status'] === 'active'
            && password_verify($password, (string) $user['password_hash']);

        if (!$valid) {
            RateLimiter::hit($mobile, $ip);
            AuditLog::write($user['id'] ?? null, 'login.failed', ['mobile' => $mobile, 'mode' => $mode]);
            flash('danger', 'Incorrect mobile number or password.');
            remember_old(['mobile' => $mobile, 'mode' => $mode, 'next' => $next]);
            redirect('/login?mode=' . $mode . ($next !== '' ? '&next=' . rawurlencode($next) : ''));
        }

        if ($mode === 'staff' && !in_array($user['role'], ['super_admin', 'admin'], true)) {
            RateLimiter::hit($mobile, $ip);
            flash('danger', 'That mobile number is not a staff account. Use the Donor tab to sign in.');
            remember_old(['mobile' => $mobile, 'mode' => 'donor', 'next' => $next]);
            redirect('/login?mode=donor');
        }

        RateLimiter::clear($mobile, $ip);
        Auth::login($user);
        clear_old();
        AuditLog::write((int) $user['id'], 'login.success', ['role' => $user['role']]);

        flash('success', 'Welcome back, ' . $user['name'] . ' 🙏');
        redirect($next !== '' ? safe_next($next, Auth::homeFor($user['role'])) : Auth::homeFor($user['role']));
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect(Auth::homeFor());
        }

        $next = (string) ($_GET['next'] ?? '');

        echo view('layouts.auth', [
            'content'   => view('auth.register', ['next' => $next]),
            'state'     => (new LeaderboardRepository())->snapshot(),
            'pageTitle' => 'Create account — Chanda Collection',
            'mode'      => 'donor',
        ]);
    }

    public function register(): void
    {
        Csrf::require();

        $name     = trim((string) ($_POST['name'] ?? ''));
        $mobile   = preg_replace('/\D+/', '', (string) ($_POST['mobile'] ?? '')) ?? '';
        $email    = trim((string) ($_POST['email'] ?? ''));
        $locality = trim((string) ($_POST['locality'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm  = (string) ($_POST['password_confirm'] ?? '');
        $next     = (string) ($_POST['next'] ?? '');

        $errors = [];

        if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 120) {
            $errors[] = 'Enter your full name (3–120 characters).';
        }
        if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
            $errors[] = 'Enter a valid 10-digit Indian mobile number.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address, or leave it blank.';
        }
        if (mb_strlen($locality, 'UTF-8') > 120) {
            $errors[] = 'Locality must be 120 characters or fewer.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        $users = new UserRepository();
        if ($mobile !== '' && $users->findByMobile($mobile)) {
            $errors[] = 'This mobile number is already registered. Try signing in instead.';
        }

        if ($errors) {
            flash('danger', implode(' ', $errors));
            remember_old([
                'name'     => $name,
                'mobile'   => $mobile,
                'email'    => $email,
                'locality' => $locality,
                'next'     => $next,
            ]);
            redirect('/register' . ($next !== '' ? '?next=' . rawurlencode($next) : ''));
        }

        try {
            $id = $users->createDonor([
                'name'     => $name,
                'mobile'   => $mobile,
                'email'    => $email,
                'password' => $password,
                'locality' => $locality,
            ]);
        } catch (\Throwable) {
            flash('danger', 'We could not create your account right now. Please try again.');
            redirect('/register');
        }

        $user = $users->find($id);
        if ($user) {
            Auth::login($user);
        }

        AuditLog::write($id, 'user.register', ['role' => 'bhaktpremi']);
        clear_old();
        flash('success', 'Your account is ready. Add your first chanda below. 🙏');

        redirect($next !== '' ? safe_next($next, '/donate') : '/donate');
    }

    public function logout(): void
    {
        Csrf::require();

        $user = Auth::user();
        if ($user) {
            AuditLog::write((int) $user['id'], 'logout');
        }

        Auth::logout();
        flash('success', 'You have been signed out.');

        redirect('/');
    }
}
