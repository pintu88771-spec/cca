<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Settings;
use App\Repositories\DonationRepository;
use App\Repositories\LeaderboardRepository;
use App\Support\ImageUpload;

final class DonationController
{
    private const PRESETS = [501, 1101, 2101, 5101, 11000, 21000];

    public function show(): void
    {
        Auth::requireLogin('/donate');

        $festival = (new DonationRepository())->activeFestival();
        if (!$festival) {
            http_response_code(503);
            echo view('layouts.app', [
                'content'   => view('errors.inline', [
                    'title'   => 'No active festival',
                    'message' => 'The committee has not opened a festival for collection yet. Please check back soon.',
                ]),
                'pageTitle' => 'Donate — Chanda Collection',
                'active'    => 'donate',
            ]);
            return;
        }

        $state = (new LeaderboardRepository())->snapshot();
        $stats = (new DonationRepository())->statsForDonor(Auth::id());

        $rawUpi = (string) (Settings::get('upi_id', '') ?? '');
        if ($rawUpi === '') {
            $rawUpi = (string) config('payment.upi_id', 'chhathsamiti@upi');
        }

        echo view('layouts.app', [
            'content'   => view('donor.donate', [
                'festival' => $festival,
                'state'    => $state,
                'stats'    => $stats,
                'presets'  => self::PRESETS,
                'upiId'    => $rawUpi,
            ]),
            'pageTitle' => 'Donate — Chanda Collection',
            'active'    => 'donate',
            'festival'  => $festival,
        ]);
    }

    public function store(): void
    {
        Csrf::require();
        Auth::requireLogin('/donate');

        $repo = new DonationRepository();
        $festival = $repo->activeFestival();

        if (!$festival) {
            flash('danger', 'No active festival is accepting contributions right now.');
            redirect('/');
        }

        $amountRaw = str_replace([',', ' ', '₹'], '', (string) ($_POST['amount'] ?? ''));
        $amount = round((float) $amountRaw, 2);
        $utr = strtoupper(trim((string) ($_POST['utr'] ?? '')));
        $anonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1';

        $errors = [];

        if (!is_numeric($amountRaw) || $amount < 1) {
            $errors[] = 'Enter a contribution amount of at least ₹1.';
        }
        if ($amount > 1000000) {
            $errors[] = 'For contributions above ₹10,00,000, please contact the committee directly.';
        }
        if (!preg_match('/^[A-Z0-9][A-Z0-9\-\/_]{5,63}$/', $utr)) {
            $errors[] = 'Enter a valid UPI reference / UTR number (6–64 letters or digits).';
        }
        if ($utr !== '' && $repo->utrExists($utr)) {
            $errors[] = 'That UTR is already submitted. Check your UPI app for the correct reference.';
        }

        if ($errors) {
            flash('danger', implode(' ', $errors));
            remember_old([
                'amount'       => $amountRaw,
                'utr'          => $utr,
                'is_anonymous' => $anonymous ? '1' : '0',
            ]);
            redirect('/donate');
        }

        $screenshot = null;
        try {
            $screenshot = ImageUpload::store($_FILES['screenshot'] ?? null, 'donations');
        } catch (\RuntimeException $e) {
            flash('danger', $e->getMessage());
            remember_old([
                'amount'       => $amountRaw,
                'utr'          => $utr,
                'is_anonymous' => $anonymous ? '1' : '0',
            ]);
            redirect('/donate');
        }

        try {
            $id = $repo->create(Auth::id(), (int) $festival['id'], [
                'amount'       => $amount,
                'mode'         => 'upi',
                'utr'          => $utr,
                'screenshot'   => $screenshot,
                'is_anonymous' => $anonymous,
            ]);
        } catch (\Throwable) {
            ImageUpload::delete($screenshot);
            flash('danger', 'We could not record your contribution. Please try again.');
            redirect('/donate');
        }

        clear_old();
        AuditLog::write(Auth::id(), 'donation.create', [
            'donation_id' => $id,
            'amount'      => $amount,
            'utr'         => $utr,
        ]);

        flash('success', inr($amount) . ' submitted for approval. Your receipt appears here once an admin verifies the UTR.');
        redirect('/profile');
    }
}
