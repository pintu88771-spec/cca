<?php
/** @var string $title */
/** @var string $message */
?>
<section class="card empty-state">
    <div class="empty-state__icon" aria-hidden="true">🙏</div>
    <h1 class="empty-state__title"><?= e($title) ?></h1>
    <p class="empty-state__text"><?= e($message) ?></p>
    <a href="<?= e(url('/')) ?>" class="btn btn--primary">Back to honour board</a>
</section>
