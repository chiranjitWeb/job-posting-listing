<?php get_header(); ?>
<section class="content-area">
        <div class="roadmap-section">
            <div class="container">
<div class="job-details-container">
    <?php while (have_posts()) : the_post(); 
     $job_id = get_the_ID();
     ?>
        <div class="job-description">
            <h1><?php the_title(); ?></h1>
            <p><strong>Location:</strong> <?php echo get_post_meta(get_the_ID(), 'location', true); ?></p>
            <p><strong>Experience:</strong> <?php echo get_post_meta(get_the_ID(), 'experience', true); ?></p>
            <p><strong>Employment Type:</strong><?php //echo the_terms($post->ID, 'job_type');
            
              $terms = get_the_terms ($post->id, 'job_type');
              if ( !is_wp_error($terms)) : 
                 $skills_links = wp_list_pluck($terms, 'name'); 
                 $skills_yo = implode(", ", $skills_links);
              ?>
              <span><?php echo $skills_yo; ?></span>
             <?php endif; ?></p>
             <p><strong>Compensation:</strong> <?php echo get_post_meta(get_the_ID(), 'compensation', true); ?></p>
            <!-- Add more fields as needed -->
            <div class="job-full-description">
                <?php the_content(); ?>
            </div>
        </div>
        
        <div class="job-application-form">
            <?php echo do_shortcode('[contact-form-7 id="860d058" title="Contact form 1"]'); // Replace with your actual contact form shortcode ?>
        </div>
       
        
    <?php endwhile; ?>


</div>
</div>
</div>
</section>

<?php get_footer(); ?>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var jobIdField = document.querySelector('input[name="job-id"]');
        if (jobIdField) {
            jobIdField.value = '<?php echo $job_id; ?>'; // Populate job ID dynamically
        }
    });
</script>
