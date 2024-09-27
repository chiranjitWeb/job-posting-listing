<?php
function custom_job_register_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Jobs',
        'supports' => array('title', 'editor', 'custom-fields'),
        'show_in_rest' => true,
    );
    register_post_type('custom_job', $args);

    register_taxonomy('job_type', 'custom_job', array(
        'label' => 'Job Types',
        'hierarchical' => true,
        'show_in_rest' => true
    ));

    register_taxonomy('job_region', 'custom_job', array(
        'label' => 'Job Regions',
        'hierarchical' => true,
        'show_in_rest' => true
    ));
    register_taxonomy('job_designation', 'custom_job', array(
        'label' => 'Job Designations',
        'hierarchical' => true,
        'show_in_rest' => true, // For Gutenberg support
    ));
}
add_action('init', 'custom_job_register_post_type');
// Register the meta box for job applications within the job post type


function custom_add_job_meta_boxes() {
    add_meta_box(
        'job_details_meta_box',       // ID of the meta box
        'Job Details',                // Title of the meta box
        'custom_display_job_meta_box', // Callback function to display the meta box
        'custom_job',                 // Post type
        'normal',                     // Context where the box will show
        'high'                        // Priority of the box
    );
}

add_action('add_meta_boxes', 'custom_add_job_meta_boxes');

function custom_display_job_meta_box($post) {
    // Adding a nonce field for security
    wp_nonce_field('custom_save_job_meta_box_data', 'custom_job_meta_box_nonce');

    // Retrieve current values of meta fields
    $experience = get_post_meta($post->ID, 'experience', true);
    $location = get_post_meta($post->ID, 'location', true);
    $compensation = get_post_meta($post->ID, 'compensation', true);

    ?>
     <p>
         <label for="experience">Experience:</label>
         <input type="text" id="experience" name="experience" value="<?php echo esc_attr($experience); ?>" class="widefat">
   </p>
    <p>
         <label for="location">Location:</label>
         <input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" class="widefat">
    </p>
    <p>
        <label for="location">Compensation:</label>
        <input type="text" id="compensation" name="compensation" value="<?php echo esc_attr($compensation); ?>" class="widefat">
    </p>
    <?php
}

function custom_save_job_meta_box_data($post_id) {
    // Check if nonce is set and is valid
    if (!isset($_POST['custom_job_meta_box_nonce']) || !wp_verify_nonce($_POST['custom_job_meta_box_nonce'], 'custom_save_job_meta_box_data')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permission
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Sanitize and update the meta data
    if (isset($_POST['experience'])) {
        update_post_meta($post_id, 'experience', sanitize_text_field($_POST['experience']));
    }
    if (isset($_POST['location'])) {
        update_post_meta($post_id, 'location', sanitize_text_field($_POST['location']));
    }
    if (isset($_POST['compensation'])) {
        update_post_meta($post_id, 'compensation', sanitize_text_field($_POST['compensation']));
    }
}
add_action('save_post_custom_job', 'custom_save_job_meta_box_data');



function register_application_post_type() {
    $args = array(
        'public' => true,
        'has_archive' => true,
        'label' => 'Applications',
        'supports' => array('title', 'editor', 'custom-fields'),
        'show_in_rest' => true,
    );
    register_post_type('job_application', $args);
}

add_action('init', 'register_application_post_type');


/**
 * This filter modifies "my_post_type" post rows,
 * such as "Edit", "Quick Edit" and "Trash".
 *
 * @param $actions
 * @param $post
 *
 * @return mixed
 */
add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );
function remove_row_actions( $actions, $post ) {
    // Check if the post type is 'job_application'
    if ( 'job_application' === $post->post_type ) {
        unset( $actions['edit'] );
        unset( $actions['view'] );
        //unset( $actions['trash'] );
        unset( $actions['inline hide-if-no-js'] );
    }
    return $actions;
}
// add_filter( 'post_type_link', 'remove_permalink', 10, 2 );
// function remove_permalink( $permalink, $post ) {
//     // Check if the post type is 'job_application'
//     if ( 'job_application' === $post->post_type ) {
//         return ''; // Return an empty string to hide the permalink
//     }
//     return $permalink;
// }

