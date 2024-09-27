<?php
class Bricks_Remote_Template_Sync_Import_Export {

    public static function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Debug: Check if the function exists
        if (!function_exists('is_client_plugin_license_valid')) {
            echo '<div class="error"><p>Error: is_client_plugin_license_valid function not found.</p></div>';
            return;
        }

        $is_license_valid = is_client_plugin_license_valid();

        // Debug: Output the license status
        echo '<div class="notice notice-info"><p>Debug: License status: ' . ($is_license_valid ? 'Valid' : 'Invalid') . '</p></div>';

        $saved_google_sheet_url = get_option('bricks_remote_sync_google_sheet_url', '');
        $feedback_message = '';
        $feedback_type = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_license_valid) {
            // Your existing POST handling code...
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

            <!-- Your existing form elements... -->

        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isLicenseValid = <?php echo $is_license_valid ? 'true' : 'false'; ?>;
            console.log('Debug: isLicenseValid =', isLicenseValid); // Debug: Log to console

            if (!isLicenseValid) {
                document.querySelectorAll('.bricks-importer input, .bricks-importer button').forEach(el => {
                    el.disabled = true;
                    console.log('Debug: Disabled element', el); // Debug: Log each disabled element
                });
            }

            // Your existing JavaScript code...
        });
        </script>
        <?php
    }

    // Your existing methods...
}