<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\LeaderboardRepository;

final class HomeController
{
    public function index(): void
    {
        $state = (new LeaderboardRepository())->snapshot();
        $leaders = $state['leaders'];

        $content = view('public.home', [
            'state'   => $state,
            'leaders' => $leaders,
        ]);

        echo view('layouts.public', [
            'state'     => $state,
            'content'   => $content,
            'pageTitle' => $state['festival']['name'] . ' — Chanda Collection',
        ]);
    }
}