add_action('admin_footer', function() {
    global $post_type;
    if ($post_type === 'job_application') {
      echo '<script> document.getElementById("edit-slug-box").outerHTML = ""; </script>';
    }
  });
add_action( 'init', function() {
    remove_post_type_support( 'job_application', 'editor' );
}, 99);

// Add meta box for job application details
add_action('add_meta_boxes', 'job_application_details_meta_box');

function job_application_details_meta_box() {
    add_meta_box(
        'job_application_details',          // Meta box ID
        'Application Details',              // Meta box title
        'display_job_application_details',  // Callback to display content
        'job_application',                  // Post type
        'normal',                           // Context
        'high'                              // Priority
    );
}

// Display the content in the meta box
function display_job_application_details($post) {
    // Get the meta data
    $applicant_name = get_post_meta($post->ID, 'applicant_name', true);
    $applicant_email = get_post_meta($post->ID, 'applicant_email', true);
    $job_id = get_post_meta($post->ID, 'job_id', true);
    $applicant_dob = get_post_meta($post->ID, 'applicant_dob', true);
    $cv_file = get_post_meta($post->ID, 'cv_file', true);
    $linkedin_id = get_post_meta($post->ID, 'linkedin_id', true);
    $total_exp = get_post_meta($post->ID, 'total_experience', true);  // Retrieve dropdown value
    $relevant_exp = get_post_meta($post->ID, 'relevant_experience', true);  // Retrieve dropdown value
    $skills = get_post_meta($post->ID, 'skills', true);  // Retrieve textarea value

    // Get job title if job ID is available
    $job_title = $job_id ? get_the_title($job_id) : 'N/A';

    // Display the data
    echo '<p><strong>Applicant Name:</strong> ' . esc_html($applicant_name) . '</p>';
    echo '<p><strong>Applicant Email:</strong> ' . esc_html($applicant_email) . '</p>';
    echo '<p><strong>Applied for Job:</strong> ' . esc_html($job_title) . ' (' . esc_html($job_id) . ')</p>';
    echo '<p><strong>Applicant DOB:</strong> ' . esc_html($applicant_dob) . '</p>';
    echo '<p><strong>LinkedIn ID:</strong> ' . esc_html($linkedin_id) . '</p>';
    echo '<p><strong>Total Experience:</strong> ' . esc_html($total_exp) . '</p>';  // Display dropdown value
    echo '<p><strong>Relevant Experience:</strong> ' . esc_html($relevant_exp) . '</p>';  // Display dropdown value
    echo '<p><strong>Skills:</strong> ' . nl2br(esc_html($skills)) . '</p>';  // Display textarea value with line breaks

    // Display CV link if it exists and is a string
    if (!empty($cv_file) && is_string($cv_file)) {
        echo '<p><strong>CV:</strong> <a href="' . esc_url($cv_file) . '" target="_blank">Download CV</a></p>';
    } else {
        echo '<p><strong>CV:</strong> No CV uploaded.</p>';
    }
}

// Make job application title clickable and go to edit page
// function make_job_application_editable($column, $post_id) {
//     if ($column == 'title') {
//         $edit_link = get_edit_post_link($post_id);
//         echo '<a href="' . $edit_link . '">' . get_the_title($post_id) . '</a>';
//     }
// }
// add_filter('post_row_actions', 'make_job_application_editable', 10, 2);

// function handle_job_application_submission() {
//     if (isset($_POST['submit_application'])) {
//         $job_id = sanitize_text_field($_POST['job_id']); // Assuming job_id is passed in the form
//         $applicant_data = array(
//             'name' => sanitize_text_field($_POST['name']),
//             'email' => sanitize_email($_POST['email']),
//             'resume_url' => esc_url_raw($_POST['resume_url']),
//         );

//         $post_data = array(
//             'post_title' => $applicant_data['name'],
//             'post_status' => 'publish',
//             'post_type' => 'job_application',
//             'meta_input' => array(
//                 'email' => $applicant_data['email'],
//                 'resume' => $applicant_data['resume_url'],
//                 'job_id' => $job_id,
//             ),
//         );

