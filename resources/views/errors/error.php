<?php
/** @var int $code */
/** @var string $title */
/** @var string $message */
/** @var string $back */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b0614">
    <meta name="robots" content="noindex">
    <title><?= e($code) ?> — <?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
</head>
<body class="page-body">
    <div class="aurora" aria-hidden="true"></div>

    <main class="error-page">
        <div class="error-card">
            <span class="error-code"><?= e((string) $code) ?></span>
            <h1><?= e($title) ?></h1>
            <p><?= e($message) ?></p>
            <div class="error-actions">
                <a href="<?= e($back) ?>" class="btn btn--primary">Go back</a>
                <a href="<?= e(url('/')) ?>" class="btn btn--ghost">Honour board</a>
            </div>
        </div>
    </main>
</body>
</html>
