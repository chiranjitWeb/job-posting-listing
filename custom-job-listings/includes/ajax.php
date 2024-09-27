<?php
add_action('wp_ajax_filter_jobs', 'custom_job_filter_jobs');
add_action('wp_ajax_nopriv_filter_jobs', 'custom_job_filter_jobs');
add_action('wp_ajax_load_more_jobs', 'custom_job_load_more_jobs');
add_action('wp_ajax_nopriv_load_more_jobs', 'custom_job_load_more_jobs');

function custom_job_filter_jobs() {
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $region = isset($_POST['region']) ? $_POST['region'] : '';
    $page = isset($_POST['page']) ? $_POST['page'] : 1;

    $args = array(
        'post_type' => 'custom_job',
        'posts_per_page' => 5, // Change the number based on how many jobs you want to show initially
        'paged' => $page,
        'tax_query' => array('relation' => 'AND')
    );

    if (!empty($type)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'job_type',
            'field'    => 'slug',
            'terms'    => $type
        );
    }

    if (!empty($region)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'job_region',
            'field'    => 'slug',
            'terms'    => $region
        );
    }
    // Add designation filter to the query if it's set
if (!empty($_GET['job_designation'])) {
    $args['tax_query'][] = array(
        'taxonomy' => 'job_designation',
        'field'    => 'slug',
        'terms'    => $_GET['job_designation'],
    );
}

    if (empty($type) && empty($region)) {
        unset($args['tax_query']); // Show all jobs if no filter is selected
    }
    
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $terms = get_the_terms ($post->id, 'job_type');
            $skills_links = wp_list_pluck($terms, 'name');

            $experience = get_post_meta(get_the_ID(), 'experience', true); // Example meta field
            $location = get_post_meta(get_the_ID(), 'location', true); // Example meta field
            
           
            // Output each job listing in a card format
            echo '<div class="job-card">';
			echo '<figure><img src="' . site_url() . '/wp-content/uploads/2024/09/jog-img.png" alt="Job Image"></figure>';
            echo '<div class="job-title-experience"><h3>' . get_the_title() . '</h3>';
            echo '<span class="job-experience">' . $experience . '</span></div>';
            echo '<div class="job-location">' . $location . '</div>';
            if ( !is_wp_error($terms)) : 
            echo '<div class="job-type"><span>' . $job_type = implode(", ", $skills_links). '</span></div>';
              endif; 
            echo '<a href="' . get_permalink() . '" class="apply-btn">Apply Now</a>';
            echo '</div>';
        }
    } else {
        echo 'no_jobs';
    }
    wp_die();
}

function custom_job_load_more_jobs() {
    // This function can be similar to custom_job_filter_jobs but designed to only fetch and return the next set of posts.
    // Implement similar logic here, fetching next page of posts.
    
}
