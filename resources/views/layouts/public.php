<?php
/** @var array $state */
/** @var string $content */
/** @var string $pageTitle */
$refreshMs = (int) (config('app.refresh_interval', 0) ?: 8000);
$festival = $state['festival'];
?>
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b0614">
    <meta name="description" content="<?= e($festival['name']) ?> — live chanda collection honour board. <?= e($festival['committee']) ?>">
    <title><?= e($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
</head>
<body
    data-api="<?= e(url('/api/home.php')) ?>"
    data-refresh="<?= e((string) $refreshMs) ?>"
>
    <div class="aurora" aria-hidden="true"></div>

    <?= $content ?>

    <div id="toast" class="toast" role="status" aria-live="polite">
        <span id="toastIcon">✨</span>
        <span id="toastText"></span>
    </div>

    <canvas id="confetti" class="confetti-canvas" aria-hidden="true"></canvas>

    <script type="application/json" id="initialState"><?= json_encode(
        $state,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) ?></script>
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</body>
</html>
