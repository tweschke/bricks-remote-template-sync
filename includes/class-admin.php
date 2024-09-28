<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH')) {
    die('Direct access to this file is not allowed.');
}

class Bricks_Remote_Template_Sync_Admin {

    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_bb_save_google_sheet_url', array($this, 'save_google_sheet_url'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Bricks Templates',
            'Bricks Templates',
            'manage_options',
            'bb-import-remote-templates',
            array($this, 'display_import_page'),
            'dashicons-database-import',
            60
        );

        add_submenu_page(
            'bb-import-remote-templates',
            'Import/Export',
            'Import/Export',
            'manage_options',
            'bb-import-remote-templates',
            array($this, 'display_import_page')
        );

        add_submenu_page(
            'bb-import-remote-templates',
            'License',
            'License',
            'manage_options',
            'bb-license',
            array($this, 'display_license_page')
        );
    }

    public function enqueue_admin_styles($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false &&
            strpos($hook, 'bb-license') === false) {
            return;
        }
        wp_enqueue_style('bricks-remote-sync-admin', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/css/admin-style.css', array(), BRICKS_REMOTE_SYNC_VERSION, 'all');
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'bb-import-remote-templates') === false &&
            strpos($hook, 'bb-license') === false) {
            return;
        }
        wp_enqueue_script('bricks-remote-sync-admin', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), BRICKS_REMOTE_SYNC_VERSION, false);
        wp_localize_script('bricks-remote-sync-admin', 'bricksRemoteSync', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bb_save_google_sheet_url')
        ));
    }

    public function display_import_page() {
        error_log("Displaying import page");
        Bricks_Remote_Template_Sync_Import_Export::render_import_export_page();
    }

    public function display_license_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $license_key = get_option('client_plugin_license_key', '');
        $license_email = get_option('client_plugin_license_email', '');
        $license_status = get_option('client_plugin_license_status', 'invalid');

        ?>
        <div class="wrap">
            <h1>Bricks Remote Template Sync License</h1>
            <form method="post" action="options.php">
                <?php settings_fields('bricks_remote_sync_license'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">License Key</th>
                        <td><input type="text" name="client_plugin_license_key" value="<?php echo esc_attr($license_key); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">License Email</th>
                        <td><input type="email" name="client_plugin_license_email" value="<?php echo esc_attr($license_email); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button('Save License'); ?>
            </form>
            <?php if ($license_status == 'valid') : ?>
                <p>Your license is active.</p>
                <form method="post" action="options.php">
                    <?php settings_fields('bricks_remote_sync_license'); ?>
                    <input type="hidden" name="bricks_remote_sync_deactivate_license" value="1" />
                    <?php submit_button('Deactivate License'); ?>
                </form>
            <?php else : ?>
                <p>Your license is inactive. Please enter your license key and email to activate.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function save_google_sheet_url() {
        check_ajax_referer('bb_save_google_sheet_url', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('You do not have sufficient permissions to perform this action.');
            return;
        }

        if (isset($_POST['google_sheet_url'])) {
            $google_sheet_url = esc_url_raw($_POST['google_sheet_url']);
            update_option('bricks_remote_sync_google_sheet_url', $google_sheet_url);
            wp_send_json_success(['message' => 'Google Sheet URL saved successfully.']);
        } else {
            wp_send_json_error(['message' => 'Google Sheet URL missing.']);
        }
    }

    public function register_settings() {
        register_setting('bricks_remote_sync_license', 'client_plugin_license_key');
        register_setting('bricks_remote_sync_license', 'client_plugin_license_email');
    }

    public function handle_license_actions() {
        if (isset($_POST['bricks_remote_sync_deactivate_license'])) {
            delete_option('client_plugin_license_key');
            delete_option('client_plugin_license_email');
            update_option('client_plugin_license_status', 'invalid');
            add_settings_error('bricks_remote_sync_messages', 'bricks_remote_sync_message', __('License deactivated.', 'bricks-remote-template-sync'), 'updated');
        } elseif (isset($_POST['client_plugin_license_key']) && isset($_POST['client_plugin_license_email'])) {
            $license_key = sanitize_text_field($_POST['client_plugin_license_key']);
            $license_email = sanitize_email($_POST['client_plugin_license_email']);
            
            $validation_result = validate_client_plugin_license($license_key, $license_email);
            
            if ($validation_result['valid']) {
                update_option('client_plugin_license_key', $license_key);
                update_option('client_plugin_license_email', $license_email);
                update_option('client_plugin_license_status', 'valid');
                add_settings_error('bricks_remote_sync_messages', 'bricks_remote_sync_message', __('License activated successfully.', 'bricks-remote-template-sync'), 'updated');
            } else {
                add_settings_error('bricks_remote_sync_messages', 'bricks_remote_sync_message', __('License activation failed: ', 'bricks-remote-template-sync') . $validation_result['message'], 'error');
            }
        }
    }
}