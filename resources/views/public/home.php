<?php
/** @var array $state */
/** @var array $leaders */
$festival = $state['festival'];
$leaders = $leaders ?? [];
$hero = $leaders[0] ?? [
    'name' => 'Be the first donor',
    'locality' => 'Your name belongs here',
    'amount' => 0,
    'message' => 'हर योगदान मायने रखता है 🙏',
    'badge' => 'platinum',
];
$second = $leaders[1] ?? null;
$third = $leaders[2] ?? null;
$preview = array_slice($leaders, 0, 5);

$percent = min(100, (float) $festival['percent']);
$percentLabel = rtrim(rtrim(number_format($percent, 1, '.', ''), '0'), '.');
$topDonors = array_slice($leaders, 0, 50);
?>
<div class="app-shell">

    <!-- ================= Top bar ================= -->
    <header class="topbar">
        <div class="brand-block">
            <div class="logo-mark logo-mark--lg" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                    <circle cx="12" cy="12" r="4" fill="currentColor"/>
                    <g stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/>
                    </g>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="topbar-kicker"><?= e($festival['name']) ?></p>
                <h1 class="brand-title" data-i18n="brand">Chanda Collection</h1>
                <p class="brand-sub"><?= e($festival['committee']) ?></p>
            </div>
        </div>

        <div class="header-meta">
            <span class="live-pill">
                <span class="live-dot" aria-hidden="true"></span>
                <span data-i18n="liveUpdates">Live updates</span>
            </span>
            <span class="countdown-pill">
                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                    <circle cx="12" cy="13" r="8" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 9v4l2.5 1.5M9 2h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span id="daysLeft"><?= e((string) $festival['days_left']) ?></span>
                <span data-i18n="daysToGo">days to go</span>
            </span>
        </div>

        <div class="header-actions">
            <div class="lang-toggle shrink-0" role="group" aria-label="Language">
                <button type="button" data-lang-btn="en" class="is-active" aria-pressed="true">EN</button>
                <button type="button" data-lang-btn="hi" aria-pressed="false">हिं</button>
            </div>

            <a id="donateBtn" href="<?= e(url('/donate')) ?>" class="btn-primary header-donate">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M12 20s-7-4.4-7-9.2A4.1 4.1 0 0 1 12 7a4.1 4.1 0 0 1 7 3.8C19 15.6 12 20 12 20Z" fill="currentColor"/>
                </svg>
                <span data-i18n="donate">Donate</span>
            </a>
        </div>
    </header>

    <!-- ================= Board ================= -->
    <main class="board">

        <!-- Stats -->
        <section class="stats-panel glass" aria-label="Collection progress">
            <div>
                <p class="label flex items-center gap-1.5">
                    <span class="live-dot" aria-hidden="true"></span>
                    <span data-i18n="total">Total collected</span>
                </p>
                <p id="totalAmount" class="stat-total text-gradient"><?= e(inr($festival['total'])) ?></p>
                <p class="stat-caption">
                    <span id="donorCount"><?= e((string) $festival['donors_count']) ?></span>
                    <span data-i18n="donorsWord">donors</span>
                    <span class="mx-1 text-cream/25">•</span>
                    <span data-i18n="approvedShort">approved</span>
                </p>
            </div>

            <div>
                <div class="flex items-end justify-between gap-2">
                    <span class="label" data-i18n="target">Target</span>
                    <span id="targetAmount" class="text-[13px] font-semibold text-cream/75"><?= e(inr($festival['target'])) ?></span>
                </div>
                <div class="progress-track mt-2" id="progressTrack" role="progressbar"
                     aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= e((string) $percent) ?>"
                     aria-label="Collection progress">
                    <div id="progressFill" class="progress-fill" style="width: <?= e((string) $percent) ?>%"></div>
                </div>
                <div class="mt-2 flex items-center justify-between gap-2">
                    <p class="text-[11px] text-cream/55">
                        <b id="percentValue" class="font-semibold text-gold"><?= e($percentLabel) ?>%</b>
                        <span data-i18n="complete">complete</span>
                    </p>
                    <span class="countdown-pill mobile-only">
                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" aria-hidden="true">
                            <circle cx="12" cy="13" r="8" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M12 9v4l2.5 1.5M9 2h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span id="daysLeftMobile"><?= e((string) $festival['days_left']) ?></span>
                        <span data-i18n="daysShort">days</span>
                    </span>
                </div>
            </div>

            <div class="stat-mini-grid">
                <div class="mini-stat">
                    <span class="label" data-i18n="daysLeftLabel">Days to go</span>
                    <b id="daysLeftStat"><?= e((string) $festival['days_left']) ?></b>
                </div>
                <div class="mini-stat">
                    <span class="label" data-i18n="leaderLabel">Current leader</span>
                    <b id="leaderShort" class="truncate"><?= e($hero['name']) ?></b>
                </div>
            </div>

            <div class="stat-remaining">
                <span class="label" data-i18n="remainingLabel">Remaining to target</span>
                <b id="remainingAmount"><?= e(inr(max(0, (float) $festival['target'] - (float) $festival['total']))) ?></b>
            </div>

            <div class="stats-cta">
                <p class="text-[12.5px] font-semibold leading-snug" data-i18n="ctaTitle">Be part of this year's celebration</p>
                <a href="<?= e(url('/donate')) ?>" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M12 20s-7-4.4-7-9.2A4.1 4.1 0 0 1 12 7a4.1 4.1 0 0 1 7 3.8C19 15.6 12 20 12 20Z" fill="currentColor"/>
                    </svg>
                    <span data-i18n="donateNow">Donate now</span>
                </a>
                <p class="text-[9.5px] leading-relaxed text-cream/40" data-i18n="ctaNote">Every contribution is recorded and acknowledged.</p>
            </div>

            <p class="stats-note">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 shrink-0 text-mint/80" aria-hidden="true">
                    <path d="M12 3 5 6v5.2c0 4.4 2.9 8.1 7 9.8 4.1-1.7 7-5.4 7-9.8V6l-7-3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span data-i18n="approvedNote">Only admin-approved contributions are counted.</span>
            </p>
        </section>

        <!-- Rank #1 + runners -->
        <section class="hero-zone">
            <section id="heroCard" class="hero-card" aria-label="Top donor">
                <span class="hero-watermark" aria-hidden="true">01</span>
                <div class="hero-inner">
                    <span class="rank-chip">
                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                            <path d="M4 8.5 7.5 11 12 5l4.5 6L20 8.5l-1.6 8.5H5.6L4 8.5Z" fill="currentColor" opacity=".9"/>
                            <path d="M5.6 17h12.8v2.2H5.6z" fill="currentColor" opacity=".65"/>
                        </svg>
                        <span data-i18n="topDonor">#1 Top Donor</span>
                    </span>

                    <div class="mt-4 flex flex-col items-center">
                        <div class="avatar-ring">
                            <div id="heroAvatar" class="avatar avatar--xl"
                                 data-tone="<?= e((string) avatar_tone((string) $hero['name'])) ?>"
                                 aria-hidden="true"><?= e(initials((string) $hero['name'])) ?></div>
                            <span class="crown" aria-hidden="true">👑</span>
                        </div>

                        <h2 id="heroName" class="mt-4 max-w-full truncate"><?= e($hero['name']) ?></h2>
                        <p id="heroLocality" class="hero-locality"><?= e($hero['locality']) ?></p>
                        <p id="heroAmount" class="hero-amount text-gradient"><?= e(inr($hero['amount'])) ?></p>
                        <p id="heroMessage" class="hero-message">
                            <?= e($hero['message'] !== '' ? $hero['message'] : 'हर योगदान मायने रखता है 🙏') ?>
                        </p>
                    </div>
                </div>
                <div class="shine" aria-hidden="true"></div>
            </section>

            <div class="runner-grid">
                <article id="r2Card" class="runner-card is-faded" aria-label="Rank 2 donor">
                    <div class="flex items-center gap-2.5">
                        <span class="runner-rank" data-rank="2">2</span>
                        <div id="r2Avatar" class="avatar avatar--md" data-tone="<?= e((string) avatar_tone((string) ($second['name'] ?? 'Second'))) ?>" aria-hidden="true">
                            <?= e(initials((string) ($second['name'] ?? '—'))) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p id="r2Name" class="truncate text-[13px] font-semibold leading-tight"><?= e($second['name'] ?? '—') ?></p>
                            <p id="r2Locality" class="truncate text-[10px] leading-tight text-cream/45"><?= e($second['locality'] ?? '') ?></p>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span id="r2Badge" class="badge badge--<?= e($second['badge'] ?? 'silver') ?>"><?= e(\App\Support\Badge::label($second['badge'] ?? 'silver')) ?></span>
                        <span id="r2Amount" class="text-[15px] font-bold text-gold"><?= e(inr($second['amount'] ?? 0)) ?></span>
                    </div>
                </article>

                <article id="r3Card" class="runner-card is-faded" aria-label="Rank 3 donor">
                    <div class="flex items-center gap-2.5">
                        <span class="runner-rank" data-rank="3">3</span>
                        <div id="r3Avatar" class="avatar avatar--md" data-tone="<?= e((string) avatar_tone((string) ($third['name'] ?? 'Third'))) ?>" aria-hidden="true">
                            <?= e(initials((string) ($third['name'] ?? '—'))) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p id="r3Name" class="truncate text-[13px] font-semibold leading-tight"><?= e($third['name'] ?? '—') ?></p>
                            <p id="r3Locality" class="truncate text-[10px] leading-tight text-cream/45"><?= e($third['locality'] ?? '') ?></p>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span id="r3Badge" class="badge badge--<?= e($third['badge'] ?? 'bronze') ?>"><?= e(\App\Support\Badge::label($third['badge'] ?? 'bronze')) ?></span>
                        <span id="r3Amount" class="text-[15px] font-bold text-gold"><?= e(inr($third['amount'] ?? 0)) ?></span>
                    </div>
                </article>
            </div>
        </section>

        <!-- Desktop side panel -->
        <aside class="side-panel">
            <section class="glass side-card top-preview-card">
                <div class="side-head">
                    <div>
                        <p class="label flex items-center gap-1.5">
                            <span class="live-dot" aria-hidden="true"></span>
                            <span data-i18n="live">Live leaderboard</span>
                        </p>
                        <h2 class="side-title" data-i18n="topDonors">Top donors</h2>
                    </div>
                    <span class="badge badge--platinum">TOP 5</span>
                </div>

                <div id="topPreview" class="top-preview">
                    <?php foreach ($preview as $donor): ?>
                        <div class="preview-row">
                            <span class="donor-rank <?= $donor['rank'] <= 3 ? 'is-top' : '' ?>"><?= e((string) $donor['rank']) ?></span>
                            <div class="avatar avatar--sm" data-tone="<?= e((string) avatar_tone((string) $donor['name'])) ?>" aria-hidden="true">
                                <?= e(initials((string) $donor['name'])) ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[12px] font-semibold leading-tight"><?= e($donor['name']) ?></p>
                                <p class="truncate text-[9.5px] leading-tight text-cream/45"><?= e($donor['locality']) ?></p>
                            </div>
                            <span class="text-[12px] font-bold text-gold"><?= e(inr($donor['amount'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button id="openTop50" type="button" class="btn-ghost mt-auto w-full">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M8 20h8M12 16v4M7 4h10v4a5 5 0 0 1-10 0V4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    <span data-i18n="viewTop50">View top 50</span>
                </button>
            </section>

            <section class="glass side-card trust-card">
                <div class="trust-icon">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                        <path d="M12 3 5 6v5.2c0 4.4 2.9 8.1 7 9.8 4.1-1.7 7-5.4 7-9.8V6l-7-3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="text-[12.5px] font-semibold leading-snug" data-i18n="approvedNote">Only admin-approved contributions are counted.</p>
                <p class="mt-1 text-[10.5px] leading-relaxed text-cream/50"><?= e($festival['committee']) ?></p>
                <p class="mt-2 text-[10px] leading-relaxed text-cream/40" data-i18n="anonymousNote">
                    Anonymous donors are counted in the total but never shown publicly.
                </p>
            </section>
        </aside>
    </main>

    <!-- ================= Bottom dock ================= -->
    <footer class="dock-wrap">
        <div class="mobile-donate">
            <a href="<?= e(url('/donate')) ?>" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M12 20s-7-4.4-7-9.2A4.1 4.1 0 0 1 12 7a4.1 4.1 0 0 1 7 3.8C19 15.6 12 20 12 20Z" fill="currentColor"/>
                </svg>
                <span data-i18n="donate">Donate</span>
            </a>
        </div>

        <nav class="dock" aria-label="Main navigation">
            <a href="<?= e(url('/login?mode=staff')) ?>" class="dock-btn" aria-label="Staff login">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="10" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M8 10V7.5a4 4 0 0 1 8 0V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="15" r="1.2" fill="currentColor"/>
                </svg>
                <span data-i18n="staff">Staff</span>
            </a>

            <a href="<?= e(url('/login?mode=donor')) ?>" class="dock-btn" aria-label="Account login">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M5 20c.7-3.6 3.5-5.5 7-5.5s6.3 1.9 7 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span data-i18n="account">Account</span>
            </a>

            <button id="donorsBtn" type="button" class="dock-btn" aria-label="Top donors" aria-haspopup="dialog" aria-controls="donorSheet">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M8 20h8M12 16v4M7 4h10v4a5 5 0 0 1-10 0V4Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 6H4.5v2A3.5 3.5 0 0 0 8 11.5M17 6h2.5v2A3.5 3.5 0 0 1 16 11.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span data-i18n="donors">Donors</span>
            </button>
        </nav>

        <div class="dock-status">
            <span class="live-dot" aria-hidden="true"></span>
            <span data-i18n="autoRefresh">Updates every 8 seconds</span>
        </div>
    </footer>
</div>

<!-- ================= Top 50 sheet / drawer ================= -->
<div id="donorSheet" class="sheet-root" aria-hidden="true">
    <div class="sheet-backdrop" data-close-sheet></div>

    <section class="sheet-panel" role="dialog" aria-modal="true" aria-labelledby="sheetTitle" tabindex="-1">
        <div class="sheet-handle" aria-hidden="true"></div>

        <div class="flex items-start justify-between gap-3 px-4">
            <div>
                <p class="label flex items-center gap-1.5">
                    <span class="live-dot" aria-hidden="true"></span>
                    <span data-i18n="live">Live leaderboard</span>
                </p>
                <h2 id="sheetTitle" class="mt-1 text-[19px] font-bold tracking-tight" data-i18n="top50">Top 50 Donors</h2>
                <p class="mt-0.5 text-[10.5px] text-cream/45" data-i18n="top50Hint">Ranked by total approved contribution</p>
            </div>
            <button type="button" class="icon-btn" data-close-sheet aria-label="Close donors list">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4">
                    <path d="m7 7 10 10M17 7 7 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="relative mt-3 px-4">
            <svg viewBox="0 0 24 24" fill="none" class="pointer-events-none absolute left-[27px] top-1/2 h-4 w-4 -translate-y-1/2 text-cream/35" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/>
                <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <input id="donorSearch" type="search" class="search-input" placeholder="Search donors" data-i18n-placeholder="search" autocomplete="off">
        </div>

        <div class="mt-2 min-h-0 flex-1 overflow-y-auto px-2 pb-6 no-scrollbar">
            <div id="donorList">
                <?php foreach ($topDonors as $donor): ?>
                    <div class="donor-row" data-donor-name="<?= e(mb_strtolower($donor['name'], 'UTF-8')) ?>">
                        <span class="donor-rank <?= $donor['rank'] <= 3 ? 'is-top' : '' ?>"><?= e((string) $donor['rank']) ?></span>
                        <div class="avatar avatar--sm" data-tone="<?= e((string) avatar_tone((string) $donor['name'])) ?>" aria-hidden="true">
                            <?= e(initials((string) $donor['name'])) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[12.5px] font-semibold leading-tight"><?= e($donor['name']) ?></p>
                            <p class="truncate text-[10px] leading-tight text-cream/45"><?= e($donor['locality']) ?></p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="badge badge--<?= e($donor['badge']) ?>"><?= e(\App\Support\Badge::label($donor['badge'])) ?></span>
                            <p class="mt-0.5 text-[12px] font-bold leading-tight text-gold"><?= e(inr($donor['amount'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$topDonors): ?>
                    <p class="px-4 py-10 text-center text-[12px] text-cream/45" data-i18n="noDonors">
                        No approved donations yet. Be the first!
                    </p>
                <?php endif; ?>
            </div>

            <p id="noResults" class="hidden px-4 py-10 text-center text-[12px] text-cream/45" data-i18n="noResults">
                No donor matches your search.
            </p>
        </div>
    </section>
</div>
