<?php
/**
 * Plugin Name: Bricks Remote Template Sync
 * Plugin URI: https://wpdesigns4u.com/plugins/bricks-remote-template-sync
 * Description: A plugin to import remote templates into Bricks Builder from a CSV file or Google Sheets, reset all remote templates, and export/import to/from CSV or JSON.
 * Version: 1.0.2
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
define('BRICKS_REMOTE_SYNC_VERSION', '1.0.2');
define('BRICKS_REMOTE_SYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRICKS_REMOTE_SYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main class file
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-init.php';

// Include license check functionality
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/license-check.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_bricks_remote_template_sync() {
    // Check if the license is valid before initializing the plugin
    if (is_client_plugin_license_valid()) {
        $plugin = new Bricks_Remote_Template_Sync\Init();
        $plugin->run();
    } else {
        add_action('admin_notices', 'client_plugin_license_notice');
    }
}

// Hook for plugin activation
register_activation_hook(__FILE__, 'bricks_remote_template_sync_activate');

function bricks_remote_template_sync_activate() {
    // Schedule license check
    schedule_license_check();
}

// Hook for plugin deactivation
register_deactivation_hook(__FILE__, 'bricks_remote_template_sync_deactivate');

function bricks_remote_template_sync_deactivate() {
    // Unschedule license check
    unschedule_license_check();
}

// Run the plugin
add_action('plugins_loaded', 'run_bricks_remote_template_sync');

// Add license management menu
add_action('admin_menu', 'client_plugin_license_menu');