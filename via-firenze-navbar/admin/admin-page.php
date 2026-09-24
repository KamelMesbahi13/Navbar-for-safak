<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$all_menus = Via_Firenze_Navbar::get_all_menus();
$is_wc_active = function_exists( 'WC' );
wp_enqueue_media();

function via_firenze_opt( $key, $default = '' ) {
    $val = get_option( 'via_firenze_navbar_' . $key );
    return ( false !== $val && '' !== $val ) ? $val : $default;
}
?>
<div class="wrap via-firenze-navbar-admin">
    <div class="via-firenze-navbar-admin__header">
        <div class="via-firenze-navbar-admin__header-inner">
            <div class="via-firenze-navbar-admin__title-group">
                <h1 class="via-firenze-navbar-admin__title">
                    <span class="dashicons dashicons-menu-alt3"></span>
                    <?php esc_html_e( 'Via Firenze Navbar Settings', 'via-firenze-navbar' ); ?>
                </h1>
                <p class="via-firenze-navbar-admin__subtitle"><?php esc_html_e( 'Configure your minimalist luxury Italian navigation bar.', 'via-firenze-navbar' ); ?></p>
            </div>
            <span class="via-firenze-navbar-admin__version">v<?php echo esc_html( VIA_FIRENZE_NAVBAR_VERSION ); ?></span>
        </div>
    </div>

    <form method="post" action="options.php" class="via-firenze-navbar-admin__form">
        <?php settings_fields( 'via_firenze_navbar_settings' ); ?>

        <!-- Brand & Logo -->
        <div class="via-firenze-navbar-admin__card">
            <div class="via-firenze-navbar-admin__card-header">
                <h2 class="via-firenze-navbar-admin__card-title">
                    <span class="dashicons dashicons-format-image"></span>
                    <?php esc_html_e( 'Brand & Logo', 'via-firenze-navbar' ); ?>
                </h2>
            </div>
            <div class="via-firenze-navbar-admin__card-body">
                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label" for="via_firenze_navbar_logo_text"><?php esc_html_e( 'Brand Text (Serif Logo)', 'via-firenze-navbar' ); ?></label>
                    <input type="text" id="via_firenze_navbar_logo_text" name="via_firenze_navbar_logo_text" value="<?php echo esc_attr( via_firenze_opt( 'logo_text', 'VIA FIRENZE' ) ); ?>" class="regular-text" placeholder="VIA FIRENZE" />
                    <p class="via-firenze-navbar-admin__hint"><?php esc_html_e( 'Displayed in high-end serif typography if no image logo is uploaded.', 'via-firenze-navbar' ); ?></p>
                </div>

                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label"><?php esc_html_e( '1. Default Logo - White (For Transparent Navbar)', 'via-firenze-navbar' ); ?></label>
                    <p class="via-firenze-navbar-admin__hint"><?php esc_html_e( 'Upload your white logo to display when the navbar is transparent over the hero.', 'via-firenze-navbar' ); ?></p>
                    <div class="via-firenze-navbar-admin__media-field">
                        <?php
                        $logo_id  = via_firenze_opt( 'logo', '' );
                        $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
                        ?>
                        <input type="hidden" id="via_firenze_navbar_logo" name="via_firenze_navbar_logo" value="<?php echo esc_attr( $logo_id ); ?>" />
                        <div class="via-firenze-navbar-admin__media-preview" id="via-firenze-logo-preview" style="<?php echo $logo_url ? '' : 'display:none;'; ?>">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Default Logo" style="max-height: 48px; background: #000; padding: 6px; border-radius: 4px;" />
                        </div>
                        <div class="via-firenze-navbar-admin__media-buttons">
                            <button type="button" class="button button-primary" id="via-firenze-logo-upload"><?php esc_html_e( 'Upload White Logo', 'via-firenze-navbar' ); ?></button>
                            <button type="button" class="button" id="via-firenze-logo-remove" style="<?php echo $logo_url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'via-firenze-navbar' ); ?></button>
                        </div>
                    </div>
                </div>

                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label"><?php esc_html_e( '2. Scrolled Logo - Black (For White Scrolled Navbar)', 'via-firenze-navbar' ); ?></label>
                    <p class="via-firenze-navbar-admin__hint"><?php esc_html_e( 'Upload your black/dark logo to display when the user scrolls down and the navbar turns white.', 'via-firenze-navbar' ); ?></p>
                    <div class="via-firenze-navbar-admin__media-field">
                        <?php
                        $logo_scrolled_id  = via_firenze_opt( 'logo_scrolled', '' );
                        $logo_scrolled_url = $logo_scrolled_id ? wp_get_attachment_image_url( $logo_scrolled_id, 'medium' ) : '';
                        ?>
                        <input type="hidden" id="via_firenze_navbar_logo_scrolled" name="via_firenze_navbar_logo_scrolled" value="<?php echo esc_attr( $logo_scrolled_id ); ?>" />
                        <div class="via-firenze-navbar-admin__media-preview" id="via-firenze-logo-scrolled-preview" style="<?php echo $logo_scrolled_url ? '' : 'display:none;'; ?> background: #fff;">
                            <img src="<?php echo esc_url( $logo_scrolled_url ); ?>" alt="Scrolled Logo" style="max-height: 48px; background: #fff; padding: 6px; border-radius: 4px;" />
                        </div>
                        <div class="via-firenze-navbar-admin__media-buttons">
                            <button type="button" class="button button-primary" id="via-firenze-logo-scrolled-upload"><?php esc_html_e( 'Upload Black Logo', 'via-firenze-navbar' ); ?></button>
                            <button type="button" class="button" id="via-firenze-logo-scrolled-remove" style="<?php echo $logo_scrolled_url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'via-firenze-navbar' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="via-firenze-navbar-admin__card">
            <div class="via-firenze-navbar-admin__card-header">
                <h2 class="via-firenze-navbar-admin__card-title">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php esc_html_e( 'Navigation Menu', 'via-firenze-navbar' ); ?>
                </h2>
            </div>
            <div class="via-firenze-navbar-admin__card-body">
                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label" for="via_firenze_navbar_menu"><?php esc_html_e( 'Select WordPress Menu', 'via-firenze-navbar' ); ?></label>
                    <select id="via_firenze_navbar_menu" name="via_firenze_navbar_menu" class="regular-text">
                        <?php
                        $selected_menu = via_firenze_opt( 'menu', '' );
                        foreach ( $all_menus as $id => $name ) :
                        ?>
                            <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $selected_menu, $id ); ?>><?php echo esc_html( $name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="via-firenze-navbar-admin__hint"><?php esc_html_e( 'Select the menu to display in the center. Default preset renders: UOMO, DONNA, BAMBINI, THE STUDIO, SCOPRI.', 'via-firenze-navbar' ); ?></p>
                </div>
            </div>
        </div>

        <!-- E-Commerce & Actions -->
        <div class="via-firenze-navbar-admin__card">
            <div class="via-firenze-navbar-admin__card-header">
                <h2 class="via-firenze-navbar-admin__card-title">
                    <span class="dashicons dashicons-cart"></span>
                    <?php esc_html_e( 'E-Commerce & Actions (Search & Cart)', 'via-firenze-navbar' ); ?>
                </h2>
            </div>
            <div class="via-firenze-navbar-admin__card-body">
                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label">
                        <input type="checkbox" name="via_firenze_navbar_enable_search" value="1" <?php checked( via_firenze_opt( 'enable_search', '1' ), '1' ); ?> />
                        <?php esc_html_e( 'Enable Search Icon (magnifying glass with sleek overlay)', 'via-firenze-navbar' ); ?>
                    </label>
                </div>

                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label" for="via_firenze_navbar_cart_url"><?php esc_html_e( 'Shopping Bag / Cart URL', 'via-firenze-navbar' ); ?></label>
                    <input type="text" id="via_firenze_navbar_cart_url" name="via_firenze_navbar_cart_url" value="<?php echo esc_attr( via_firenze_opt( 'cart_url', '' ) ); ?>" class="regular-text" placeholder="<?php echo $is_wc_active ? esc_attr( wc_get_cart_url() ) : '/cart/'; ?>" />
                    <p class="via-firenze-navbar-admin__hint">
                        <?php if ( $is_wc_active ) : ?>
                            <span style="color: #10b981; font-weight: 600;">✓ <?php esc_html_e( 'WooCommerce detected: Cart link and live AJAX badge counts are automatically connected.', 'via-firenze-navbar' ); ?></span>
                        <?php else : ?>
                            <?php esc_html_e( 'Enter your custom cart page link (e.g. /cart/).', 'via-firenze-navbar' ); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Appearance & Layout -->
        <div class="via-firenze-navbar-admin__card">
            <div class="via-firenze-navbar-admin__card-header">
                <h2 class="via-firenze-navbar-admin__card-title">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php esc_html_e( 'Appearance & Behavior', 'via-firenze-navbar' ); ?>
                </h2>
            </div>
            <div class="via-firenze-navbar-admin__card-body">
                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label" for="via_firenze_navbar_style"><?php esc_html_e( 'Header Style', 'via-firenze-navbar' ); ?></label>
                    <select id="via_firenze_navbar_style" name="via_firenze_navbar_style" class="regular-text">
                        <option value="transparent" <?php selected( via_firenze_opt( 'style', 'transparent' ), 'transparent' ); ?>><?php esc_html_e( 'Transparent (Floats seamlessly over Hero / Content)', 'via-firenze-navbar' ); ?></option>
                        <option value="black" <?php selected( via_firenze_opt( 'style', 'transparent' ), 'black' ); ?>><?php esc_html_e( 'Solid Black (Classic Dark Mode #000000)', 'via-firenze-navbar' ); ?></option>
                    </select>
                </div>

                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label">
                        <input type="checkbox" name="via_firenze_navbar_sticky" value="1" <?php checked( via_firenze_opt( 'sticky', '1' ), '1' ); ?> />
                        <?php esc_html_e( 'Sticky Header (fixed on top while scrolling)', 'via-firenze-navbar' ); ?>
                    </label>
                </div>

                <div class="via-firenze-navbar-admin__field">
                    <label class="via-firenze-navbar-admin__label">
                        <input type="checkbox" name="via_firenze_navbar_auto_render" value="1" <?php checked( via_firenze_opt( 'auto_render', '1' ), '1' ); ?> />
                        <?php esc_html_e( 'Auto-render navbar (automatically replace theme header on all pages)', 'via-firenze-navbar' ); ?>
                    </label>
                    <p class="via-firenze-navbar-admin__hint"><?php esc_html_e( 'If disabled, you can place it manually anywhere using the [via_firenze_navbar] shortcode.', 'via-firenze-navbar' ); ?></p>
                </div>
            </div>
        </div>

        <?php submit_button( __( 'Save Via Firenze Navbar Settings', 'via-firenze-navbar' ), 'primary large', 'submit', true ); ?>
    </form>
</div>

<script type="text/javascript">
(function() {
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;

        function attachUploader(btnId, removeBtnId, inputId, previewId, title) {
            var frame;

            $(document).on('click', btnId, function(e) {
                e.preventDefault();

                if (frame) {
                    frame.open();
                    return;
                }

                if (typeof wp === 'undefined' || !wp.media) {
                    alert('WordPress Media Library is loading. Please try again in a moment or refresh the page.');
                    return;
                }

                frame = wp.media({
                    title: title,
                    button: { text: 'Use as Logo' },
                    multiple: false,
                    library: { type: 'image' }
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $(inputId).val(attachment.id);
                    $(previewId).show().find('img').attr('src', attachment.url);
                    $(removeBtnId).show();
                });

                frame.open();
            });

            $(document).on('click', removeBtnId, function(e) {
                e.preventDefault();
                $(inputId).val('');
                $(previewId).hide().find('img').attr('src', '');
                $(this).hide();
            });
        }

        attachUploader(
            '#via-firenze-logo-upload',
            '#via-firenze-logo-remove',
            '#via_firenze_navbar_logo',
            '#via-firenze-logo-preview',
            'Select White Logo (For Transparent Header)'
        );

        attachUploader(
            '#via-firenze-logo-scrolled-upload',
            '#via-firenze-logo-scrolled-remove',
            '#via_firenze_navbar_logo_scrolled',
            '#via-firenze-logo-scrolled-preview',
            'Select Black Logo (For White Scrolled Header)'
        );
    });
})();
</script>

