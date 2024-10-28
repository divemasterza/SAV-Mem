<?php
/**
 * Plugin Name: SAVRALA Member Management
 * Description: Handles member management, logins, and certificate generation
 * Version: 1.0.0
 * Author: SAVRALA
 */

if (!defined('ABSPATH')) exit;

// Plugin activation hook
register_activation_hook(__FILE__, 'savrala_plugin_activate');

function savrala_plugin_activate() {
    // Create custom tables
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}savrala_member_details (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        company_name varchar(100),
        company_logo varchar(255),
        website varchar(255),
        status varchar(20) DEFAULT 'active',
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Create member role
    add_role('savrala_member', 'SAVRALA Member', array(
        'read' => true,
        'edit_profile' => true
    ));
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/member-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/certificate-generator.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'savrala_enqueue_scripts');
function savrala_enqueue_scripts() {
    wp_enqueue_style('savrala-styles', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_script('savrala-scripts', plugins_url('assets/js/scripts.js', __FILE__), array('jquery'), '1.0.0', true);
}