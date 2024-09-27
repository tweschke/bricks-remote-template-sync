<?php
// Debug: Add this at the top of the file
error_log('Debug: license-check.php is being included');

// Schedule license check on plugin activation
function schedule_license_check() {
    if (!wp_next_scheduled('check_license_status')) {
        wp_schedule_event(time(), 'daily', 'check_license_status');
    }
    error_log('Debug: License check scheduled');
}

// Remove scheduled event on plugin deactivation
function unschedule_license_check() {
    wp_clear_scheduled_hook('check_license_status');
    error_log('Debug: License check unscheduled');
}

// Add the license check function to the scheduled event
add_action('check_license_status', 'perform_license_check');

function perform_license_check() {
    $license_key = get_option('client_plugin_license_key');
    $license_email = get_option('client_plugin_license_email');
    
    error_log('Debug: Performing license check');
    error_log('Debug: License Key: ' . (empty($license_key) ? 'Not set' : 'Set'));
    error_log('Debug: License Email: ' . (empty($license_email) ? 'Not set' : 'Set'));

    if ($license_key && $license_email) {
        $validation_result = validate_client_plugin_license($license_key, $license_email);
        update_option('client_plugin_license_status', $validation_result['valid'] ? 'valid' : 'invalid');
        error_log('Debug: License status updated to: ' . ($validation_result['valid'] ? 'valid' : 'invalid'));
    } else {
        update_option('client_plugin_license_status', 'invalid');
        error_log('Debug: License status set to invalid due to missing key or email');
    }
}

// Function to validate the license with your server
function validate_client_plugin_license($license_key, $license_email) {
    $api_url = 'https://www.wpdesigns4u.com/wp-json/license-api/v1/validate';
    $product_id = 'bricks-remote-template-sync'; // Set this to match your product ID on the license server

    error_log('Debug: Validating license with API');

    $response = wp_remote_post($api_url, array(
        'timeout' => 45,
        'body' => array(
            'license_key' => $license_key,
            'license_email' => $license_email,
            'product_id' => $product_id
        )
    ));

    if (is_wp_error($response)) {
        error_log('Debug: API request failed: ' . $response->get_error_message());
        return array('valid' => false, 'message' => 'Connection error. Please try again later.');
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    error_log('Debug: API response: ' . print_r($data, true));

    if (isset($data['valid'])) {
        return $data;
    }

    return array('valid' => false, 'message' => 'Invalid response from server. Please try again later.');
}

// Check if the license is valid
function is_client_plugin_license_valid() {
    $license_status = get_option('client_plugin_license_status');
    error_log('Debug: Checking license status: ' . $license_status);
    return $license_status === 'valid';
}

// Add an admin notice if the license is not valid
function client_plugin_license_notice() {
    ?>
    <div class="notice notice-error">
        <p>Your Bricks Remote Template Sync license is not active or has expired. Please <a href="<?php echo admin_url('admin.php?page=bb-license'); ?>">check your license</a> to continue using the plugin.</p>
    </div>
    <?php
    error_log('Debug: Displayed license notice');
}

// Create the license management page
function client_plugin_license_page() {
    error_log('Debug: Rendering license management page');

    $license_key = get_option('client_plugin_license_key');
    $license_email = get_option('client_plugin_license_email');
    $license_status = get_option('client_plugin_license_status');

    if (isset($_POST['activate_license'])) {
        error_log('Debug: License activation attempted');
        $new_license_key = sanitize_text_field($_POST['license_key']);
        $new_license_email = sanitize_email($_POST['license_email']);
        $validation_result = validate_client_plugin_license($new_license_key, $new_license_email);

        if ($validation_result['valid']) {
            update_option('client_plugin_license_key', $new_license_key);
            update_option('client_plugin_license_email', $new_license_email);
            update_option('client_plugin_license_status', 'valid');
            $license_key = $new_license_key;
            $license_email = $new_license_email;
            $license_status = 'valid';
            echo '<div class="notice notice-success"><p>License activated successfully!</p></div>';
            error_log('Debug: License activated successfully');
        } else {
            echo '<div class="notice notice-error"><p>License activation failed: ' . esc_html($validation_result['message']) . '</p></div>';
            error_log('Debug: License activation failed: ' . $validation_result['message']);
        }
    }

    if (isset($_POST['deactivate_license'])) {
        error_log('Debug: License deactivation attempted');
        delete_option('client_plugin_license_key');
        delete_option('client_plugin_license_email');
        update_option('client_plugin_license_status', 'invalid');
        $license_key = '';
        $license_email = '';
        $license_status = 'invalid';
        echo '<div class="notice notice-success"><p>License deactivated successfully!</p></div>';
        error_log('Debug: License deactivated successfully');
    }

    ?>
    <div class="wrap">
        <h1>Bricks Remote Template Sync License Management</h1>
        <?php if ($license_status !== 'valid') : ?>
            <form method="POST">
                <table class="form-table">
                    <tr>
                        <th scope="row">License Key</th>
                        <td><input type="text" name="license_key" value="<?php echo esc_attr($license_key); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row">Email Address</th>
                        <td><input type="email" name="license_email" value="<?php echo esc_attr($license_email); ?>" class="regular-text" required></td>
                    </tr>
                </table>
                <?php submit_button('Activate License', 'primary', 'activate_license'); ?>
            </form>
        <?php else : ?>
            <table class="form-table">
                <tr>
                    <th scope="row">License Key</th>
                    <td><?php echo esc_html(mask_license_key($license_key)); ?></td>
                </tr>
                <tr>
                    <th scope="row">Email Address</th>
                    <td><?php echo esc_html($license_email); ?></td>
                </tr>
                <tr>
                    <th scope="row">Status</th>
                    <td>Active</td>
                </tr>
            </table>
            <form method="POST">
                <?php submit_button('Deactivate License', 'secondary', 'deactivate_license'); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
    error_log('Debug: License management page rendered');
}

// Function to mask the license key
function mask_license_key($license_key) {
    $length = strlen($license_key);
    if ($length <= 4) {
        return $license_key;
    }
    return str_repeat('*', $length - 4) . substr($license_key, -4);
}

// Add a menu item for license management
function client_plugin_license_menu() {
    add_submenu_page(
        'bb-import-remote-templates',
        'License',
        'License',
        'manage_options',
        'bb-license',
        'client_plugin_license_page'
    );
    error_log('Debug: License submenu added');
}

// Debug: Add this function for debugging
function debug_license_info() {
    $license_key = get_option('client_plugin_license_key');
    $license_email = get_option('client_plugin_license_email');
    $license_status = get_option('client_plugin_license_status');

    error_log('Debug: License Key: ' . (empty($license_key) ? 'Not set' : 'Set'));
    error_log('Debug: License Email: ' . (empty($license_email) ? 'Not set' : 'Set'));
    error_log('Debug: License Status: ' . $license_status);
}

// Call this function at the end of the file
debug_license_info();

// Debug: Log that the file has finished loading
error_log('Debug: license-check.php has finished loading');