//         $post_id = wp_insert_post($post_data);
//         if ($post_id) {
//             // Redirect or send a success response
//         }
//     }
// }

function custom_mime_types($mimes) {
    // Allow DOC and DOCX file types
    $mimes['doc'] = 'application/msword';
    $mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $mimes['pdf'] = 'application/pdf';
    return $mimes;
}
add_filter('upload_mimes', 'custom_mime_types');




add_action('wpcf7_before_send_mail', 'process_application_data');

/**
 * Main function to process the application data.
 * Calls the file upload and post insertion functions.
 */
function process_application_data($contact_form) {
    $submission = WPCF7_Submission::get_instance();

    if ($submission) {
        $posted_data = $submission->get_posted_data();
        $cv_file = $_FILES['cv-file'];
        $upload_dir = wp_upload_dir();
        $custom_dir = $upload_dir['basedir'] . '/applications';

        // Create the directory if it doesn't exist
        if (!file_exists($custom_dir)) {
            mkdir($custom_dir, 0755, true); // Create directory with permissions 0755
        }

        // Generate a unique filename
       // $file_name = time() . '-' . basename($cv_file['name']);
       $file_name = basename($cv_file['name']);
        $new_file_path = $custom_dir . '/' . $file_name;
       // move_uploaded_file($cv_file['tmp_name'], $new_file_path);
       $uploaded_file_url= esc_url($upload_dir['baseurl'] . '/applications/' . $file_name);
       attach_cv_to_email($contact_form, $new_file_path);
       insert_application_post($posted_data, $uploaded_file_url);
       

     
       
    }
}
add_action('wpcf7_before_send_mail', 'attach_cv_to_email', 10, 2);

function attach_cv_to_email($contact_form, $result) {
    // Get the current mail properties
    $submission = WPCF7_Submission::get_instance();
    $cv_file = $_FILES['cv-file'];
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/applications';
    $file_name = basename($cv_file['name']);
    $new_file_path = $custom_dir . '/' . $file_name;
    $mail = $contact_form->prop('mail');
    
    if ( ! isset( $mail['attachments'] ) || ! is_array( $mail['attachments'] ) ) {
        $mail['attachments'] = array(); // Initialize as an array if not already
    }
    $mail['attachments'] = $new_file_path;
    //print_r($mail);die();
    $contact_form->set_properties(array('mail' => $mail));
     // if ( isset( $mail['attachments'] ) ) {
    //     echo '';
    //     $mail['attachments'][] = $file_path; // Attach file to the existing attachments
    // } else {
    //     $mail['attachments'] = array( $file_path ); // Create the attachments array if it doesn't exist
    // }
    
}




/**
 * Function to handle file upload.
 * @param string $file_input_name The name of the file input in the form.
 * @return string|bool The URL of the uploaded file, or false on failure.
 */

// Hook into wpcf7_mail_sent to trigger the file upload after email is sent
add_action('init', 'upload_cv_file_after_submission', 10, 1);

function upload_cv_file_after_submission($contact_form) {
    // Get form submission data
    $cv_file = $_FILES['cv-file'];
       // print_r($cv_file);die();
    
        // Ensure there is a file and no error during upload
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Set up WordPress uploads directory
            $upload_dir = wp_upload_dir();
            $custom_dir = $upload_dir['basedir'] . '/applications';
    
            // Create the directory if it doesn't exist
            if (!file_exists($custom_dir)) {
                mkdir($custom_dir, 0755, true); // Create directory with permissions 0755
            }
    
            // Generate a unique filename
           // $file_name = time() . '-' . basename($cv_file['name']);
            $file_name = basename($cv_file['name']);
            $new_file_path = $custom_dir . '/' . $file_name;
           
            // Move the uploaded file to the custom directory
            if (move_uploaded_file($cv_file['tmp_name'], $new_file_path)) {
                // Return the URL of the uploaded file
                
                //return esc_url($upload_dir['baseurl'] . '/applications/' . $file_name);
            } else {
                error_log('Failed to move uploaded file.');
                return false;
            }
        } else {
            error_log('No file uploaded or file upload error: ' . $cv_file['error']);
            return false;
        }
}

