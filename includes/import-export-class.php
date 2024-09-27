<?php
class Bricks_Remote_Template_Sync_Import_Export {

    public static function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Ensure the function exists
        if (!function_exists('is_client_plugin_license_valid')) {
            error_log('Error: is_client_plugin_license_valid function not found.');
            echo '<div class="error"><p>Error: License validation function not found. Please contact support.</p></div>';
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();

        // Debug output
        error_log('Debug: License status in render_import_export_page: ' . ($is_license_valid ? 'Valid' : 'Invalid'));

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
                        <div class="button-group">
                            <button type="submit" name="import_from_google_sheet" class="button button-primary">Sync Templates from Google Sheets</button>
                            <button type="button" id="save-google-sheet-url" class="button button-primary">Save Google Sheet URL</button>
                        </div>
                    </form>
                </div>

                <div class="bricks-section">
                    <h2>Export to CSV</h2>
                    <form method="POST" id="csv-export-form">
                        <?php wp_nonce_field('bb_import_templates'); ?>
                        <button type="submit" name="export_to_csv" class="button button-primary">Export Templates to CSV</button>
                    </form>
                </div>

                <div class="bricks-section">
                    <h2>Export to JSON</h2>
                    <form method="POST" id="json-export-form">
                        <?php wp_nonce_field('bb_import_templates'); ?>
                        <button type="submit" name="export_to_json" class="button button-primary">Export Templates to JSON</button>
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

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isLicenseValid = <?php echo $is_license_valid ? 'true' : 'false'; ?>;
            console.log('Debug: isLicenseValid =', isLicenseValid);

            if (!isLicenseValid) {
                document.querySelectorAll('.bricks-importer input, .bricks-importer button').forEach(el => {
                    el.disabled = true;
                    console.log('Debug: Disabled element', el);
                });
            }

            document.getElementById('csv-export-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isLicenseValid) {
                    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=bb_export_remote_templates_to_csv')
                        .then(response => response.text())
                        .then(data => {
                            const blob = new Blob([data], { type: 'text/csv' });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'bricks_remote_templates.csv';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            showFeedbackMessage('Templates exported to CSV successfully.', 'success');
                        })
                        .catch(() => {
                            showFeedbackMessage('Failed to export templates to CSV.', 'error');
                        });
                }
            });

            document.getElementById('json-export-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isLicenseValid) {
                    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=bb_export_remote_templates_to_json')
                        .then(response => response.json())
                        .then(data => {
                            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'bricks_remote_templates.json';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            showFeedbackMessage('Templates exported to JSON successfully.', 'success');
                        })
                        .catch(() => {
                            showFeedbackMessage('Failed to export templates to JSON.', 'error');
                        });
                }
            });

            document.getElementById('save-google-sheet-url').addEventListener('click', function() {
                const googleSheetUrl = document.getElementById('google_sheet_url').value;
                if (googleSheetUrl && isLicenseValid) {
                    const data = {
                        'action': 'bb_save_google_sheet_url',
                        'google_sheet_url': googleSheetUrl,
                        '_wpnonce': '<?php echo wp_create_nonce('bb_save_google_sheet_url'); ?>'
                    };
                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams(data).toString()
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showFeedbackMessage('Google Sheet URL saved successfully.', 'success');
                        } else {
                            showFeedbackMessage('Error saving Google Sheet URL: ' + data.message, 'error');
                        }
                    })
                    .catch(() => {
                        showFeedbackMessage('Error saving Google Sheet URL.', 'error');
                    });
                } else if (!isLicenseValid) {
                    showFeedbackMessage('Please activate your license to use this feature.', 'error');
                } else {
                    showFeedbackMessage('Please enter a Google Sheet URL.', 'error');
                }
            });

            function showFeedbackMessage(message, type) {
                const feedbackElement = document.getElementById('feedback-message');
                if (feedbackElement) {
                    feedbackElement.className = `notice notice-${type} is-dismissible`;
                    feedbackElement.innerHTML = `<p>${message}</p>`;
                    feedbackElement.style.display = 'block';
                    feedbackElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    setTimeout(() => {
                        feedbackElement.style.display = 'none';
                    }, 5000);
                }
            }
        });
        </script>
        <?php
    }

    public static function import_from_csv($file_path) {
        // Implement CSV import logic here
    }

    public static function import_from_json($file_path) {
        // Implement JSON import logic here
    }

    public static function import_from_google_sheet($google_sheet_url) {
        // Implement Google Sheets import logic here
    }

    public static function reset_remote_templates() {
        // Implement reset logic here
    }

    // Add any other methods you need for your import/export functionality
}