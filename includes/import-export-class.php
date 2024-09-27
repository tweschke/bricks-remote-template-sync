<?php
class Bricks_Remote_Template_Sync_Import_Export {

    public static function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();
        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $feedback_message = '';
        $feedback_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
            if (isset($_POST['import_remote_templates']) && !empty($_FILES['csv_file']['tmp_name'])) {
                check_admin_referer('bb_import_templates');
                $file_path = $_FILES['csv_file']['tmp_name'];
                self::import_from_csv($file_path);
                $feedback_message = 'Templates imported successfully from CSV.';
                $feedback_type = 'success';
            }

            if (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                check_admin_referer('bb_import_templates');
                $google_sheet_url = esc_url_raw($_POST['google_sheet_url']);
                self::import_from_google_sheet($google_sheet_url);
                $feedback_message = 'Templates synced successfully from Google Sheets.';
                $feedback_type = 'success';
            }

            if (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                check_admin_referer('bb_import_templates');
                $file_path = $_FILES['json_file']['tmp_name'];
                self::import_from_json($file_path);
                $feedback_message = 'Templates imported successfully from JSON.';
                $feedback_type = 'success';
            }

            if (isset($_POST['reset_remote_templates'])) {
                check_admin_referer('bb_import_templates');
                self::reset_remote_templates();
                $feedback_message = 'All remote templates have been reset.';
                $feedback_type = 'warning';
            }
        }

        ?>
        <div class="wrap bricks-importer<?php echo !$is_license_valid ? ' inactive' : ''; ?>">
            <h1 class="wp-heading-inline">Bricks Builder Templates</h1>
            <hr class="wp-header-end">

            <?php if (!$is_license_valid): ?>
                <div class="notice notice-warning">
                    <p>Please activate your license to use this plugin. <a href="<?php echo admin_url('admin.php?page=bb-license'); ?>">Activate License</a></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($feedback_message)): ?>
                <div id="feedback-message" class="notice notice-<?php echo $feedback_type; ?> is-dismissible">
                    <p><?php echo esc_html($feedback_message); ?></p>
                </div>
            <?php endif; ?>

            <div class="bricks-section">
                <h2>Import from CSV</h2>
                <form method="POST" enctype="multipart/form-data">
                    <?php wp_nonce_field('bb_import_templates'); ?>
                    <input type="file" name="csv_file" accept=".csv" required<?php echo !$is_license_valid ? ' disabled' : ''; ?>>
                    <button type="submit" name="import_remote_templates" class="button button-primary"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Import Templates</button>
                </form>
            </div>

            <div class="bricks-section">
                <h2>Import from JSON</h2>
                <form method="POST" enctype="multipart/form-data">
                    <?php wp_nonce_field('bb_import_templates'); ?>
                    <input type="file" name="json_file" accept=".json" required<?php echo !$is_license_valid ? ' disabled' : ''; ?>>
                    <button type="submit" name="import_remote_templates_json" class="button button-primary"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Import Templates</button>
                </form>
            </div>

            <div class="bricks-section">
                <h2>Sync with Google Sheets</h2>
                <form method="POST" id="google-sheet-form">
                    <?php wp_nonce_field('bb_import_templates'); ?>
                    <input type="url" name="google_sheet_url" id="google_sheet_url" value="<?php echo esc_attr($saved_google_sheet_url); ?>" placeholder="https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/pub?output=csv" required<?php echo !$is_license_valid ? ' disabled' : ''; ?>>
                    <div class="button-group">
                        <button type="submit" name="import_from_google_sheet" class="button button-primary"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Sync Templates from Google Sheets</button>
                        <button type="button" id="save-google-sheet-url" class="button button-primary"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Save Google Sheet URL</button>
                    </div>
                </form>
            </div>

            <div class="bricks-section">
                <h2>Export to CSV</h2>
                <form method="POST" id="csv-export-form">
                    <?php wp_nonce_field('bb_import_templates'); ?>
                    <button type="submit" name="export_to_csv" class="button button-primary"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Export Templates to CSV</button>
                </form>
            </div>

            <div class="bricks-section">
                <h2>Export to JSON</h2>
                <form method="POST" id="json-export-form">
                    <?php wp_nonce_field('bb_import_templates'); ?>
                    <button type="submit" name="export_to_json" class="button button-primary"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Export Templates to JSON</button>
                </form>
            </div>

            <div class="bricks-section">
                <h2>Reset Templates</h2>
                <form method="POST" onsubmit="return confirm('Are you sure you want to reset all remote templates? This action cannot be undone.');">
                    <?php wp_nonce_field('bb_import_templates'); ?>
                    <button type="submit" name="reset_remote_templates" class="button button-secondary bricks-reset-button"<?php echo !$is_license_valid ? ' disabled' : ''; ?>>Reset Remote Templates</button>
                </form>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isLicenseValid = <?php echo $is_license_valid ? 'true' : 'false'; ?>;
            if (!isLicenseValid) {
                document.querySelectorAll('.bricks-importer input, .bricks-importer button').forEach(el => {
                    el.disabled = true;
                });
            }

            // The rest of your existing JavaScript code...
        });
        </script>
        <?php
    }

    // Your existing methods (import_from_csv, import_from_json, import_from_google_sheet, reset_remote_templates, etc.) should remain unchanged
    // ...

}