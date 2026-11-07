<?php

declare(strict_types=1);

/**
 * Application bootstrap.
 * Loads configuration, autoloader, helpers and defines BASE_PATH.
 */

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/app/Core/Config.php';
require ROOT_PATH . '/app/Support/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = ROOT_PATH . '/app/' . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$configFile = ROOT_PATH . '/config/config.php';
$config = is_file($configFile)
    ? require $configFile
    : require ROOT_PATH . '/config/config.example.php';

\App\Core\Config::setAll($config);

date_default_timezone_set((string) \App\Core\Config::get('app.timezone', 'Asia/Kolkata'));

// Sessions power CSRF, flash messages, login and rate-limit state.
\App\Core\Session::start();

// BASE_PATH = folder the app is served from, e.g. "" or "/cca/public".
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (str_ends_with($basePath, '/api')) {
    $basePath = substr($basePath, 0, -4);
}
if ($basePath === '.' || $basePath === '/') {
    $basePath = '';
}
define('BASE_PATH', $basePath);

if ((bool) \App\Core\Config::get('app.debug', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}
