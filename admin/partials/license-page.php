<?php
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