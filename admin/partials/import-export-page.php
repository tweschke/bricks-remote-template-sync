<?php
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
<div class="wrap bricks-importer<?php echo !$is_license_valid ? ' inactive' : ''; ?>">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <hr class="wp-header-end">

    <?php if (!empty($message)): ?>
        <div id="feedback-message" class="<?php echo $message_type; ?>">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($is_license_valid): ?>
        <div class="bricks-section">
            <h2><?php _e('Import from CSV', 'bricks-remote-template-sync'); ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit" name="import_remote_templates" class="button button-primary"><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></button>
            </form>
        </div>

        <div class="bricks-section">
            <h2><?php _e('Import from JSON', 'bricks-remote-template-sync'); ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="file" name="json_file" accept=".json" required>
                <button type="submit" name="import_remote_templates_json" class="button button-primary"><?php _e('Import Templates', 'bricks-remote-template-sync'); ?></button>
            </form>
        </div>

        <div class="bricks-section">
            <h2><?php _e('Sync with Google Sheets', 'bricks-remote-template-sync'); ?></h2>
            <form method="POST" id="google-sheet-form">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <input type="url" name="google_sheet_url" id="google_sheet_url" value="<?php echo esc_attr($saved_google_sheet_url); ?>" placeholder="https://docs.google.com/spreadsheets/d/..." required>
                <button type="submit" name="import_from_google_sheet" class="button button-primary"><?php _e('Sync Templates', 'bricks-remote-template-sync'); ?></button>
            </form>
        </div>

        <div class="bricks-section">
            <h2><?php _e('Export Templates', 'bricks-remote-template-sync'); ?></h2>
            <div class="button-group">
                <button id="export-csv" class="button button-secondary"><?php _e('Export to CSV', 'bricks-remote-template-sync'); ?></button>
                <button id="export-json" class="button button-secondary"><?php _e('Export to JSON', 'bricks-remote-template-sync'); ?></button>
            </div>
        </div>

        <div class="bricks-section">
            <h2><?php _e('Reset Templates', 'bricks-remote-template-sync'); ?></h2>
            <form method="POST" onsubmit="return confirm('<?php _e('Are you sure you want to reset all remote templates? This action cannot be undone.', 'bricks-remote-template-sync'); ?>');">
                <?php wp_nonce_field('bb_import_templates', 'bb_import_nonce'); ?>
                <button type="submit" name="reset_remote_templates" class="button button-secondary bricks-reset-button"><?php _e('Reset Remote Templates', 'bricks-remote-template-sync'); ?></button>
            </form>
        </div>
    <?php else: ?>
        <div class="bricks-section">
            <p><?php _e('Please activate your license to access the import/export features.', 'bricks-remote-template-sync'); ?></p>
        </div>
    <?php endif; ?>
</div>