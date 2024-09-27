<?php
function custom_job_enqueue_scripts() {
    wp_enqueue_script('custom-job-ajax', plugin_dir_url(__FILE__) . 'js/custom-job-ajax.js', array('jquery'), null, true);
    wp_localize_script('custom-job-ajax', 'customJobAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_style('custom-job-css', plugin_dir_url(__FILE__) . 'css/custom-job-styles.css');
}
add_action('wp_enqueue_scripts', 'custom_job_enqueue_scripts');


// function enqueue_custom_job_scripts() {
//     wp_enqueue_script('custom-job-ajax-js', get_template_directory_uri() . '/includes/js/custom-job-ajax.js', array('jquery'), null, true);
//     wp_localize_script('custom-job-ajax-js', 'customJobAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
// }

// add_action('wp_enqueue_scripts', 'enqueue_custom_job_scripts');



