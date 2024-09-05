<?php
/**
 * Plugin Name: Bricks Remote Template Sync
 * Plugin URI: https://wpdesigns4u.com/plugins/bricks-remote-template-sync
 * Description: A plugin to import remote templates into Bricks Builder from a CSV file or Google Sheets, reset all remote templates, and export/import to/from CSV or JSON.
 * Version: 1.0
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
define('BRICKS_REMOTE_SYNC_VERSION', '1.0');
define('BRICKS_REMOTE_SYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRICKS_REMOTE_SYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main class file
require_once BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'includes/class-init.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_bricks_remote_template_sync() {
    $plugin = new Bricks_Remote_Template_Sync\Init();
    $plugin->run();
}
add_action('plugins_loaded', 'run_bricks_remote_template_sync');