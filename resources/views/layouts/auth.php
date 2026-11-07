<?php
/** @var string $content */
/** @var string $pageTitle */
/** @var array $state */
$festival = $state['festival'] ?? [];
$committee = (string) ($festival['committee'] ?? '');
$total = (float) ($festival['total'] ?? 0);
$target = (float) ($festival['target'] ?? 0);
$percent = min(100, (float) ($festival['percent'] ?? 0));
$days = (int) ($festival['days_left'] ?? 0);
$donors = (int) ($festival['donors_count'] ?? 0);
$percentLabel = rtrim(rtrim(number_format($percent, 1, '.', ''), '0'), '.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b0614">
    <meta name="robots" content="noindex">
    <title><?= e($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
</head>
<body class="page-body auth-body">
    <div class="aurora" aria-hidden="true"></div>

    <main class="auth-shell">
        <aside class="auth-aside">
            <a href="<?= e(url('/')) ?>" class="auth-brand">
                <span class="logo-mark logo-mark--lg" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                        <circle cx="12" cy="12" r="4" fill="currentColor"/>
                        <g stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/>
                        </g>
                    </svg>
                </span>
                <span>
                    <b>Chanda Collection</b>
                    <small><?= e((string) ($festival['name'] ?? 'Chhath Puja 2026')) ?></small>
                </span>
            </a>

            <div class="auth-aside__copy">
                <p class="auth-kicker"><span class="live-dot" aria-hidden="true"></span> Live honour board</p>
                <h1>Every chanda becomes <span class="text-gradient">community light.</span></h1>
                <p class="auth-lede">
                    <?= e($committee) ?> is collecting for Chhath Puja with complete transparency.
                    Only admin-approved contributions are counted and shown publicly.
                </p>
            </div>

            <div class="auth-stats">
                <div class="auth-stat">
                    <span class="label">Collected</span>
                    <b class="text-gradient"><?= e(inr($total)) ?></b>
                </div>
                <div class="auth-stat">
                    <span class="label">Donors</span>
                    <b><?= e((string) $donors) ?></b>
                </div>
                <div class="auth-stat">
                    <span class="label">Days left</span>
                    <b><?= e((string) $days) ?></b>
                </div>
            </div>

            <div class="auth-progress">
                <div class="flex items-center justify-between gap-2">
                    <span class="label">Target <?= e(inr($target)) ?></span>
                    <span class="auth-progress__pct"><?= e($percentLabel) ?>%</span>
                </div>
                <div class="progress-track mt-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= e((string) $percent) ?>">
                    <div class="progress-fill" style="width: <?= e((string) $percent) ?>%"></div>
                </div>
            </div>

            <ul class="auth-points">
                <li><span aria-hidden="true">✓</span> UPI contributions verified by an admin</li>
                <li><span aria-hidden="true">✓</span> Acknowledgement receipt for every approval</li>
                <li><span aria-hidden="true">✓</span> Anonymous donors stay private, still counted</li>
            </ul>

            <a href="<?= e(url('/')) ?>" class="auth-back">← Back to honour board</a>
        </aside>

        <section class="auth-main">
            <div class="auth-card">
                <?= view('partials.flashes') ?>
                <?= $content ?>
            </div>
            <p class="auth-foot">
                <?= e($committee) ?> · Secure sign-in with CSRF protection and attempt limiting.
            </p>
        </section>
    </main>

    <script src="<?= e(asset('assets/js/pages.js')) ?>" defer></script>
</body>
</html>
