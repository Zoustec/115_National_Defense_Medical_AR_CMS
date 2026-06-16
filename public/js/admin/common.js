(function($) {
    'use strict';

    $(document).ready(function() {
        // Password change confirmation using modal
        let passwordConfirmSubmitted = false;
        const $form = $('form');

        $form.on('submit', function(e) {
            if ($('#password').val().length > 0 && !passwordConfirmSubmitted) {
                // Prevent immediate submission and show confirmation modal
                e.preventDefault();
                $('#confirmPasswordChangeModal').modal('show');
                return false;
            }
            // otherwise allow submit to continue
        });

        // When user confirms in modal, set flag and submit the form
        $('#confirmPasswordChangeBtn').on('click', function() {
            passwordConfirmSubmitted = true;
            $('#confirmPasswordChangeModal').modal('hide');
            // submit the form programmatically
            $form.submit();
        });

        // If validation errors exist, focus and scroll to the first invalid field
        const $firstInvalid = $('.is-invalid').first();
        if ($firstInvalid.length) {
            $('html, body').animate({ scrollTop: $firstInvalid.offset().top - 120 }, 300);
            try { $firstInvalid.focus(); } catch(e) {}
        }
    });
})(jQuery);
