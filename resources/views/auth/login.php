<?php
/** @var string $mode */
/** @var string $next */
$isStaff = $mode === 'staff';
$demo = (bool) config('app.demo_mode', false);
?>
<div class="auth-tabs" role="tablist" aria-label="Login type">
    <a href="<?= e(url('/login?mode=staff' . ($next !== '' ? '&next=' . rawurlencode($next) : ''))) ?>"
       class="auth-tab <?= $isStaff ? 'is-active' : '' ?>" role="tab" aria-selected="<?= $isStaff ? 'true' : 'false' ?>">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
            <rect x="4" y="10" width="16" height="10" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8 10V7.5a4 4 0 0 1 8 0V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        Staff
    </a>
    <a href="<?= e(url('/login?mode=donor' . ($next !== '' ? '&next=' . rawurlencode($next) : ''))) ?>"
       class="auth-tab <?= !$isStaff ? 'is-active' : '' ?>" role="tab" aria-selected="<?= !$isStaff ? 'true' : 'false' ?>">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
            <circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.8"/>
            <path d="M5 20c.7-3.6 3.5-5.5 7-5.5s6.3 1.9 7 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        Donor
    </a>
</div>

<h1 class="auth-title"><?= $isStaff ? 'Staff sign in' : 'Welcome back' ?></h1>
<p class="auth-subtitle">
    <?= $isStaff
        ? 'Super Admin and Admin accounts approve contributions, issue receipts and manage the board.'
        : 'Sign in to donate by UPI, track your contributions and download acknowledgements.' ?>
</p>

<form action="<?= e(url('/login')) ?>" method="post" class="auth-form" autocomplete="on">
    <?= csrf_field() ?>
    <input type="hidden" name="mode" value="<?= e($mode) ?>">
    <input type="hidden" name="next" value="<?= e($next) ?>">

    <div class="field">
        <label class="field-label" for="loginMobile">Mobile number</label>
        <div class="field-control">
            <span class="field-prefix" aria-hidden="true">+91</span>
            <input class="input input--with-prefix" id="loginMobile" name="mobile" type="tel"
                   inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required
                   autocomplete="username" placeholder="10-digit mobile"
                   value="<?= e(old('mobile')) ?>">
        </div>
    </div>

    <div class="field">
        <label class="field-label" for="loginPassword">Password</label>
        <div class="field-control">
            <input class="input input--with-action" id="loginPassword" name="password" type="password"
                   required autocomplete="current-password" placeholder="Your password">
            <button type="button" class="field-action" data-password-toggle="loginPassword" aria-label="Show password">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.7"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn--primary btn--block btn--lg">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
            <path d="M12 3 5 6v5.2c0 4.4 2.9 8.1 7 9.8 4.1-1.7 7-5.4 7-9.8V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Sign in securely
    </button>
</form>

<?php if (!$isStaff): ?>
    <p class="auth-switch">
        New to the samiti? <a href="<?= e(url('/register' . ($next !== '' ? '?next=' . rawurlencode($next) : ''))) ?>">Create a donor account</a>
    </p>
<?php else: ?>
    <p class="auth-switch auth-switch--muted">
        Staff accounts are created by the Super Admin. Contact the committee if you need access.
    </p>
<?php endif; ?>

<?php if ($demo): ?>
    <div class="demo-box">
        <p class="demo-box__title"><span aria-hidden="true">🧪</span> Demo credentials</p>
        <?php if ($isStaff): ?>
            <div class="demo-row">
                <span>Super Admin <code>9000000001</code></span>
                <button type="button" class="demo-use" data-fill-mobile="9000000001" data-fill-password="Chanda@123">Use</button>
            </div>
            <div class="demo-row">
                <span>Admin <code>9000000002</code></span>
                <button type="button" class="demo-use" data-fill-mobile="9000000002" data-fill-password="Chanda@123">Use</button>
            </div>
        <?php else: ?>
            <div class="demo-row">
                <span>Donor <code>9811000010</code></span>
                <button type="button" class="demo-use" data-fill-mobile="9811000010" data-fill-password="Chanda@123">Use</button>
            </div>
        <?php endif; ?>
        <p class="demo-box__foot">Password for all demo accounts: <code>Chanda@123</code></p>
    </div>
<?php endif; ?>
