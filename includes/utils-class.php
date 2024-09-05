<?php
namespace Bricks_Remote_Template_Sync;

class Utils {
    public static function sanitize_template_data($data) {
        return array(
            'name' => sanitize_text_field($data['name']),
            'url' => esc_url_raw($data['url']),
            'password' => sanitize_text_field($data['password'])
        );
    }

    public static function validate_google_sheet_url($url) {
        return (strpos($url, '/export?format=csv') !== false || strpos($url, '/pub?output=csv') !== false);
    }
}
