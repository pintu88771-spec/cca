<?php
/** @var array<int, array{type: string, message: string}> $items */
$items = flashes();

if (!$items) {
    return;
}

$icons = [
    'success' => '✓',
    'danger'  => '!',
    'warning' => '!',
    'info'    => 'i',
];
?>
<div class="flash-stack" aria-live="polite">
    <?php foreach ($items as $item): ?>
        <?php $type = in_array($item['type'], ['success', 'danger', 'warning', 'info'], true) ? $item['type'] : 'info'; ?>
        <div class="alert alert--<?= e($type) ?>" role="alert">
            <span class="alert__icon" aria-hidden="true"><?= e($icons[$type]) ?></span>
            <span class="alert__text"><?= e($item['message']) ?></span>
            <button type="button" class="alert__close" data-dismiss aria-label="Dismiss message">&times;</button>
        </div>
    <?php endforeach; ?>
</div>
