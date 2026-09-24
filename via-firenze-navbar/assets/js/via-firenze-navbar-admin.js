/**
 * Via Firenze Navbar - Admin JavaScript
 * Handles: Media uploaders for default logo and scrolled logo
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        function initMediaUploader(uploadBtnSelector, removeBtnSelector, inputSelector, previewSelector, titleText) {
            var $uploadBtn = $(uploadBtnSelector);
            var $removeBtn = $(removeBtnSelector);
            var $input = $(inputSelector);
            var $preview = $(previewSelector);
            var frame;

            $uploadBtn.on('click', function (e) {
                e.preventDefault();

                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: titleText || 'Select Logo',
                    button: { text: 'Use as Logo' },
                    multiple: false,
                    library: { type: 'image' },
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.id);
                    $preview.show().find('img').attr('src', attachment.url);
                    $removeBtn.show();
                });

                frame.open();
            });

            $removeBtn.on('click', function (e) {
                e.preventDefault();
                $input.val('');
                $preview.hide().find('img').attr('src', '');
                $(this).hide();
            });
        }

        // Initialize Default (White) Logo Uploader
        initMediaUploader(
            '#via-firenze-logo-upload',
            '#via-firenze-logo-remove',
            '#via_firenze_navbar_logo',
            '#via-firenze-logo-preview',
            'Select White / Transparent Header Logo'
        );

        // Initialize Scrolled (Black) Logo Uploader
        initMediaUploader(
            '#via-firenze-logo-scrolled-upload',
            '#via-firenze-logo-scrolled-remove',
            '#via_firenze_navbar_logo_scrolled',
            '#via-firenze-logo-scrolled-preview',
            'Select Black / Scrolled Header Logo'
        );

    });
})(jQuery);
