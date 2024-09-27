<?php
function schedule_license_check() {
    if (!wp_next_scheduled('check_license_status')) {
        wp_schedule_event(time(), 'daily', 'check_license_status');
    }
}

function unschedule_license_check() {
    wp_clear_scheduled_hook('check_license_status');
}

add_action('check_license_status', 'perform_license_check');

function perform_license_check() {
    $license_key = get_option('client_plugin_license_key');
    $license_email = get_option('client_plugin_license_email');
    
    if ($license_key && $license_email) {
        $validation_result = validate_client_plugin_license($license_key, $license_email);
        update_option('client_plugin_license_status', $validation_result['valid'] ? 'valid' : 'invalid');
    } else {
        update_option('client_plugin_license_status', 'invalid');
    }
}

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
        return array('valid' => false, 'message' => 'Connection error. Please try again later.');
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
}

function handle_license_form_submission() {
    if (isset($_POST['activate_license'])) {
        $license_key = sanitize_text_field($_POST['license_key']);
        $license_email = sanitize_email($_POST['license_email']);
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
