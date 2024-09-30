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
        <div class="bricks-section">
            <h2>Import from CSV</h2>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit" name="import_remote_templates" class="button button-primary">Import Templates</button>
            </form>
        </div>

        <div class="bricks-section">
            <h2>Import from JSON</h2>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <input type="file" name="json_file" accept=".json" required>
                <button type="submit" name="import_remote_templates_json" class="button button-primary">Import Templates</button>
            </form>
        </div>

        <div class="bricks-section">
            <h2>Sync with Google Sheets</h2>
            <form method="POST" id="google-sheet-form">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <input type="url" name="google_sheet_url" id="google_sheet_url" value="<?php echo esc_attr($saved_google_sheet_url); ?>" placeholder="https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/pub?output=csv" required>
                <button type="submit" name="import_from_google_sheet" class="button button-primary">Sync Templates from Google Sheets</button>
            </form>
        </div>

        <div class="bricks-section">
            <h2>Reset Templates</h2>
            <form method="POST" onsubmit="return confirm('Are you sure you want to reset all remote templates? This action cannot be undone.');">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <button type="submit" name="reset_remote_templates" class="button button-secondary bricks-reset-button">Reset Remote Templates</button>
            </form>
        </div>
    <?php else: ?>
        <p>Please activate your license to access the import/export features.</p>
    <?php endif; ?>
</div>