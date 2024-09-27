function filterJobs(page = 1) {
    var type = jQuery('#job_type').val();
    var region = jQuery('#job_region').val();

    jQuery.ajax({
        url: customJobAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'filter_jobs',
            type: type,
            region: region,
            page: page
        },
        success: function(response) {
            if (response.trim() === 'no_jobs') {
                
                if (page === 1) {  // Only show this message if it's the first page
                    jQuery('#job-listings').html('<div>No jobs foundrttrt.</div>');
                }
                jQuery('#load-more').hide();  // Hide the Load More button
            } else {
                if (page === 1) {
                    jQuery('#job-listings').html(response);
                } else {
                    jQuery('#job-listings').append(response);
                }
                jQuery('#load-more').show();  // Show the Load More button only if there are jobs
            }
        },
        error: function() {
            console.log('Error retrieving data');  // Log errors to console for debugging
        }
    });
}

jQuery(document).ready(function() {
   
    var page = 1;  // Initialize page counter
    jQuery('#load-more').on('click', function() {
        alert("asasasasas");
        page++;
        filterJobs(page);
    });

    filterJobs();  // Load initial set of jobs
});
