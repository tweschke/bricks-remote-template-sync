<?php
namespace Bricks_Remote_Template_Sync;

class Init {
    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'handle_form_submissions'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_bb_export_to_csv', array($this, 'ajax_export_to_csv'));
        add_action('wp_ajax_bb_export_to_json', array($this, 'ajax_export_to_json'));
        add_action('admin_post_reset_remote_templates', array($this, 'handle_reset_templates'));
        add_filter('plugin_action_links_' . plugin_basename(BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'bricks-remote-template-sync.php'), array($this, 'add_action_links'));
    }

    /**
     * Add the options page and menu item.
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            'Bricks Templates',
            'Bricks Templates',
            'manage_options',
            'bricks-remote-template-sync',
            array($this, 'display_plugin_admin_page'),
            'dashicons-database-import',
            60
        );
    }

    /**
     * Handle form submissions
     */
    public function handle_form_submissions() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['import_remote_templates']) && !empty($_FILES['csv_file']['tmp_name'])) {
                $this->import_from_csv($_FILES['csv_file']['tmp_name']);
            } elseif (isset($_POST['import_remote_templates_json']) && !empty($_FILES['json_file']['tmp_name'])) {
                $this->import_from_json($_FILES['json_file']['tmp_name']);
            } elseif (isset($_POST['import_from_google_sheet']) && !empty($_POST['google_sheet_url'])) {
                $this->import_from_google_sheet($_POST['google_sheet_url']);
            }
        }
    }

    /**
     * Enqueue the admin-specific stylesheet and JavaScript.
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'bricks-remote-template-sync') !== false || $hook === 'plugins.php') {
            wp_enqueue_style('bricks-remote-sync-admin', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/css/admin-style.css', array(), BRICKS_REMOTE_SYNC_VERSION);
            wp_enqueue_script('bricks-remote-sync-admin', BRICKS_REMOTE_SYNC_PLUGIN_URL . 'admin/js/admin-script.js', array('jquery'), BRICKS_REMOTE_SYNC_VERSION, true);
            wp_localize_script('bricks-remote-sync-admin', 'bricksRemoteSync', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bricks_remote_sync_export')
            ));
        }
    }

    /**
     * Render the settings page for this plugin.
     */
    public function display_plugin_admin_page() {
        $view = isset($_GET['view']) ? sanitize_key($_GET['view']) : '';

        echo '<div class="wrap bricks-importer">';
        
        switch ($view) {
            case 'readme':
                $this->display_readme();
                break;
            case 'changelog':
                $this->display_changelog();
                break;
            default:
                include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/admin-display.php';
                break;
        }
        
        echo '</div>';
    }

    /**
     * Display the readme page.
     */
    private function display_readme() {
        include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/read-me.php';
    }

    /**
     * Display the changelog page.
     */
    private function display_changelog() {
        include BRICKS_REMOTE_SYNC_PLUGIN_DIR . 'admin/partials/changelog.php';
    }

    /**
     * Add action links to the plugin page.
     */
    public function add_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=bricks-remote-template-sync&view=readme') . '">Read Me</a>',
            '<a href="' . admin_url('admin.php?page=bricks-remote-template-sync&view=changelog') . '">Changelog</a>'
        );
        return array_merge($plugin_links, $links);
    }

    /**
     * Import templates from CSV file
     */
    private function import_from_csv($file) {
        if (($handle = fopen($file, "r")) !== FALSE) {
            $new_templates = array();
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row > 0 && count($data) >= 4) { // Skip header row and ensure we have all fields
                    $new_templates[] = array(
                        'name' => sanitize_text_field($data[1]),
                        'url' => esc_url_raw($data[2]),
                        'password' => sanitize_text_field($data[3])
                    );
                }
                $row++;
            }
            fclose($handle);
            
            $this->update_remote_templates($new_templates);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Templates imported successfully from CSV.</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>Error reading CSV file.</p></div>';
            });
        }
    }

    /**
     * Import templates from JSON file
     */
    private function import_from_json($file) {
        $json_data = file_get_contents($file);
        $templates = json_decode($json_data, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $new_templates = array();
            foreach ($templates as $template) {
                if (isset($template['name'], $template['url'], $template['password'])) {
                    $new_templates[] = array(
                        'name' => sanitize_text_field($template['name']),
                        'url' => esc_url_raw($template['url']),
                        'password' => sanitize_text_field($template['password'])
                    );
                }
            }
            $this->update_remote_templates($new_templates);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Templates imported successfully from JSON.</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>Error reading JSON file.</p></div>';
            });
        }
    }

    /**
     * Import templates from Google Sheets
     */
    private function import_from_google_sheet($google_sheet_url) {
        $csv_data = file_get_contents($google_sheet_url);
        if ($csv_data === false) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>Error fetching data from Google Sheets.</p></div>';
            });
            return;
        }
        
        $rows = explode("\n", $csv_data);
        $new_templates = array();
        
        foreach ($rows as $row => $data) {
            if ($row === 0) continue; // Skip header row
            $data = str_getcsv($data);
            if (count($data) >= 4) {
                $new_templates[] = array(
                    'name' => sanitize_text_field($data[1]),
                    'url' => esc_url_raw($data[2]),
                    'password' => sanitize_text_field($data[3])
                );
            }
        }
        
        $this->update_remote_templates($new_templates);
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Templates synced successfully from Google Sheets.</p></div>';
        });
    }

    /**
     * Update remote templates in Bricks settings
     */
    private function update_remote_templates($new_templates) {
        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = $new_templates;
        update_option('Bricks_Global_Settings', $global_settings);
    }

    /**
     * AJAX handler for CSV export
     */
    public function ajax_export_to_csv() {
        check_ajax_referer('bricks_remote_sync_export', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access');
        }

        $templates = $this->get_remote_templates();
        $csv_data = "template_id,name,url,password\n";
        foreach ($templates as $index => $template) {
            $csv_data .= ($index + 1) . ',' . 
                         $template['name'] . ',' . 
                         $template['url'] . ',' . 
                         $template['password'] . "\n";
        }

        wp_send_json_success($csv_data);
    }

    /**
     * AJAX handler for JSON export
     */
    public function ajax_export_to_json() {
        check_ajax_referer('bricks_remote_sync_export', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access');
        }

        $templates = $this->get_remote_templates();
        wp_send_json_success($templates);
    }

    /**
     * Get remote templates from Bricks settings
     */
    private function get_remote_templates() {
        $global_settings = get_option('Bricks_Global_Settings', array());
        return isset($global_settings['remoteTemplates']) ? $global_settings['remoteTemplates'] : array();
    }

    /**
     * Handle resetting of remote templates
     */
    public function handle_reset_templates() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        check_admin_referer('reset_remote_templates');

        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = array();
        update_option('Bricks_Global_Settings', $global_settings);

        wp_safe_redirect(admin_url('admin.php?page=bricks-remote-template-sync&reset=success'));
        exit;
    }
}