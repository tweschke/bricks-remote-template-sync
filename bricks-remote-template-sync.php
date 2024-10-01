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

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

define('BRICKS_REMOTE_SYNC_VERSION', '1.0.5');
define('BRICKS_REMOTE_SYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRICKS_REMOTE_SYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-admin.php';
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/import-export-class.php';

function run_bricks_remote_template_sync() {
    $plugin_admin = new Bricks_Remote_Template_Sync_Admin();
    $plugin_admin->init();

    add_action('wp_ajax_bb_export_remote_templates_to_csv', array('Bricks_Remote_Template_Sync_Export', 'export_to_csv'));
    add_action('wp_ajax_bb_export_remote_templates_to_json', array('Bricks_Remote_Template_Sync_Export', 'export_to_json'));
    add_action('wp_ajax_bb_save_google_sheet_url', array('Bricks_Remote_Template_Sync_Import_Export', 'save_google_sheet_url'));
    add_action('wp_ajax_bb_run_google_sheet_sync', array('Bricks_Remote_Template_Sync_Import_Export', 'run_google_sheet_sync'));
}

add_action('plugins_loaded', 'run_bricks_remote_template_sync');

register_activation_hook(__FILE__, 'bricks_remote_template_sync_activate');
function bricks_remote_template_sync_activate() {
    // Perform any necessary setup on activation
}

register_deactivation_hook(__FILE__, 'bricks_remote_template_sync_deactivate');
function bricks_remote_template_sync_deactivate() {
    // Perform any necessary cleanup on deactivation
}

function validate_client_plugin_license($license_key, $license_email) {
    $api_url = 'https://www.wpdesigns4u.com/wp-json/license-api/v1/validate';
    $product_id = 'bricks-remote-template-sync';

    error_log('Attempting license validation - Key: ' . $license_key . ', Product ID: ' . $product_id);

    $response = wp_remote_post($api_url, array(
        'timeout' => 45,
        'body' => array(
            'license_key' => $license_key,
            'product_id' => $product_id
            // Note: The API doesn't seem to use license_email, so we're not sending it
        )
    ));

    if (is_wp_error($response)) {
        $error_message = 'Connection error: ' . $response->get_error_message();
        error_log('License validation failed: ' . $error_message);
        return array('valid' => false, 'message' => $error_message);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    error_log('License validation response - Code: ' . $response_code . ', Body: ' . $body);

    if ($response_code !== 200) {
        $error_message = 'Server returned an unexpected response code: ' . $response_code;
        error_log('License validation failed: ' . $error_message);
        return array('valid' => false, 'message' => $error_message);
    }

    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $error_message = 'Failed to parse server response: ' . json_last_error_msg();
        error_log('License validation failed: ' . $error_message);
        return array('valid' => false, 'message' => $error_message);
    }

    if (!is_array($data) || !isset($data['valid'])) {
        $error_message = 'Unexpected server response format';
        error_log('License validation failed: ' . $error_message);
        return array('valid' => false, 'message' => $error_message);
    }

    error_log('License validation result: ' . print_r($data, true));
    return $data;
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

function bricks_remote_sync_register_settings() {
    register_setting('bricks_remote_sync_license', 'client_plugin_license_key');
    register_setting('bricks_remote_sync_license', 'client_plugin_license_email');
    register_setting('bricks_remote_sync_license', 'client_plugin_license_status');
}
add_action('admin_init', 'bricks_remote_sync_register_settings');