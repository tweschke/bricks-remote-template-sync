<?php
/**
 * Plugin Name: Bricks Remote Template Sync
 * Plugin URI: https://wpdesigns4u.com/plugins/bricks-remote-template-sync
 * Description: A plugin to import remote templates into Bricks Builder from a CSV file or Google Sheets, reset all remote templates, and export/import to/from CSV or JSON.
 * Version: 1.0.4
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
define('BRICKS_REMOTE_SYNC_VERSION', '1.0.4');
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
    add_action('wp_ajax_bb_export_remote_templates_to_csv', array('Bricks_Remote_Template_Sync_Import_Export', 'export_to_csv'));
    add_action('wp_ajax_bb_export_remote_templates_to_json', array('Bricks_Remote_Template_Sync_Import_Export', 'export_to_json'));
    add_action('wp_ajax_bb_save_google_sheet_url', array('Bricks_Remote_Template_Sync_Import_Export', 'save_google_sheet_url'));
}

// Run the plugin
add_action('plugins_loaded', 'run_bricks_remote_template_sync');