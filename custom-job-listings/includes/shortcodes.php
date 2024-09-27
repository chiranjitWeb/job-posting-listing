<?php
function custom_job_shortcode() {
    ob_start();
    ?>
    <div id="job-filters">
        <select id="job_region" onchange="filterJobs()">
            <option value="">Select Region</option>
            <?php
            $job_regions = get_terms('job_region', array('hide_empty' => false));
            foreach ($job_regions as $region) {
                echo '<option value="' . esc_attr($region->slug) . '">' . esc_html($region->name) . '</option>';
            }
            ?>
        </select>
        
        <select id="job_type" onchange="filterJobs()">
            <option value="">Select Type</option>
            <?php
            $job_types = get_terms('job_type', array('hide_empty' => false));
            foreach ($job_types as $type) {
                echo '<option value="' . esc_attr($type->slug) . '">' . esc_html($type->name) . '</option>';
            }
            ?>
        </select>

        <select id="job_designation" onchange="filterJobs()">
            <option value="">All Designations</option>
            <?php
            $designations = get_terms(array('taxonomy' => 'job_designation', 'hide_empty' => false));
            foreach ($designations as $designation) {
                echo '<option value="' . esc_attr($designation->slug) . '">' . esc_html($designation->name) . '</option>';
            }
            ?>
        </select>
    </div>

    <div id="job-listings">
        <?php
        // Load initial jobs
        $all_jobs = new WP_Query(array(
            'post_type' => 'custom_job',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        ));

        // if ($all_jobs->have_posts()) {
        //     while ($all_jobs->have_posts()) : $all_jobs->the_post();
        //         echo '<div>' . get_the_title() . '</div>';
        //     endwhile;
        //     wp_reset_postdata();
        // } else {
        //     echo 'No jobs found.';
        // }
        ?>
    </div>

    <?php if ($all_jobs->found_posts > 5) { ?>
        <div class="text-center">
            <button id="load-more">Load More</button>
        </div>
    <?php } ?>
    
  

    <?php
    return ob_get_clean();
}
add_shortcode('custom_job_listings', 'custom_job_shortcode');
?>
