<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}
?>
<div class="wrap bricks-importer<?php echo !$is_license_valid ? ' inactive' : ''; ?>">
    <h1 class="wp-heading-inline">Bricks Builder Templates</h1>
    <hr class="wp-header-end">

    <?php if (!$is_license_valid): ?>
        <div class="notice notice-error">
            <p>Your license is not active or has expired. Please <a href="<?php echo admin_url('admin.php?page=bb-license'); ?>">activate your license</a> to use this plugin.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($feedback_message)): ?>
        <div id="feedback-message" class="notice notice-<?php echo $feedback_type; ?> is-dismissible">
            <p><?php echo esc_html($feedback_message); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($is_license_valid): ?>
        <!-- Import from CSV -->
        <div class="bricks-section">
            <h2>Import from CSV</h2>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit" name="import_remote_templates" class="button button-primary">Import Templates</button>
            </form>
        </div>

        <!-- Import from JSON -->
        <div class="bricks-section">
            <h2>Import from JSON</h2>
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <input type="file" name="json_file" accept=".json" required>
                <button type="submit" name="import_remote_templates_json" class="button button-primary">Import Templates</button>
            </form>
        </div>

        <!-- Sync with Google Sheets -->
        <div class="bricks-section">
            <h2>Sync with Google Sheets</h2>
            <form method="POST" id="google-sheet-form">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <input type="url" name="google_sheet_url" id="google_sheet_url" value="<?php echo esc_attr($saved_google_sheet_url); ?>" placeholder="https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/pub?output=csv" required>
                <div class="button-group">
                    <button type="submit" name="import_from_google_sheet" class="button button-primary">Sync Templates from Google Sheets</button>
                    <button type="button" id="save-google-sheet-url" class="button button-primary">Save Google Sheet URL</button>
                </div>
            </form>
        </div>

        <!-- Export Templates -->
        <div class="bricks-section">
            <h2>Export Templates</h2>
            <form method="POST" id="csv-export-form">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <button type="submit" name="export_to_csv" class="button button-primary">Export to CSV</button>
            </form>
            <form method="POST" id="json-export-form">
                <?php wp_nonce_field('bb_import_templates'); ?>
                <button type="submit" name="export_to_json" class="button button-primary">Export to JSON</button>
            </form>
        </div>

        <!-- Reset Templates -->
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