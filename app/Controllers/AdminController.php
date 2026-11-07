<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\Csrf;
use App\Repositories\DonationRepository;

final class AdminController
{
    public function dashboard(): void
    {
        Auth::requireRole(['super_admin', 'admin'], '/dashboard');

        $repo = new DonationRepository();
        $festival = $repo->activeFestival();
        $stats = $repo->dashboardStats();

        echo view('layouts.app', [
            'content'   => view('admin.dashboard', [
                'festival' => $festival,
                'stats'    => $stats,
                'pending'  => $repo->pending(),
                'recent'   => $repo->recentApproved(10),
            ]),
            'pageTitle' => 'Approvals — Chanda Collection',
            'active'    => 'dashboard',
            'festival'  => $festival,
        ]);
    }

    public function action(): void
    {
        Csrf::require();
        Auth::requireRole(['super_admin', 'admin'], '/dashboard');

        $id = (int) ($_POST['donation_id'] ?? 0);
        $action = (string) ($_POST['action'] ?? '');
        $reason = trim((string) ($_POST['reason'] ?? ''));

        if ($id <= 0 || !in_array($action, ['approve', 'reject', 'cancel'], true)) {
            flash('danger', 'Invalid approval action.');
            redirect('/dashboard');
        }

        if (in_array($action, ['reject', 'cancel'], true) && mb_strlen($reason, 'UTF-8') < 3) {
            flash('danger', 'A reason is required to ' . $action . ' a contribution.');
            redirect('/dashboard');
        }

        if (mb_strlen($reason, 'UTF-8') > 255) {
            $reason = mb_substr($reason, 0, 255, 'UTF-8');
        }

        $repo = new DonationRepository();
        $donation = $repo->find($id);
        if (!$donation) {
            flash('danger', 'That contribution could not be found.');
            redirect('/dashboard');
        }

        if ($action === 'approve') {
            $result = $repo->approve($id, Auth::id());
            if ($result['ok']) {
                AuditLog::write(Auth::id(), 'donation.approve', [
                    'donation_id' => $id,
                    'donor'       => $donation['donor_name'],
                    'amount'      => (float) $donation['amount'],
                    'receipt'     => $result['receipt'],
                ]);
                flash('success', 'Approved ' . inr((float) $donation['amount']) . ' for ' . $donation['donor_name']
                    . '. Receipt ' . $result['receipt'] . ' issued.');
            } else {
                flash('danger', (string) $result['error']);
            }

            redirect('/dashboard');
        }

        $result = $action === 'reject'
            ? $repo->reject($id, Auth::id(), $reason)
            : $repo->cancel($id, Auth::id(), $reason);

        if ($result['ok']) {
            AuditLog::write(Auth::id(), 'donation.' . $action, [
                'donation_id' => $id,
                'donor'       => $donation['donor_name'],
                'amount'      => (float) $donation['amount'],
                'reason'      => $reason,
            ]);
            flash('success', ($action === 'reject' ? 'Rejected ' : 'Cancelled ') . inr((float) $donation['amount']) . ' for ' . $donation['donor_name'] . '.');
        } else {
            flash('danger', (string) $result['error']);
        }

        redirect('/dashboard');
    }
}
