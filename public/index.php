<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (BASE_PATH !== '' && str_starts_with($uri, BASE_PATH)) {
    $uri = substr($uri, strlen(BASE_PATH));
}

$uri = '/' . trim($uri, '/');
$route = $method . ' ' . $uri;

switch ($route) {
    case 'GET /':
    case 'GET /index.php':
        (new App\Controllers\HomeController())->index();
        break;

    /* ------------------------------------------------------------
     * Authentication
     * ---------------------------------------------------------- */
    case 'GET /login':
        (new App\Controllers\AuthController())->showLogin();
        break;

    case 'POST /login':
        (new App\Controllers\AuthController())->login();
        break;

    case 'GET /register':
        (new App\Controllers\AuthController())->showRegister();
        break;

    case 'POST /register':
        (new App\Controllers\AuthController())->register();
        break;

    case 'POST /logout':
        (new App\Controllers\AuthController())->logout();
        break;

    /* ------------------------------------------------------------
     * Donations
     * ---------------------------------------------------------- */
    case 'GET /donate':
        (new App\Controllers\DonationController())->show();
        break;

    case 'POST /donate':
        (new App\Controllers\DonationController())->store();
        break;

    case 'GET /receipt':
        (new App\Controllers\ReceiptController())->show();
        break;

    /* ------------------------------------------------------------
     * Donor account
     * ---------------------------------------------------------- */
    case 'GET /profile':
        (new App\Controllers\ProfileController())->show();
        break;

    case 'POST /profile':
        (new App\Controllers\ProfileController())->update();
        break;

    case 'POST /profile/password':
        (new App\Controllers\ProfileController())->password();
        break;

    /* ------------------------------------------------------------
     * Admin / approvals
     * ---------------------------------------------------------- */
    case 'GET /dashboard':
    case 'GET /admin':
        (new App\Controllers\AdminController())->dashboard();
        break;

    case 'POST /admin/donations/action':
        (new App\Controllers\AdminController())->action();
        break;

    default:
        http_response_code(404);
        echo view('errors.error', [
            'code'    => 404,
            'title'   => 'Page not found',
            'message' => 'The page you are looking for does not exist or has moved.',
            'back'    => url('/'),
        ]);
        break;
}
