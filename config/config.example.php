<?php

/**
 * Copy this file to config/config.php and fill in real values.
 * config/config.php is git-ignored and must live OUTSIDE the public web root.
 */
return [
    'app' => [
        'name'      => 'Chanda Collection',
        'env'       => 'local',          // local | production
        'debug'     => true,
        'timezone'  => 'Asia/Kolkata',
        'url'       => '',               // e.g. https://chanda.example.com (empty = auto-detect)
        'demo_mode' => true,             // true = show sample leaderboard when DB has no data
    ],

    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'cca',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'security' => [
        'session_name'     => 'cca_session',
        'pin_session_ttl'  => 600,       // 10 minutes
        'pin_max_attempts' => 5,
        'pin_lock_minutes' => 15,
        'login_max_attempts' => 5,
        'login_lock_minutes' => 15,
        'upload_max_bytes' => 2 * 1024 * 1024,
        'upload_mimes'     => ['image/jpeg', 'image/png', 'image/webp'],
    ],

    'payment' => [
        'gateway_enabled' => false,      // Phase 2: Razorpay etc.
        'upi_id'          => '',
    ],
];
