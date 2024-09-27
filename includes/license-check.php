<?php
// Schedule license check on plugin activation
function schedule_license_check() {
    if (!wp_next_scheduled('check_license_status')) {
        wp_schedule_event(time(), 'daily', 'check_license_status');
    }
}

// Remove scheduled event on plugin deactivation
function unschedule_license_check() {
    wp_clear_scheduled_hook('check_license_status');
}

// Add the license check function to the scheduled event
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

// Function to validate the license with your server
function validate_client_plugin_license($license_key, $license_email) {
    $api_url = 'https://www.wpdesigns4u.com/wp-json/license-api/v1/validate';
    $product_id = 'bricks-remote-template-sync'; // Set this to match your product ID on the license server

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

// Check if the license is valid
function is_client_plugin_license_valid() {
    $license_status = get_option('client_plugin_license_status');
    return $license_status === 'valid';
}

// Add an admin notice if the license is not valid
function client_plugin_license_notice() {
    ?>
    <div class="notice notice-error">
        <p>Your Bricks Remote Template Sync license is not active or has expired. Please <a href="<?php echo admin_url('admin.php?page=bb-license'); ?>">check your license</a> to continue using the plugin.</p>
    </div>
    <?php
}

// Create the license management page
function client_plugin_license_page() {
    $license_key = get_option('client_plugin_license_key');
    $license_email = get_option('client_plugin_license_email');
    $license_status = get_option('client_plugin_license_status');

    if (isset($_POST['activate_license'])) {
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
        } else {
            echo '<div class="notice notice-error"><p>License activation failed: ' . esc_html($validation_result['message']) . '</p></div>';
        }
    }

    if (isset($_POST['deactivate_license'])) {
        delete_option('client_plugin_license_key');
        delete_option('client_plugin_license_email');
        update_option('client_plugin_license_status', 'invalid');
        $license_key = '';
        $license_email = '';
        $license_status = 'invalid';
        echo '<div class="notice notice-success"><p>License deactivated successfully!</p></div>';
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
}