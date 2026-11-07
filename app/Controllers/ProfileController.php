<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Csrf;
use App\Repositories\DonationRepository;
use App\Repositories\UserRepository;

final class ProfileController
{
    public function show(): void
    {
        Auth::requireLogin('/profile');

        $user = (new UserRepository())->find(Auth::id());
        if (!$user) {
            Auth::logout();
            flash('danger', 'Your account could not be loaded. Please sign in again.');
            redirect('/login');
        }

        $donations = new DonationRepository();

        echo view('layouts.app', [
            'content'   => view('donor.profile', [
                'user'       => $user,
                'donations'  => $donations->forDonor((int) $user['id']),
                'stats'      => $donations->statsForDonor((int) $user['id']),
                'festival'   => $donations->activeFestival(),
            ]),
            'pageTitle' => 'My contributions — Chanda Collection',
            'active'    => 'profile',
        ]);
    }

    public function update(): void
    {
        Csrf::require();
        Auth::requireLogin('/profile');

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $locality = trim((string) ($_POST['locality'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        $showPublic = isset($_POST['show_public']);
        $anonymous = isset($_POST['is_anonymous_default']);

        $errors = [];
        if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 120) {
            $errors[] = 'Enter your full name (3–120 characters).';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address, or leave it blank.';
        }
        if (mb_strlen($locality, 'UTF-8') > 120) {
            $errors[] = 'Locality must be 120 characters or fewer.';
        }
        if (mb_strlen($message, 'UTF-8') > 280) {
            $errors[] = 'Your message must be 280 characters or fewer.';
        }

        if ($errors) {
            flash('danger', implode(' ', $errors));
            remember_old([
                'name'         => $name,
                'email'        => $email,
                'locality'     => $locality,
                'message'      => $message,
                'show_public'  => $showPublic ? '1' : '0',
                'is_anonymous_default' => $anonymous ? '1' : '0',
            ]);
            redirect('/profile');
        }

        (new UserRepository())->updateProfile(Auth::id(), [
            'name'                 => $name,
            'email'                => $email,
            'locality'             => $locality,
            'message'              => $message,
            'show_public'          => $showPublic,
            'is_anonymous_default' => $anonymous,
        ]);

        AuditLog::write(Auth::id(), 'profile.update', ['show_public' => $showPublic]);
        clear_old();
        flash('success', 'Profile updated.');
        redirect('/profile');
    }

    public function password(): void
    {
        Csrf::require();
        Auth::requireLogin('/profile');

        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');

        $users = new UserRepository();
        $user = $users->find(Auth::id());

        if (!$user || !password_verify($current, (string) $user['password_hash'])) {
            flash('danger', 'Your current password is incorrect.');
            redirect('/profile#security');
        }

        if (strlen($new) < 8) {
            flash('danger', 'The new password must be at least 8 characters.');
            redirect('/profile#security');
        }

        if ($new !== $confirm) {
            flash('danger', 'The new passwords do not match.');
            redirect('/profile#security');
        }

        $users->updatePassword(Auth::id(), $new);
        Auth::logout();
        AuditLog::write((int) $user['id'], 'profile.password');

        flash('success', 'Password changed. Please sign in again.');
        redirect('/login?mode=donor');
    }
}
