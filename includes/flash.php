<?php
/** Render and clear flash messages from $_SESSION['flash']. */
if (!empty($_SESSION['flash'])):
    foreach ($_SESSION['flash'] as $f):
        $type = e($f['type'] ?? 'info'); ?>
        <div class="flash flash-<?= $type ?>" role="alert">
            <?= e($f['message'] ?? '') ?>
        </div>
    <?php endforeach;
    unset($_SESSION['flash']);
endif;

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }
}
