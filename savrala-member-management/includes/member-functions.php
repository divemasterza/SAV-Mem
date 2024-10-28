<?php
if (!defined('ABSPATH')) exit;

// Member status update
function savrala_update_member_status($user_id, $status) {
    global $wpdb;
    return $wpdb->update(
        $wpdb->prefix . 'savrala_member_details',
        array('status' => $status),
        array('user_id' => $user_id)
    );
}

// Member profile update
function savrala_update_member_profile() {
    if (!wp_verify_nonce($_POST['savrala_nonce'], 'update_member_profile')) {
        wp_die('Invalid request');
    }

    $user_id = get_current_user_id();
    $company_name = sanitize_text_field($_POST['company_name']);
    $website = esc_url_raw($_POST['website']);
    
    // Handle logo upload
    if (isset($_FILES['company_logo'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $logo_id = media_handle_upload('company_logo', 0);
        if (!is_wp_error($logo_id)) {
            update_user_meta($user_id, 'company_logo', $logo_id);
        }
    }
    
    // Update company details
    global $wpdb;
    $wpdb->update(
        $wpdb->prefix . 'savrala_member_details',
        array(
            'company_name' => $company_name,
            'website' => $website
        ),
        array('user_id' => $user_id)
    );
    
    wp_redirect(add_query_arg('updated', 'true', $_POST['_wp_http_referer']));
    exit;
}
add_action('admin_post_update_member_profile', 'savrala_update_member_profile');

// Member dashboard shortcode
function savrala_member_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        return 'Please log in to access your dashboard.';
    }
    
    $user_id = get_current_user_id();
    $member = get_member_details($user_id);
    
    ob_start();
    ?>
    <div class="savrala-dashboard">
        <h2>Member Dashboard</h2>
        
        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_member_profile">
            <?php wp_nonce_field('update_member_profile', 'savrala_nonce'); ?>
            
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" value="<?php echo esc_attr($member->company_name); ?>">
            </div>
            
            <div class="form-group">
                <label>Website</label>
                <input type="url" name="website" value="<?php echo esc_url($member->website); ?>">
            </div>
            
            <div class="form-group">
                <label>Company Logo</label>
                <input type="file" name="company_logo" accept="image/*">
            </div>
            
            <button type="submit">Update Profile</button>
        </form>
        
        <?php if ($member->status === 'active'): ?>
        <div class="certificate-downloads">
            <h3>Download Certificates</h3>
            <a href="<?php echo esc_url(get_certificate_url('portrait')); ?>" class="button">Download Portrait Certificate</a>
            <a href="<?php echo esc_url(get_certificate_url('landscape')); ?>" class="button">Download Landscape Certificate</a>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('savrala_dashboard', 'savrala_member_dashboard_shortcode');