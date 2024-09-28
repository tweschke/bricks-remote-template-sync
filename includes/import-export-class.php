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
                $result = self::import_from_csv($file_path);
                if ($result) {
                    $feedback_message = 'Templates imported successfully from CSV.';
                    $feedback_type = 'success';
                } else {
                    $feedback_message = 'Failed to import templates from CSV.';
                    $feedback_type = 'error';
                }
            }

            if (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                check_admin_referer('bb_import_templates');
                $google_sheet_url = esc_url_raw($_POST['google_sheet_url']);
                $result = self::import_from_google_sheet($google_sheet_url);
                if ($result) {
                    $feedback_message = 'Templates synced successfully from Google Sheets.';
                    $feedback_type = 'success';
                } else {
                    $feedback_message = 'Failed to sync templates from Google Sheets.';
                    $feedback_type = 'error';
                }
            }

            if (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                check_admin_referer('bb_import_templates');
                $file_path = $_FILES['json_file']['tmp_name'];
                $result = self::import_from_json($file_path);
                if ($result) {
                    $feedback_message = 'Templates imported successfully from JSON.';
                    $feedback_type = 'success';
                } else {
                    $feedback_message = 'Failed to import templates from JSON.';
                    $feedback_type = 'error';
                }
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

            if (!isLicenseValid) {
                document.querySelectorAll('.bricks-importer input, .bricks-importer button').forEach(el => {
                    el.disabled = true;
                });
            }

            document.getElementById('csv-export-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isLicenseValid) {
                    window.location.href = '<?php echo admin_url('admin-ajax.php?action=bb_export_remote_templates_to_csv'); ?>';
                }
            });

            document.getElementById('json-export-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isLicenseValid) {
                    window.location.href = '<?php echo admin_url('admin-ajax.php?action=bb_export_remote_templates_to_json'); ?>';
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
                            alert('Google Sheet URL saved successfully.');
                        } else {
                            alert('Error saving Google Sheet URL: ' + data.message);
                        }
                    })
                    .catch(() => {
                        alert('Error saving Google Sheet URL.');
                    });
                } else if (!isLicenseValid) {
                    alert('Please activate your license to use this feature.');
                } else {
                    alert('Please enter a Google Sheet URL.');
                }
            });
        });
        </script>
        <?php
    }

    public static function import_from_csv($file_path) {
        $file = fopen($file_path, 'r');
        if ($file) {
            fgetcsv($file); // Skip header row
            $imported = 0;
            while (($line = fgetcsv($file)) !== FALSE) {
                if (count($line) >= 3) {
                    $template_id = sanitize_text_field($line[0]);
                    $name = sanitize_text_field($line[1]);
                    $url = esc_url_raw($line[2]);
                    $password = isset($line[3]) ? sanitize_text_field($line[3]) : '';
                    if (self::save_remote_template($template_id, $name, $url, $password)) {
                        $imported++;
                    }
                }
            }
            fclose($file);
            return $imported > 0;
        }
        return false;
    }

    public static function import_from_json($file_path) {
        $json_data = file_get_contents($file_path);
        $templates = json_decode($json_data, true);
        if (is_array($templates)) {
            $imported = 0;
            foreach ($templates as $template) {
                if (isset($template['id'], $template['name'], $template['url'])) {
                    $template_id = sanitize_text_field($template['id']);
                    $name = sanitize_text_field($template['name']);
                    $url = esc_url_raw($template['url']);
                    $password = isset($template['password']) ? sanitize_text_field($template['password']) : '';
                    if (self::save_remote_template($template_id, $name, $url, $password)) {
                        $imported++;
                    }
                }
            }
            return $imported > 0;
        }
        return false;
    }

    public static function import_from_google_sheet($google_sheet_url) {
        $response = wp_remote_get($google_sheet_url);
        if (!is_wp_error($response)) {
            $csv_data = wp_remote_retrieve_body($response);
            $lines = explode("\n", $csv_data);
            array_shift($lines); // Skip header row
            $imported = 0;
            foreach ($lines as $line) {
                $data = str_getcsv($line);
                if (count($data) >= 3) {
                    $template_id = sanitize_text_field($data[0]);
                    $name = sanitize_text_field($data[1]);
                    $url = esc_url_raw($data[2]);
                    $password = isset($data[3]) ? sanitize_text_field($data[3]) : '';
                    if (self::save_remote_template($template_id, $name, $url, $password)) {
                        $imported++;
                    }
                }
            }
            return $imported > 0;
        }
        return false;
    }

    private static function save_remote_template($template_id, $name, $url, $password) {
        $remote_templates = get_option('bricks_remote_templates', array());
        $remote_templates[$template_id] = array(
            'name' => $name,
            'url' => $url,
            'password' => $password
        );
        return update_option('bricks_remote_templates', $remote_templates);
    }

    public static function reset_remote_templates() {
        return delete_option('bricks_remote_templates');
    }

    public static function export_to_csv() {
        $remote_templates = get_option('bricks_remote_templates', array());
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bricks_remote_templates.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Template ID', 'Name', 'URL', 'Password'));
        foreach ($remote_templates as $id => $template) {
            fputcsv($output, array($id, $template['name'], $template['url'], $template['password']));
        }
        fclose($output);
        exit;
    }

    public static function export_to_json() {
        $remote_templates = get_option('bricks_remote_templates', array());
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="bricks_remote_templates.json"');
        echo json_encode($remote_templates, JSON_PRETTY_PRINT);
        exit;
    }
}