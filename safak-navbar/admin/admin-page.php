<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$all_menus = Safak_Navbar::get_all_menus();
?>
<div class="wrap safak-navbar-admin">
    <div class="safak-navbar-admin__header">
        <div class="safak-navbar-admin__header-inner">
            <div class="safak-navbar-admin__title-group">
                <h1 class="safak-navbar-admin__title">
                    <span class="dashicons dashicons-menu-alt3" style="color: #ffffff; font-size: 24px; margin-inline-end: 8px;"></span>
                    <?php esc_html_e( 'Safak Navbar Settings', 'safak-navbar' ); ?>
                </h1>
                <p class="safak-navbar-admin__subtitle"><?php esc_html_e( 'Configure your premium multilingual navigation bar.', 'safak-navbar' ); ?></p>
            </div>
            <span class="safak-navbar-admin__version">v<?php echo esc_html( SAFAK_NAVBAR_VERSION ); ?></span>
        </div>
    </div>

    <form method="post" action="options.php" class="safak-navbar-admin__form">
        <?php settings_fields( 'safak_navbar_settings' ); ?>

        <!-- General Settings -->
        <div class="safak-navbar-admin__card">
            <div class="safak-navbar-admin__card-header">
                <h2 class="safak-navbar-admin__card-title">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php esc_html_e( 'General Settings', 'safak-navbar' ); ?>
                </h2>
            </div>
            <div class="safak-navbar-admin__card-body">
                <div class="safak-navbar-admin__field">
                    <label class="safak-navbar-admin__label">
                        <input type="checkbox" name="safak_navbar_sticky" value="1" <?php checked( get_option( 'safak_navbar_sticky', '1' ), '1' ); ?> />
                        <?php esc_html_e( 'Sticky Navbar (fixed on top while scrolling)', 'safak-navbar' ); ?>
                    </label>
                </div>
                <div class="safak-navbar-admin__field">
                    <label class="safak-navbar-admin__label">
                        <input type="checkbox" name="safak_navbar_auto_render" value="1" <?php checked( get_option( 'safak_navbar_auto_render', '1' ), '1' ); ?> />
                        <?php esc_html_e( 'Auto-render navbar (display automatically on all pages)', 'safak-navbar' ); ?>
                    </label>
                    <p class="safak-navbar-admin__hint"><?php esc_html_e( 'If disabled, use the [safak_navbar] shortcode to display manually.', 'safak-navbar' ); ?></p>
                </div>
            </div>
        </div>

        <!-- Logo Settings -->
        <div class="safak-navbar-admin__card">
            <div class="safak-navbar-admin__card-header">
                <h2 class="safak-navbar-admin__card-title">
                    <span class="dashicons dashicons-format-image"></span>
                    <?php esc_html_e( 'Logo', 'safak-navbar' ); ?>
                </h2>
            </div>
            <div class="safak-navbar-admin__card-body">
                <div class="safak-navbar-admin__field">
                    <label class="safak-navbar-admin__label"><?php esc_html_e( 'Logo Image', 'safak-navbar' ); ?></label>
                    <p class="safak-navbar-admin__hint"><?php esc_html_e( 'Upload your logo. If no logo is set, a default icon with your site name will be used.', 'safak-navbar' ); ?></p>
                    <div class="safak-navbar-admin__media-field">
                        <?php
                        $logo_id  = get_option( 'safak_navbar_logo', '' );
                        $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
                        ?>
                        <input type="hidden" id="safak_navbar_logo" name="safak_navbar_logo" value="<?php echo esc_attr( $logo_id ); ?>" />
                        <div class="safak-navbar-admin__media-preview" id="safak-logo-preview" style="<?php echo $logo_url ? '' : 'display:none;'; ?>">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo" />
                        </div>
                        <div class="safak-navbar-admin__media-buttons">
                            <button type="button" class="button button-primary" id="safak-logo-upload"><?php esc_html_e( 'Upload Logo', 'safak-navbar' ); ?></button>
                            <button type="button" class="button" id="safak-logo-remove" style="<?php echo $logo_url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove Logo', 'safak-navbar' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Selection -->
        <div class="safak-navbar-admin__card">
            <div class="safak-navbar-admin__card-header">
                <h2 class="safak-navbar-admin__card-title">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php esc_html_e( 'Navigation Menu', 'safak-navbar' ); ?>
                </h2>
            </div>
            <div class="safak-navbar-admin__card-body">
                <div class="safak-navbar-admin__info-box">
                    <span class="dashicons dashicons-info" style="color: #1A4A72;"></span>
                    <div>
                        <p><?php esc_html_e( 'Select which WordPress menu to show for each language. Create your menus in Appearance → Menus first.', 'safak-navbar' ); ?></p>
                    </div>
                </div>

                <!-- Arabic Menu -->
                <div class="safak-navbar-admin__lang-group">
                    <h3 class="safak-navbar-admin__lang-title">🇩🇿 <?php esc_html_e( 'Arabic Menu (Default)', 'safak-navbar' ); ?></h3>
                    <div class="safak-navbar-admin__field">
                        <label class="safak-navbar-admin__label" for="safak_navbar_menu_ar"><?php esc_html_e( 'Select Menu', 'safak-navbar' ); ?></label>
                        <select id="safak_navbar_menu_ar" name="safak_navbar_menu_ar" class="regular-text">
                            <?php
                            $selected_ar = get_option( 'safak_navbar_menu_ar', '' );
                            foreach ( $all_menus as $id => $name ) :
                            ?>
                                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $selected_ar, $id ); ?>><?php echo esc_html( $name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- French Menu -->
                <div class="safak-navbar-admin__lang-group">
                    <h3 class="safak-navbar-admin__lang-title">🇫🇷 <?php esc_html_e( 'French Menu', 'safak-navbar' ); ?></h3>
                    <div class="safak-navbar-admin__field">
                        <label class="safak-navbar-admin__label" for="safak_navbar_menu_fr"><?php esc_html_e( 'Select Menu', 'safak-navbar' ); ?></label>
                        <select id="safak_navbar_menu_fr" name="safak_navbar_menu_fr" class="regular-text">
                            <?php
                            $selected_fr = get_option( 'safak_navbar_menu_fr', '' );
                            foreach ( $all_menus as $id => $name ) :
                            ?>
                                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $selected_fr, $id ); ?>><?php echo esc_html( $name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- English Menu -->
                <div class="safak-navbar-admin__lang-group">
                    <h3 class="safak-navbar-admin__lang-title">🇬🇧 <?php esc_html_e( 'English Menu', 'safak-navbar' ); ?></h3>
                    <div class="safak-navbar-admin__field">
                        <label class="safak-navbar-admin__label" for="safak_navbar_menu_en"><?php esc_html_e( 'Select Menu', 'safak-navbar' ); ?></label>
                        <select id="safak_navbar_menu_en" name="safak_navbar_menu_en" class="regular-text">
                            <?php
                            $selected_en = get_option( 'safak_navbar_menu_en', '' );
                            foreach ( $all_menus as $id => $name ) :
                            ?>
                                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $selected_en, $id ); ?>><?php echo esc_html( $name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Button Settings -->
        <div class="safak-navbar-admin__card">
            <div class="safak-navbar-admin__card-header">
                <h2 class="safak-navbar-admin__card-title">
                    <span class="dashicons dashicons-button"></span>
                    <?php esc_html_e( 'CTA Button', 'safak-navbar' ); ?>
                </h2>
            </div>
            <div class="safak-navbar-admin__card-body">
                <!-- Arabic Button -->
                <div class="safak-navbar-admin__lang-group">
                    <h3 class="safak-navbar-admin__lang-title">🇩🇿 <?php esc_html_e( 'Arabic (Default)', 'safak-navbar' ); ?></h3>
                    <div class="safak-navbar-admin__field-row">
                        <div class="safak-navbar-admin__field">
                            <label class="safak-navbar-admin__label" for="safak_navbar_button_text_ar"><?php esc_html_e( 'Button Text', 'safak-navbar' ); ?></label>
                            <input type="text" id="safak_navbar_button_text_ar" name="safak_navbar_button_text_ar" value="<?php echo esc_attr( get_option( 'safak_navbar_button_text_ar', 'ابدأ الآن' ) ); ?>" class="regular-text" dir="rtl" />
                        </div>
                        <div class="safak-navbar-admin__field">
                            <label class="safak-navbar-admin__label" for="safak_navbar_button_link_ar"><?php esc_html_e( 'Button Link', 'safak-navbar' ); ?></label>
                            <input type="text" id="safak_navbar_button_link_ar" name="safak_navbar_button_link_ar" value="<?php echo esc_attr( get_option( 'safak_navbar_button_link_ar', '#safak-popup' ) ); ?>" class="regular-text" placeholder="https:// or #anchor" />
                        </div>
                    </div>
                </div>

                <!-- French Button -->
                <div class="safak-navbar-admin__lang-group">
                    <h3 class="safak-navbar-admin__lang-title">🇫🇷 <?php esc_html_e( 'French', 'safak-navbar' ); ?></h3>
                    <div class="safak-navbar-admin__field-row">
                        <div class="safak-navbar-admin__field">
                            <label class="safak-navbar-admin__label" for="safak_navbar_button_text_fr"><?php esc_html_e( 'Button Text', 'safak-navbar' ); ?></label>
                            <input type="text" id="safak_navbar_button_text_fr" name="safak_navbar_button_text_fr" value="<?php echo esc_attr( get_option( 'safak_navbar_button_text_fr', 'Commencer' ) ); ?>" class="regular-text" />
                        </div>
                        <div class="safak-navbar-admin__field">
                            <label class="safak-navbar-admin__label" for="safak_navbar_button_link_fr"><?php esc_html_e( 'Button Link', 'safak-navbar' ); ?></label>
                            <input type="text" id="safak_navbar_button_link_fr" name="safak_navbar_button_link_fr" value="<?php echo esc_attr( get_option( 'safak_navbar_button_link_fr', '#safak-popup' ) ); ?>" class="regular-text" placeholder="https:// or #anchor" />
                        </div>
                    </div>
                </div>

                <!-- English Button -->
                <div class="safak-navbar-admin__lang-group">
                    <h3 class="safak-navbar-admin__lang-title">🇬🇧 <?php esc_html_e( 'English', 'safak-navbar' ); ?></h3>
                    <div class="safak-navbar-admin__field-row">
                        <div class="safak-navbar-admin__field">
                            <label class="safak-navbar-admin__label" for="safak_navbar_button_text_en"><?php esc_html_e( 'Button Text', 'safak-navbar' ); ?></label>
                            <input type="text" id="safak_navbar_button_text_en" name="safak_navbar_button_text_en" value="<?php echo esc_attr( get_option( 'safak_navbar_button_text_en', 'Get Started' ) ); ?>" class="regular-text" />
                        </div>
                        <div class="safak-navbar-admin__field">
                            <label class="safak-navbar-admin__label" for="safak_navbar_button_link_en"><?php esc_html_e( 'Button Link', 'safak-navbar' ); ?></label>
                            <input type="text" id="safak_navbar_button_link_en" name="safak_navbar_button_link_en" value="<?php echo esc_attr( get_option( 'safak_navbar_button_link_en', '#safak-popup' ) ); ?>" class="regular-text" placeholder="https:// or #anchor" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php submit_button( __( 'Save Settings', 'safak-navbar' ), 'primary large', 'submit', true ); ?>
    </form>
</div>
