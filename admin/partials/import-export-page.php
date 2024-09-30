<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
<div class="wrap bricks-importer<?php echo !$is_license_valid ? ' inactive' : ''; ?>">
    <h1 class="wp-heading-inline">Bricks Builder Templates</h1>
    <hr class="wp-header-end">

    <?php if (!empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($is_license_valid): ?>
        <!-- Your form HTML goes here -->
    <?php else: ?>
        <p>Please activate your license to access the import/export features.</p>
    <?php endif; ?>
</div>