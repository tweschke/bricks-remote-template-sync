<?php
namespace Bricks_Remote_Template_Sync;

class Import_Export {
    public function import_from_csv($file) {
        // Implement CSV import logic
    }

    public function import_from_json($file) {
        // Implement JSON import logic
    }

    public function import_from_google_sheet($google_sheet_url) {
        // Implement Google Sheets import logic
    }

    public function export_to_csv() {
        // Implement CSV export logic
    }

    public function export_to_json() {
        // Implement JSON export logic
    }

    public function reset_remote_templates() {
        // Implement reset logic
    }

    private function update_remote_templates($new_templates) {
        $global_settings = get_option('Bricks_Global_Settings', array());
        $global_settings['remoteTemplates'] = $new_templates;
        update_option('Bricks_Global_Settings', $global_settings);
    }

    public function get_remote_templates() {
        $global_settings = get_option('Bricks_Global_Settings', array());
        return isset($global_settings['remoteTemplates']) ? $global_settings['remoteTemplates'] : array();
    }
}
