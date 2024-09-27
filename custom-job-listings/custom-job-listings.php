<?php
/**
 * Plugin Name: Custom Job Listings
 * Description: A custom job listings plugin with dynamic filtering.
 * Version: 1.0
 * Author: Your Name
 */

defined( 'ABSPATH' ) or die( 'Direct script access disallowed.' );

include plugin_dir_path(__FILE__) . 'includes/post-types.php';
include plugin_dir_path(__FILE__) . 'includes/enqueue.php';
include plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
include plugin_dir_path(__FILE__) . 'includes/ajax.php';

function custom_job_listings_activate() {
    // Trigger our function that registers the custom post type
    custom_job_register_post_type();
    register_application_post_type();
    // Clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'custom_job_listings_activate' );

function custom_job_listings_deactivate() {
    // Unregister the post type, flush rewrite rules
    unregister_post_type( 'custom_job' );
    unregister_post_type('job_application');
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'custom_job_listings_deactivate' );

function custom_plugin_template_include($template) {
    if (is_singular('custom_job')) {
        $plugin_template =   plugin_dir_path(__FILE__) . '/single-custom_job.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'custom_plugin_template_include', 99);