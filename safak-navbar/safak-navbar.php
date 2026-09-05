<?php
/**
 * Plugin Name: Safak Navbar
 * Plugin URI: https://safak.ma
 * Description: A premium multilingual navigation bar with logo, menu, and CTA button. Supports Arabic, French, and English.
 * Version: 1.1.1
 * Author: Safak
 * Author URI: https://safak.ma
 * Text Domain: safak-navbar
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SAFAK_NAVBAR_VERSION', '1.1.1' );
define( 'SAFAK_NAVBAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SAFAK_NAVBAR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Plugin Class
 */
class Safak_Navbar {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_body_open', array( $this, 'render_navbar' ), 5 );
        add_shortcode( 'safak_navbar', array( $this, 'shortcode_navbar' ) );
    }

    /**
     * Detect the current language
     * Integrates with Polylang if available, otherwise checks URL prefix
     */
    public function get_current_lang() {
        // Method 1: Polylang integration
        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language( 'slug' );
            if ( $lang ) {
                // Map Polylang slugs to our lang codes
                if ( in_array( $lang, array( 'ar', 'fr', 'en' ), true ) ) {
                    return $lang;
                }
            }
        }

        // Method 2: URL prefix detection
        $request_uri = $_SERVER['REQUEST_URI'];
        $path = parse_url( $request_uri, PHP_URL_PATH );
        $path = $path ? $path : '/';

        if ( preg_match( '#^/fr(/|$)#i', $path ) ) {
            return 'fr';
        }
        if ( preg_match( '#^/en(/|$)#i', $path ) ) {
            return 'en';
        }

        // Method 3: Check locale
        $locale = get_locale();
        if ( strpos( $locale, 'fr' ) === 0 ) {
            return 'fr';
        }
        if ( strpos( $locale, 'en' ) === 0 ) {
            return 'en';
        }

        // Default is Arabic
        return 'ar';
    }

    /**
     * Get text direction based on language
     */
    public function get_text_direction() {
        return $this->get_current_lang() === 'ar' ? 'rtl' : 'ltr';
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'safak-navbar-style',
            SAFAK_NAVBAR_URL . 'assets/css/safak-navbar.css',
            array(),
            SAFAK_NAVBAR_VERSION
        );



        wp_enqueue_script(
            'safak-navbar-script',
            SAFAK_NAVBAR_URL . 'assets/js/safak-navbar.js',
            array(),
            SAFAK_NAVBAR_VERSION,
            true
        );

        wp_localize_script( 'safak-navbar-script', 'safakNavbar', array(
            'lang' => $this->get_current_lang(),
            'dir'  => $this->get_text_direction(),
        ) );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_safak-navbar' !== $hook ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style(
            'safak-navbar-admin-style',
            SAFAK_NAVBAR_URL . 'assets/css/safak-navbar-admin.css',
            array(),
            SAFAK_NAVBAR_VERSION
        );
        wp_enqueue_script(
            'safak-navbar-admin-script',
            SAFAK_NAVBAR_URL . 'assets/js/safak-navbar-admin.js',
            array( 'jquery' ),
            SAFAK_NAVBAR_VERSION,
            true
        );
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Safak Navbar', 'safak-navbar' ),
            __( 'Safak Navbar', 'safak-navbar' ),
            'manage_options',
            'safak-navbar',
            array( $this, 'render_admin_page' ),
            'dashicons-menu-alt3',
            30
        );
    }

    /**
     * Register all settings
     */
    public function register_settings() {
        // Logo
        register_setting( 'safak_navbar_settings', 'safak_navbar_logo' );

        // Menu selection (direct menu IDs)
        register_setting( 'safak_navbar_settings', 'safak_navbar_menu_ar' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_menu_fr' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_menu_en' );

        // Button text (multilingual)
        register_setting( 'safak_navbar_settings', 'safak_navbar_button_text_ar' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_button_text_fr' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_button_text_en' );

        // Button link (multilingual)
        register_setting( 'safak_navbar_settings', 'safak_navbar_button_link_ar' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_button_link_fr' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_button_link_en' );

        // General settings
        register_setting( 'safak_navbar_settings', 'safak_navbar_sticky' );
        register_setting( 'safak_navbar_settings', 'safak_navbar_auto_render' );
    }

    /**
     * Get a multilingual option
     */
    public function get_ml_option( $base_key, $default = '' ) {
        $lang = $this->get_current_lang();
        $value = get_option( $base_key . '_' . $lang, '' );
        if ( empty( $value ) ) {
            // Fallback to Arabic
            $value = get_option( $base_key . '_ar', $default );
        }
        return $value;
    }

    /**
     * Get the selected menu ID for the current language
     */
    public function get_current_menu_id() {
        $lang = $this->get_current_lang();
        $menu_id = get_option( 'safak_navbar_menu_' . $lang, '' );

        // If no menu selected for this language, fallback to Arabic
        if ( empty( $menu_id ) ) {
            $menu_id = get_option( 'safak_navbar_menu_ar', '' );
        }

        return intval( $menu_id );
    }

    /**
     * Render the navbar on the frontend
     */
    public function render_navbar() {
        $auto_render = get_option( 'safak_navbar_auto_render', '1' );
        if ( $auto_render !== '1' ) {
            return;
        }
        echo $this->get_navbar_html();
    }

    /**
     * Shortcode handler
     */
    public function shortcode_navbar( $atts ) {
        return $this->get_navbar_html();
    }

    /**
     * Generate navbar HTML
     */
    public function get_navbar_html() {
        $lang       = $this->get_current_lang();
        $dir        = $this->get_text_direction();
        $logo_id    = get_option( 'safak_navbar_logo', '' );
        $logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
        $btn_text   = $this->get_ml_option( 'safak_navbar_button_text', __( 'ابدأ الآن', 'safak-navbar' ) );
        $btn_link   = $this->get_ml_option( 'safak_navbar_button_link', '#safak-popup' );
        if ( empty( $btn_link ) || '#' === $btn_link ) {
            $btn_link = '#safak-popup';
        }
        $is_sticky  = get_option( 'safak_navbar_sticky', '1' );
        $site_name  = get_bloginfo( 'name' );

        // Get the menu for the current language
        $menu_id = $this->get_current_menu_id();

        // Build the menu HTML
        $menu_html = '';
        if ( $menu_id && is_nav_menu( $menu_id ) ) {
            $menu_html = wp_nav_menu( array(
                'menu'           => $menu_id,
                'container'      => false,
                'menu_class'     => 'safak-navbar__menu-list',
                'echo'           => false,
                'depth'          => 1,
                'fallback_cb'    => false,
            ) );
        }

        // If still empty, use fallback
        if ( empty( $menu_html ) ) {
            $menu_items = $this->get_fallback_menu_items( $lang );
            $menu_html  = '<ul class="safak-navbar__menu-list">';
            foreach ( $menu_items as $item ) {
                $menu_html .= '<li class="menu-item"><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a></li>';
            }
            $menu_html .= '</ul>';
        }

        // Language switcher
        $lang_switcher = $this->get_language_switcher_html( $lang );

        $sticky_class = $is_sticky === '1' ? ' safak-navbar--sticky' : '';

        ob_start();
        ?>
        <nav id="safak-navbar" class="safak-navbar<?php echo esc_attr( $sticky_class ); ?>" dir="<?php echo esc_attr( $dir ); ?>" data-lang="<?php echo esc_attr( $lang ); ?>">
            <div class="safak-navbar__container">
                <!-- Logo -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="safak-navbar__logo">
                    <?php if ( $logo_url ) : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" class="safak-navbar__logo-img" />
                    <?php else : ?>
                        <div class="safak-navbar__logo-icon">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="10" height="10" rx="2" fill="#1A4A72"/>
                                <rect x="16" y="2" width="10" height="10" rx="2" fill="#E30213"/>
                                <rect x="2" y="16" width="10" height="10" rx="2" fill="#E30213"/>
                                <rect x="16" y="16" width="10" height="10" rx="2" fill="#1A4A72"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <span class="safak-navbar__logo-text"><?php echo esc_html( $site_name ); ?></span>
                </a>

                <!-- Center Menu -->
                <div class="safak-navbar__menu-wrapper">
                    <?php echo $menu_html; // phpcs:ignore ?>
                </div>

                <!-- Right Section: Language Switcher + CTA Button -->
                <div class="safak-navbar__actions">
                    <?php echo $lang_switcher; // phpcs:ignore ?>
                    <a href="<?php echo esc_url( $btn_link ); ?>" class="safak-navbar__cta-btn">
                        <?php echo esc_html( $btn_text ); ?>
                    </a>
                </div>

                <!-- Mobile Hamburger -->
                <button class="safak-navbar__hamburger" aria-label="<?php esc_attr_e( 'Toggle Menu', 'safak-navbar' ); ?>" aria-expanded="false">
                    <span class="safak-navbar__hamburger-line"></span>
                    <span class="safak-navbar__hamburger-line"></span>
                    <span class="safak-navbar__hamburger-line"></span>
                </button>
            </div>

            <!-- Mobile Menu Overlay -->
            <div class="safak-navbar__mobile-menu" aria-hidden="true">
                <div class="safak-navbar__mobile-menu-inner">
                    <?php echo $menu_html; // phpcs:ignore ?>
                    <div class="safak-navbar__mobile-actions">
                        <a href="<?php echo esc_url( $btn_link ); ?>" class="safak-navbar__cta-btn safak-navbar__cta-btn--mobile">
                            <?php echo esc_html( $btn_text ); ?>
                        </a>
                        <?php echo $lang_switcher; // phpcs:ignore ?>
                    </div>
                </div>
            </div>
        </nav>
        <?php
        return ob_get_clean();
    }

    /**
     * Fallback menu items when no WordPress menu is selected
     */
    private function get_fallback_menu_items( $lang ) {
        $items = array(
            'ar' => array(
                array( 'title' => 'الرئيسية', 'url' => '/' ),
                array( 'title' => 'من نحن', 'url' => '/about' ),
                array( 'title' => 'خدماتنا', 'url' => '/services' ),
                array( 'title' => 'اتصل بنا', 'url' => '/contact' ),
            ),
            'fr' => array(
                array( 'title' => 'Accueil', 'url' => '/fr/' ),
                array( 'title' => 'À propos', 'url' => '/fr/about' ),
                array( 'title' => 'Services', 'url' => '/fr/services' ),
                array( 'title' => 'Contact', 'url' => '/fr/contact' ),
            ),
            'en' => array(
                array( 'title' => 'Home', 'url' => '/en/' ),
                array( 'title' => 'About', 'url' => '/en/about' ),
                array( 'title' => 'Services', 'url' => '/en/services' ),
                array( 'title' => 'Contact', 'url' => '/en/contact' ),
            ),
        );
        return isset( $items[ $lang ] ) ? $items[ $lang ] : $items['ar'];
    }

    /**
     * Language switcher HTML - integrates with Polylang if available
     */
    private function get_language_switcher_html( $current_lang ) {
        // Flag images using CDN — reliable rendering
        $flag_imgs = array(
            'ar' => '<img class="safak-navbar__lang-flag-img" src="https://flagcdn.com/w40/dz.png" alt="العربية" width="24" height="24" />',
            'fr' => '<img class="safak-navbar__lang-flag-img" src="https://flagcdn.com/w40/fr.png" alt="Français" width="24" height="24" />',
            'en' => '<img class="safak-navbar__lang-flag-img" src="https://flagcdn.com/w40/gb.png" alt="English" width="24" height="24" />',
        );

        $languages = array(
            'ar' => array( 'label' => 'العربية', 'short' => 'Ar' ),
            'fr' => array( 'label' => 'Français', 'short' => 'Fr' ),
            'en' => array( 'label' => 'English', 'short' => 'En' ),
        );

        $current = isset( $languages[ $current_lang ] ) ? $languages[ $current_lang ] : $languages['ar'];
        $current_flag = isset( $flag_imgs[ $current_lang ] ) ? $flag_imgs[ $current_lang ] : $flag_imgs['ar'];

        // Build URLs for language switching
        $urls = array();

        // Method 1: Polylang integration (most reliable)
        if ( function_exists( 'pll_the_languages' ) ) {
            $pll_langs = pll_the_languages( array(
                'raw'                => 1,
                'hide_if_no_translation' => 0,
            ) );

            if ( is_array( $pll_langs ) ) {
                foreach ( $pll_langs as $pll_lang ) {
                    $slug = $pll_lang['slug'];
                    if ( in_array( $slug, array( 'ar', 'fr', 'en' ), true ) ) {
                        $urls[ $slug ] = $pll_lang['url'];
                    }
                }
            }
        }

        // Method 2: Manual URL building fallback
        if ( empty( $urls ) ) {
            $request_uri = $_SERVER['REQUEST_URI'];
            $path  = parse_url( $request_uri, PHP_URL_PATH );
            $query = parse_url( $request_uri, PHP_URL_QUERY );
            $path  = $path ? $path : '/';

            // Strip existing language prefix from path
            $clean_path = preg_replace( '#^/(fr|en)(/|$)#i', '/', $path );
            $clean_path = '/' . ltrim( $clean_path, '/' );

            $query_string = $query ? '?' . $query : '';

            $urls = array(
                'ar' => $clean_path . $query_string,
                'fr' => '/fr' . ( $clean_path === '/' ? '/' : $clean_path ) . $query_string,
                'en' => '/en' . ( $clean_path === '/' ? '/' : $clean_path ) . $query_string,
            );
        }

        ob_start();
        ?>
        <div class="safak-navbar__lang-switcher">
            <button class="safak-navbar__lang-btn" aria-expanded="false" aria-haspopup="true">
                <span class="safak-navbar__lang-flag"><?php echo $current_flag; ?></span>
                <span class="safak-navbar__lang-short"><?php echo esc_html( $current['short'] ); ?></span>
                <svg class="safak-navbar__lang-arrow" width="10" height="6" viewBox="0 0 10 6" fill="none">
                    <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="safak-navbar__lang-dropdown" aria-hidden="true">
                <?php foreach ( $languages as $code => $lang_data ) : ?>
                    <?php if ( $code !== $current_lang && isset( $urls[ $code ] ) ) : ?>
                        <a href="<?php echo esc_url( $urls[ $code ] ); ?>" class="safak-navbar__lang-option" data-lang="<?php echo esc_attr( $code ); ?>">
                            <span class="safak-navbar__lang-flag"><?php echo $flag_imgs[ $code ]; ?></span>
                            <span class="safak-navbar__lang-label"><?php echo esc_html( $lang_data['label'] ); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get all WordPress menus for admin dropdown
     */
    public static function get_all_menus() {
        $menus = wp_get_nav_menus();
        $options = array( '' => __( '-- Select a Menu --', 'safak-navbar' ) );
        foreach ( $menus as $menu ) {
            $options[ $menu->term_id ] = $menu->name;
        }
        return $options;
    }

    /**
     * Render admin settings page
     */
    public function render_admin_page() {
        include SAFAK_NAVBAR_PATH . 'admin/admin-page.php';
    }
}

// Initialize
Safak_Navbar::get_instance();
