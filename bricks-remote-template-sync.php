<?php
/**
 * Plugin Name: Bricks Remote Template Sync
 * Plugin URI: https://wpdesigns4u.com/plugins/bricks-remote-template-sync
 * Description: A plugin to import remote templates into Bricks Builder from a CSV file, JSON file, or Google Sheets, reset all remote templates, and export to CSV or JSON.
 * Version: 1.0.5
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Thomas Weschke
 * Author URI: https://wpdesigns4u.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bricks-remote-template-sync
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

// Define plugin constants
define('BRICKS_REMOTE_SYNC_VERSION', '1.0.5');
define('BRICKS_REMOTE_SYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRICKS_REMOTE_SYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the required files
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-admin.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/import-export-class.php';

/**
 * Begins execution of the plugin.
 */
function run_bricks_remote_template_sync() {
    $plugin_admin = new Bricks_Remote_Template_Sync_Admin();
    $plugin_admin->init();

    // Add AJAX actions
    add_action('wp_ajax_bb_export_remote_templates_to_csv', array('Bricks_Remote_Template_Sync_Export', 'export_to_csv'));
    add_action('wp_ajax_bb_export_remote_templates_to_json', array('Bricks_Remote_Template_Sync_Export', 'export_to_json'));
    add_action('wp_ajax_bb_save_google_sheet_url', array('Bricks_Remote_Template_Sync_Import_Export', 'save_google_sheet_url'));
    add_action('wp_ajax_bb_run_google_sheet_sync', array('Bricks_Remote_Template_Sync_Import_Export', 'run_google_sheet_sync'));
}

// Run the plugin
add_action('plugins_loaded', 'run_bricks_remote_template_sync');

// Register activation hook
register_activation_hook(__FILE__, 'bricks_remote_template_sync_activate');

/**
 * Plugin activation function
 */
function bricks_remote_template_sync_activate() {
    // Perform any necessary setup on activation
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'bricks_remote_template_sync_deactivate');

/**
 * Plugin deactivation function
 */
function bricks_remote_template_sync_deactivate() {
    // Perform any necessary cleanup on deactivation
}

function bricks_remote_sync_register_settings() {
    register_setting('bricks_remote_sync_license', 'client_plugin_license_key');
    register_setting('bricks_remote_sync_license', 'client_plugin_license_email');
    register_setting('bricks_remote_sync_license', 'client_plugin_license_status');
}
add_action('admin_init', 'bricks_remote_sync_register_settings');

// New license-related functions

function validate_client_plugin_license($license_key, $license_email) {
    $api_url = 'https://www.wpdesigns4u.com/wp-json/license-api/v1/validate';
    $product_id = 'bricks-remote-template-sync';

    $response = wp_remote_post($api_url, array(
        'timeout' => 45,
        'body' => array(
            'license_key' => $license_key,
            'license_email' => $license_email,
            'product_id' => $product_id
        )
    ));

    if (is_wp_error($response)) {
        return array('valid' => false, 'message' => 'Connection error: ' . $response->get_error_message());
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['valid'])) {
        return $data;
    }

    return array('valid' => false, 'message' => 'Invalid response from server. Please try again later.');
}

function is_client_plugin_license_valid() {
    $license_status = get_option('client_plugin_license_status');
    return $license_status === 'valid';
}

function client_plugin_license_notice() {
    ?>
    <div class="notice notice-error">
        <p>Your Bricks Remote Template Sync license is not active or has expired. Please <a href="<?php echo admin_url('admin.php?page=bb-license'); ?>">check your license</a> to continue using the plugin.</p>
    </div>
    <?php
}

function handle_license_form_submission() {
    if (isset($_POST['activate_license'])) {
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
    } elseif (isset($_POST['deactivate_license'])) {
        delete_option('client_plugin_license_key');
        delete_option('client_plugin_license_email');
        update_option('client_plugin_license_status', 'invalid');
        add_settings_error('bricks_remote_sync_messages', 'bricks_remote_sync_message', __('License deactivated successfully.', 'bricks-remote-template-sync'), 'updated');
    }
}
add_action('admin_init', 'handle_license_form_submission');

// Add license check to admin notices
add_action('admin_notices', 'client_plugin_license_notice');