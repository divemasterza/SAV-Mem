jQuery(document).ready(function($) {
    // Handle profile form submission
    $('.savrala-dashboard form').on('submit', function() {
        $(this).find('button').prop('disabled', true).text('Updating...');
    });
    
    // Preview logo upload
    $('input[name="company_logo"]').on('change', function(e) {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.logo-preview').html('<img src="' + e.target.result + '" style="max-width: 200px;">');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

// Admin member status update
function updateMemberStatus(userId, status) {
    jQuery.post(ajaxurl, {
        action: 'update_member_status',
        user_id: userId,
        status: status,
        nonce: savralaAdmin.nonce
    }, function(response) {
        if (response.success) {
            alert('Member status updated successfully');
        } else {
            alert('Error updating member status');
        }
    });
}