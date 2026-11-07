<?php
/** @var string $next */
?>
<h1 class="auth-title">Create your donor account</h1>
<p class="auth-subtitle">
    Track every contribution, receive acknowledgements and appear on the honour board
    (only if you choose to be visible).
</p>

<form action="<?= e(url('/register')) ?>" method="post" class="auth-form" autocomplete="on">
    <?= csrf_field() ?>
    <input type="hidden" name="next" value="<?= e($next) ?>">

    <div class="field">
        <label class="field-label" for="regName">Full name</label>
        <input class="input" id="regName" name="name" type="text" required maxlength="120"
               autocomplete="name" placeholder="e.g. Ramesh Sah" value="<?= e(old('name')) ?>">
    </div>

    <div class="auth-grid-2">
        <div class="field">
            <label class="field-label" for="regMobile">Mobile number</label>
            <div class="field-control">
                <span class="field-prefix" aria-hidden="true">+91</span>
                <input class="input input--with-prefix" id="regMobile" name="mobile" type="tel"
                       inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required
                       autocomplete="tel-national" placeholder="10-digit mobile"
                       value="<?= e(old('mobile')) ?>">
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="regLocality">Locality <span class="field-optional">optional</span></label>
            <input class="input" id="regLocality" name="locality" type="text" maxlength="120"
                   autocomplete="address-level2" placeholder="e.g. Kankarbagh, Patna"
                   value="<?= e(old('locality')) ?>">
        </div>
    </div>

    <div class="field">
        <label class="field-label" for="regEmail">Email <span class="field-optional">optional</span></label>
        <input class="input" id="regEmail" name="email" type="email" maxlength="190"
               autocomplete="email" placeholder="you@example.com" value="<?= e(old('email')) ?>">
        <p class="field-help">Used only for account recovery. Never shown on the public board.</p>
    </div>

    <div class="auth-grid-2">
        <div class="field">
            <label class="field-label" for="regPassword">Password</label>
            <div class="field-control">
                <input class="input input--with-action" id="regPassword" name="password" type="password"
                       required minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
                <button type="button" class="field-action" data-password-toggle="regPassword" aria-label="Show password">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.7"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="field">
            <label class="field-label" for="regPasswordConfirm">Confirm password</label>
            <div class="field-control">
                <input class="input input--with-action" id="regPasswordConfirm" name="password_confirm" type="password"
                       required minlength="8" autocomplete="new-password" placeholder="Repeat password">
                <button type="button" class="field-action" data-password-toggle="regPasswordConfirm" aria-label="Show password">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.7"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <label class="check">
        <input type="checkbox" required>
        <span>I understand that contributions count only after committee approval and that receipts are acknowledgements, not tax documents.</span>
    </label>

    <button type="submit" class="btn btn--primary btn--block btn--lg">
        Create account &amp; donate
    </button>
</form>

<p class="auth-switch">
    Already registered? <a href="<?= e(url('/login?mode=donor' . ($next !== '' ? '&next=' . rawurlencode($next) : ''))) ?>">Sign in instead</a>
</p>