/**
 * Function to insert the application post into WordPress.
 * @param array $posted_data The form data submitted.
 * @param string $file_url The URL of the uploaded CV file.
 */
function insert_application_post($posted_data, $file_url) {
    //print_r($posted_data);die();
    $applicant_name = isset($posted_data['text-571']) ? sanitize_text_field($posted_data['text-571']) : 'Unknown';
    $applicant_email = isset($posted_data['email-416']) ? sanitize_email($posted_data['email-416']) : '';
    $applicant_dob = isset($posted_data['datepicker-815']) ? sanitize_text_field($posted_data['datepicker-815']) : '';
    $linkedin_id = isset($posted_data['linkdin']) ? sanitize_text_field($posted_data['linkdin']) : '';
    
    // If the dropdown values are arrays, implode them into comma-separated strings
    //$total_exp = isset($posted_data['menu-846']) ? implode(', ', $posted_data['menu-846']) : '';  // Dropdown for total experience
    $total_exp = isset($posted_data['menu-846']) ?  $posted_data['menu-846']  :'';  // Dropdown for total experience
    //$relevant_exp = isset($posted_data['menu-exp']) ? implode(', ', $posted_data['menu-exp']) : '';  // Dropdown for relevant experience
    $relevant_exp = isset($posted_data['menu-exp']) ? $posted_data['menu-exp'] : ''; 

    $skills = isset($posted_data['textarea-skill']) ? sanitize_textarea_field($posted_data['textarea-skill']) : '';  // Textarea for skills

    // Prepare post data for insertion
    $post_data = array(
        'post_title'   => 'Application from ' . $applicant_name,
        'post_status'  => 'publish',
        'post_type'    => 'job_application',
        'meta_input'   => array(
            'job_id'             => isset($posted_data['job-id']) ? sanitize_text_field($posted_data['job-id']) : '',
            'applicant_name'     => $applicant_name,
            'applicant_email'    => $applicant_email,
            'applicant_dob'      => $applicant_dob,
            'linkedin_id'        => $linkedin_id,
            'total_experience'   => $total_exp,  // Store dropdown values as comma-separated strings
            'relevant_experience'=> $relevant_exp,  // Store dropdown values as comma-separated strings
            'skills'             => $skills,  // Store textarea value
            'cv_file'            => esc_url_raw($file_url),  // Store the uploaded file URL in the post meta
        ),
    );

    // Insert the post into the WordPress database
    $post_id = wp_insert_post($post_data);

    // Check if the post was inserted successfully
    if (!is_wp_error($post_id)) {
        error_log('Application post created successfully: ' . $post_id);
    } else {
        error_log('Failed to create application post: ' . $post_id->get_error_message());
    }
}





// Add custom columns to the Application listing
add_filter('manage_job_application_posts_columns', 'add_application_columns');
function add_application_columns($columns) {
    // Unset the default 'title' and 'date' columns if you don't need them
    unset($columns['date']);
    
    // Add new custom columns
    $columns['applicant_name'] = __('Applicant Name');
    $columns['job_id'] = __('Job Title');
    $columns['application_date'] = __('Application Date');
    
    return $columns;
}

// Populate custom columns with data
add_action('manage_job_application_posts_custom_column', 'custom_application_column', 10, 2);
function custom_application_column($column, $post_id) {
    switch ($column) {
        case 'applicant_name':
            // Get the applicant's name from post meta
            $applicant_name = get_post_meta($post_id, 'applicant_name', true);
            echo esc_html($applicant_name);
            break;
        
        case 'job_id':
            // Get the job ID from post meta
            $job_id = get_post_meta($post_id, 'job_id', true);
            if ($job_id) {
                // Display the job title linked to the job post
                $job_title = get_the_title($job_id);
                echo '<a href="' . get_edit_post_link($post_id) . '">' . esc_html($job_title) . '</a>';
            } else {
                echo __('No Job Linked', 'text-domain');
            }
            break;
            
        case 'application_date':
            // Display the application date
            echo get_the_date('Y-m-d', $post_id);
            break;
    }
}


 add_action('manage_job_application_posts_custom_column', 'custom_application_column', 10, 2);


 






