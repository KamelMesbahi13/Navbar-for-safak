/**
 * Safak Navbar - Admin JavaScript
 * Handles: Media uploader for logo
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        // ===========================
        // Logo Media Uploader
        // ===========================
        var mediaFrame;

        $('#safak-logo-upload').on('click', function (e) {
            e.preventDefault();

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Select Logo',
                button: { text: 'Use as Logo' },
                multiple: false,
                library: { type: 'image' },
            });

            mediaFrame.on('select', function () {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                $('#safak_navbar_logo').val(attachment.id);
                $('#safak-logo-preview').show().find('img').attr('src', attachment.url);
                $('#safak-logo-remove').show();
            });

            mediaFrame.open();
        });

        $('#safak-logo-remove').on('click', function (e) {
            e.preventDefault();
            $('#safak_navbar_logo').val('');
            $('#safak-logo-preview').hide().find('img').attr('src', '');
            $(this).hide();
        });
    });
})(jQuery);
