<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\DonationRepository;

final class ReceiptController
{
    public function show(): void
    {
        Auth::requireLogin('/profile');

        $id = (int) ($_GET['id'] ?? 0);
        $repo = new DonationRepository();
        $donation = $repo->find($id);

        if (!$donation) {
            http_response_code(404);
            echo view('errors.error', [
                'code'    => 404,
                'title'   => 'Receipt not found',
                'message' => 'That contribution could not be found, or the link is no longer valid.',
                'back'    => url('/profile'),
            ]);
            return;
        }

        $isOwner = (int) $donation['donor_id'] === Auth::id();
        if (!$isOwner && !Auth::isStaff()) {
            http_response_code(403);
            echo view('errors.error', [
                'code'    => 403,
                'title'   => 'Access denied',
                'message' => 'This receipt belongs to another donor.',
                'back'    => url('/profile'),
            ]);
            return;
        }

        if ($donation['status'] !== 'approved') {
            flash('warning', 'A receipt becomes available once an admin approves the contribution.');
            redirect('/profile');
        }

        echo view('receipt.view', [
            'donation' => $donation,
            'committee' => (string) (\App\Core\Settings::get('committee_name', 'Shri Chhath Puja Samiti') ?? ''),
            'festival'  => $repo->activeFestival(),
        ]);
    }
}
