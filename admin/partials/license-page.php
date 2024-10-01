<?php
/**
 * Admin page template for license management
 *
 * @package Bricks_Remote_Template_Sync
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

$license_key = get_option('client_plugin_license_key', '');
$license_email = get_option('client_plugin_license_email', '');
$license_status = get_option('client_plugin_license_status', 'invalid');

// Display any saved admin notices
settings_errors('bricks_remote_sync_messages');
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('bricks_remote_sync_license'); ?>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('License Key', 'bricks-remote-template-sync'); ?></th>
                <td>
                    <input type="text" name="client_plugin_license_key" value="<?php echo esc_attr($license_key); ?>" class="regular-text" <?php echo $license_status === 'valid' ? 'disabled' : ''; ?>>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('License Email', 'bricks-remote-template-sync'); ?></th>
                <td>
                    <input type="email" name="client_plugin_license_email" value="<?php echo esc_attr($license_email); ?>" class="regular-text" <?php echo $license_status === 'valid' ? 'disabled' : ''; ?>>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('License Status', 'bricks-remote-template-sync'); ?></th>
                <td>
                    <?php if ($license_status === 'valid') : ?>
                        <span style="color:green;"><?php _e('Active', 'bricks-remote-template-sync'); ?></span>
                    <?php else : ?>
                        <span style="color:red;"><?php _e('Inactive', 'bricks-remote-template-sync'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php if ($license_status === 'valid') : ?>
            <p>
                <input type="submit" name="deactivate_license" class="button-secondary" value="<?php _e('Deactivate License', 'bricks-remote-template-sync'); ?>">
            </p>
        <?php else : ?>
            <p>
                <input type="submit" name="activate_license" class="button-primary" value="<?php _e('Activate License', 'bricks-remote-template-sync'); ?>">
            </p>
        <?php endif; ?>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('form').on('submit', function(e) {
        var activating = $('input[name="activate_license"]').length > 0;
        if (activating) {
            var key = $('input[name="client_plugin_license_key"]').val();
            var email = $('input[name="client_plugin_license_email"]').val();
            if (!key || !email) {
                alert('<?php _e('Please enter both license key and email.', 'bricks-remote-template-sync'); ?>');
                e.preventDefault();
            }
        }
    });
});
</script>