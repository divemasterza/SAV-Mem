<?php
if (!defined('ABSPATH')) exit;

// Add admin menu
add_action('admin_menu', 'savrala_admin_menu');
function savrala_admin_menu() {
    add_menu_page(
        'SAVRALA Settings',
        'SAVRALA',
        'manage_options',
        'savrala-settings',
        'savrala_settings_page',
        'dashicons-groups'
    );
}

// Settings page
function savrala_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (isset($_POST['savrala_save_settings'])) {
        check_admin_referer('savrala_settings_nonce');
        
        // Handle certificate template uploads
        if (isset($_FILES['portrait_template'])) {
            $portrait_id = media_handle_upload('portrait_template', 0);
            if (!is_wp_error($portrait_id)) {
                update_option('savrala_certificate_template_portrait', $portrait_id);
            }
        }
        
        if (isset($_FILES['landscape_template'])) {
            $landscape_id = media_handle_upload('landscape_template', 0);
            if (!is_wp_error($landscape_id)) {
                update_option('savrala_certificate_template_landscape', $landscape_id);
            }
        }
    }
    
    ?>
    <div class="wrap">
        <h1>SAVRALA Settings</h1>
        
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('savrala_settings_nonce'); ?>
            
            <h2>Certificate Templates</h2>
            
            <table class="form-table">
                <tr>
                    <th>Portrait Template</th>
                    <td>
                        <input type="file" name="portrait_template" accept="image/*">
                        <?php
                        $portrait_id = get_option('savrala_certificate_template_portrait');
                        if ($portrait_id) {
                            echo '<br>Current template: ' . wp_get_attachment_image($portrait_id, 'thumbnail');
                        }
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>Landscape Template</th>
                    <td>
                        <input type="file" name="landscape_template" accept="image/*">
                        <?php
                        $landscape_id = get_option('savrala_certificate_template_landscape');
                        if ($landscape_id) {
                            echo '<br>Current template: ' . wp_get_attachment_image($landscape_id, 'thumbnail');
                        }
                        ?>
                    </td>
                </tr>
            </table>
            
            <input type="submit" name="savrala_save_settings" class="button button-primary" value="Save Settings">
        </form>
        
        <h2>Member Management</h2>
        <?php
        $members = get_users(array('role' => 'savrala_member'));
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                <tr>
                    <td><?php echo esc_html($member->display_name); ?></td>
                    <td><?php echo esc_html(get_member_company($member->ID)); ?></td>
                    <td><?php echo esc_html(get_member_status($member->ID)); ?></td>
                    <td>
                        <select onchange="updateMemberStatus(<?php echo $member->ID; ?>, this.value)">
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="resigned">Resigned</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}