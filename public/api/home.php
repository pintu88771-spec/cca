<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');

try {
    $state = (new App\Repositories\LeaderboardRepository())->snapshot();
    echo json_encode($state, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load the leaderboard right now.']);
}
