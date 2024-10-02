/**
 * Admin JavaScript for Bricks Remote Template Options
 *
 * This script handles all the UI interactions for the Bricks Remote Template Options page.
 */

(function($) {
    $(document).ready(function() {
        // Show sub UI when a main feature button is clicked
        $('.show-sub-ui').on('click', function() {
            var targetUI = $(this).data('target');
            $('#main-ui').addClass('hidden');
            $('#' + targetUI).removeClass('hidden');
        });

        // Return to main UI when "Back to Main Menu" is clicked
        $('.return-to-main').on('click', function() {
            $('.bricks-sub-ui').addClass('hidden');
            $('#main-ui').removeClass('hidden');
        });

        // Handle file input change to show selected filename
        $('input[type="file"]').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.file-name').text(fileName);
        });

        // Confirm delete action
        $('#delete-all-button').on('click', function(e) {
            if (!confirm('Are you sure you want to delete all templates? This action cannot be undone.')) {
                e.preventDefault();
            }
        });

        // Handle AJAX form submissions
        $('.ajax-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData(this);

            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Toggle advanced options
        $('.toggle-advanced').on('click', function() {
            $('.advanced-options').toggleClass('hidden');
            $(this).text(function(i, text) {
                return text === "Show Advanced Options" ? "Hide Advanced Options" : "Show Advanced Options";
            });
        });

        // Initialize tooltips
        $('.tooltip').tooltip();

        // Handle tab navigation in sub UIs
        $('.tab-nav').on('click', 'a', function(e) {
            e.preventDefault();
            var $this = $(this);
            var target = $this.attr('href');

            $this.parent().addClass('active').siblings().removeClass('active');
            $(target).removeClass('hidden').siblings('.tab-content').addClass('hidden');
        });
    });
})(jQuery);