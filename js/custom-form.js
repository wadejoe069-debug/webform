jQuery(document).ready(function($) {
    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.text('Submitting...').prop('disabled', true);
        
        // Clear previous messages
        $('#form-messages').empty();
        
        // Create FormData object for file uploads
        var formData = new FormData(this);
        formData.append('action', 'submit_order_form');
        formData.append('nonce', ajax_object.nonce);
        
        // Submit form via AJAX
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#form-messages').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        response.data +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>'
                    );
                    
                    // Reset form
                    $('#orderForm')[0].reset();
                } else {
                    $('#form-messages').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        response.data +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>'
                    );
                }
            },
            error: function() {
                $('#form-messages').html(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    'An error occurred. Please try again.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );
            },
            complete: function() {
                // Reset button state
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Form validation
    $('#orderForm').on('input', function() {
        var isValid = true;
        
        // Check required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Check email format
        var email = $('#email').val();
        if (email && !isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }
        
        // Enable/disable submit button
        $('button[type="submit"]').prop('disabled', !isValid);
    });
    
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});