<?php
/** @var string $content */
/** @var string $pageTitle */
/** @var string $active */
/** @var array|null $festival */
$user = current_user();
$active = $active ?? '';
$festivalName = (string) ($festival['name'] ?? \App\Core\Settings::get('festival_title', 'Chhath Puja 2026'));
$committee = (string) (\App\Core\Settings::get('committee_name', 'Shri Chhath Puja Samiti, Patna') ?? '');

$navItems = [
    ['key' => 'board', 'href' => '/', 'label' => 'Honour board'],
    ['key' => 'donate', 'href' => '/donate', 'label' => 'Donate'],
    ['key' => 'profile', 'href' => '/profile', 'label' => 'My chanda'],
];

if (is_staff()) {
    $navItems[] = ['key' => 'dashboard', 'href' => '/dashboard', 'label' => 'Approvals'];
}
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
<body class="page-body" data-page="<?= e($active) ?>">
    <div class="aurora" aria-hidden="true"></div>

    <div class="page-shell">
        <header class="page-topbar">
            <a href="<?= e(url('/')) ?>" class="page-brand">
                <span class="logo-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5">
                        <circle cx="12" cy="12" r="4" fill="currentColor"/>
                        <g stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/>
                        </g>
                    </svg>
                </span>
                <span class="page-brand__text">
                    <b>Chanda Collection</b>
                    <small><?= e($festivalName) ?></small>
                </span>
            </a>

            <nav class="page-nav" aria-label="Account navigation">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= e(url($item['href'])) ?>"
                       class="page-nav__link <?= $active === $item['key'] ? 'is-active' : '' ?>"
                       <?= $active === $item['key'] ? 'aria-current="page"' : '' ?>>
                        <?= e($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php if ($user): ?>
                <div class="page-user">
                    <span class="avatar avatar--sm" data-tone="<?= e((string) avatar_tone((string) $user['name'])) ?>" aria-hidden="true">
                        <?= e(initials((string) $user['name'])) ?>
                    </span>
                    <span class="page-user__meta">
                        <b><?= e($user['name']) ?></b>
                        <small><?= e(role_label((string) $user['role'])) ?> · <?= e(mask_mobile((string) $user['mobile'])) ?></small>
                    </span>
                    <form action="<?= e(url('/logout')) ?>" method="post" class="page-user__logout">
                        <?= csrf_field() ?>
                        <button type="submit" class="icon-btn" aria-label="Sign out" title="Sign out">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4">
                                <path d="M15 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M18 12H10m8 0-3-3m3 3-3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </header>

        <main class="page-main">
            <?= view('partials.flashes') ?>
            <?= $content ?>
        </main>

        <footer class="page-footer">
            <span><?= e($committee) ?></span>
            <span class="page-footer__dot" aria-hidden="true">·</span>
            <span>Only admin-approved contributions appear on the public board.</span>
            <a href="<?= e(url('/')) ?>">View board</a>
        </footer>
    </div>

    <script src="<?= e(asset('assets/js/pages.js')) ?>" defer></script>
</body>
</html>
