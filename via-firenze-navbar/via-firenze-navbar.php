<?php
/**
 * Plugin Name: Via Firenze Navbar
 * Plugin URI: https://viafirenze.com
 * Description: Luxury minimalist navigation bar styled after high-end Italian fashion houses. Features brand logo, uppercase menu, live search overlay, and integrated WooCommerce cart.
 * Version: 2.0.2
 * Author: Via Firenze
 * Author URI: https://viafirenze.com
 * Text Domain: via-firenze-navbar
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'VIA_FIRENZE_NAVBAR_VERSION', '2.0.2' );
define( 'VIA_FIRENZE_NAVBAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'VIA_FIRENZE_NAVBAR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Plugin Class
 */
class Via_Firenze_Navbar {

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
        add_shortcode( 'via_firenze_navbar', array( $this, 'shortcode_navbar' ) );

        // Live WooCommerce Cart count AJAX fragment update
        add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_count_fragments' ) );
    }

    /**
     * Enqueue frontend styles and scripts
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'via-firenze-navbar-style',
            VIA_FIRENZE_NAVBAR_URL . 'assets/css/via-firenze-navbar.css',
            array(),
            time()
        );

        wp_enqueue_script(
            'via-firenze-navbar-script',
            VIA_FIRENZE_NAVBAR_URL . 'assets/js/via-firenze-navbar.js',
            array(),
            time(),
            true
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        // Broad check for our settings page (handles various hook formats)
        $is_our_page = ( false !== strpos( $hook, 'via-firenze' ) )
                    || ( isset( $_GET['page'] ) && false !== strpos( sanitize_text_field( $_GET['page'] ), 'via-firenze' ) );
        if ( ! $is_our_page ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style(
            'via-firenze-navbar-admin-style',
            VIA_FIRENZE_NAVBAR_URL . 'assets/css/via-firenze-navbar-admin.css',
            array(),
            time()
        );
        wp_enqueue_script(
            'via-firenze-navbar-admin-script',
            VIA_FIRENZE_NAVBAR_URL . 'assets/js/via-firenze-navbar-admin.js',
            array( 'jquery' ),
            time(),
            true
        );
    }

    /**
     * Register Admin Page
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Via Firenze Navbar', 'via-firenze-navbar' ),
            __( 'Via Firenze Navbar', 'via-firenze-navbar' ),
            'manage_options',
            'via-firenze-navbar',
            array( $this, 'render_admin_page' ),
            'dashicons-menu-alt3',
            30
        );
    }

    /**
     * Register settings in WP Options
     */
    public function register_settings() {
        // Logo settings
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_logo' );
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_logo_scrolled' );
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_logo_text' );

        // Menu selection
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_menu' );

        // Cart settings
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_cart_url' );

        // Search settings
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_enable_search' );

        // Visual and layout settings
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_sticky' );
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_style' );
        register_setting( 'via_firenze_navbar_settings', 'via_firenze_navbar_auto_render' );
    }

    /**
     * Helper to read setting
     */
    public function get_setting( $key, $default = '' ) {
        $val = get_option( 'via_firenze_navbar_' . $key );
        return ( false !== $val && '' !== $val ) ? $val : $default;
    }

    /**
     * Get the configured or WooCommerce Cart URL
     */
    public function get_cart_url() {
        $custom_url = $this->get_setting( 'cart_url', '' );
        if ( ! empty( $custom_url ) ) {
            return $custom_url;
        }

        if ( function_exists( 'wc_get_cart_url' ) ) {
            return wc_get_cart_url();
        }

        return home_url( '/cart/' );
    }

    /**
     * Get current cart item count
     */
    public function get_cart_count() {
        if ( function_exists( 'WC' ) && WC()->cart ) {
            return WC()->cart->get_cart_contents_count();
        }
        return 0;
    }

    /**
     * Update cart badge dynamically via WooCommerce AJAX fragments
     */
    public function cart_count_fragments( $fragments ) {
        $count = $this->get_cart_count();
        $badge_class = 'via-firenze-navbar__cart-badge' . ( $count > 0 ? ' is-visible' : '' );
        $fragments['.via-firenze-navbar__cart-badge'] = '<span class="' . esc_attr( $badge_class ) . '">' . esc_html( $count ) . '</span>';
        return $fragments;
    }

    /**
     * Render the navbar automatically on the frontend
     */
    public function render_navbar() {
        $auto_render = $this->get_setting( 'auto_render', '1' );
        if ( $auto_render !== '1' ) {
            return;
        }
        echo $this->get_navbar_html();
    }

    /**
     * Shortcode handler [via_firenze_navbar]
     */
    public function shortcode_navbar( $atts ) {
        return $this->get_navbar_html();
    }

    /**
     * Generate Navbar HTML
     */
    public function get_navbar_html() {
        $logo_id          = $this->get_setting( 'logo', '' );
        $logo_url         = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
        $logo_scrolled_id = $this->get_setting( 'logo_scrolled', '' );
        $logo_scrolled_url= $logo_scrolled_id ? wp_get_attachment_image_url( $logo_scrolled_id, 'full' ) : '';
        $custom_text      = $this->get_setting( 'logo_text', '' );
        $site_name        = ! empty( $custom_text ) ? $custom_text : ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'VIA FIRENZE' );

        $is_sticky     = $this->get_setting( 'sticky', '1' ) === '1';
        $style_mode    = $this->get_setting( 'style', 'transparent' ); // default transparent
        $enable_search = $this->get_setting( 'enable_search', '1' ) === '1';

        $cart_url      = $this->get_cart_url();
        $cart_count    = $this->get_cart_count();

        // Selected menu
        $menu_id = $this->get_setting( 'menu', '' );
        $menu_html = '';
        if ( ! empty( $menu_id ) && is_nav_menu( $menu_id ) ) {
            $menu_html = wp_nav_menu( array(
                'menu'           => intval( $menu_id ),
                'container'      => false,
                'menu_class'     => 'via-firenze-navbar__menu-list',
                'echo'           => false,
                'depth'          => 1,
                'fallback_cb'    => false,
            ) );
        }

        // If no menu assigned, use default luxury items from reference design
        if ( empty( $menu_html ) ) {
            $fallback_items = array(
                array( 'title' => 'UOMO', 'url' => home_url( '/uomo/' ) ),
                array( 'title' => 'DONNA', 'url' => home_url( '/donna/' ) ),
                array( 'title' => 'BAMBINI', 'url' => home_url( '/bambini/' ) ),
                array( 'title' => 'THE STUDIO', 'url' => home_url( '/the-studio/' ) ),
                array( 'title' => 'SCOPRI', 'url' => home_url( '/scopri/' ) ),
            );

            $menu_html = '<ul class="via-firenze-navbar__menu-list">';
            foreach ( $fallback_items as $item ) {
                $menu_html .= '<li class="menu-item"><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a></li>';
            }
            $menu_html .= '</ul>';
        }

        $classes = array( 'via-firenze-navbar' );
        if ( $is_sticky ) {
            $classes[] = 'via-firenze-navbar--sticky';
        }
        if ( 'transparent' === $style_mode ) {
            $classes[] = 'via-firenze-navbar--transparent';
        }

        ob_start();
        ?>
        <header id="via-firenze-navbar" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="banner">
            <div class="via-firenze-navbar__container">

                <!-- Left: Hamburger (Mobile Only) -->
                <button type="button" class="via-firenze-navbar__hamburger" aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'via-firenze-navbar' ); ?>" aria-expanded="false" aria-controls="via-firenze-mobile-drawer">
                    <span class="via-firenze-navbar__hamburger-line"></span>
                    <span class="via-firenze-navbar__hamburger-line"></span>
                    <span class="via-firenze-navbar__hamburger-line"></span>
                </button>

                <!-- Left: Brand Logo -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="via-firenze-navbar__logo<?php echo $logo_scrolled_url ? ' has-scrolled-logo' : ''; ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
                    <?php if ( $logo_url || $logo_scrolled_url ) : ?>
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" class="via-firenze-navbar__logo-img via-firenze-navbar__logo-img--default" />
                        <?php endif; ?>
                        <?php if ( $logo_scrolled_url ) : ?>
                            <img src="<?php echo esc_url( $logo_scrolled_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" class="via-firenze-navbar__logo-img via-firenze-navbar__logo-img--scrolled" />
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="via-firenze-navbar__logo-text"><?php echo esc_html( $site_name ); ?></span>
                    <?php endif; ?>
                </a>

                <!-- Center: Primary Navigation Menu -->
                <nav class="via-firenze-navbar__nav" aria-label="<?php esc_attr_e( 'Main Navigation', 'via-firenze-navbar' ); ?>">
                    <?php echo $menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </nav>

                <!-- Right: Action Icons (Search & Cart only) -->
                <div class="via-firenze-navbar__actions">
                    <?php if ( $enable_search ) : ?>
                        <!-- Search Icon -->
                        <button type="button" class="via-firenze-navbar__icon-btn via-firenze-navbar__search-toggle" aria-label="<?php esc_attr_e( 'Search', 'via-firenze-navbar' ); ?>" aria-expanded="false">
                            <svg class="via-firenze-navbar__icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="10.5" cy="10.5" r="7"></circle>
                                <line x1="15.5" y1="15.5" x2="21" y2="21"></line>
                            </svg>
                        </button>
                    <?php endif; ?>

                    <!-- Shopping Bag / Cart Icon -->
                    <a href="<?php echo esc_url( $cart_url ); ?>" class="via-firenze-navbar__icon-btn via-firenze-navbar__cart-link" aria-label="<?php esc_attr_e( 'View Cart', 'via-firenze-navbar' ); ?>">
                        <svg class="via-firenze-navbar__icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 8h14l-1.2 12.2A2 2 0 0 1 15.8 22H8.2a2 2 0 0 1-2-1.8L5 8z"></path>
                            <path d="M9 8V6a3 3 0 0 1 6 0v2"></path>
                        </svg>
                        <span class="via-firenze-navbar__cart-badge<?php echo $cart_count > 0 ? ' is-visible' : ''; ?>" aria-hidden="true"><?php echo esc_html( $cart_count ); ?></span>
                    </a>
                </div>
            </div>

            <?php if ( $enable_search ) : ?>
                <!-- Search Overlay -->
                <div class="via-firenze-navbar__search-overlay" aria-hidden="true">
                    <div class="via-firenze-navbar__search-inner">
                        <form role="search" method="get" class="via-firenze-navbar__search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <input type="search" class="via-firenze-navbar__search-input" placeholder="<?php esc_attr_e( 'SEARCH PRODUCTS, COLLECTIONS...', 'via-firenze-navbar' ); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                            <?php if ( function_exists( 'is_woocommerce' ) ) : ?>
                                <input type="hidden" name="post_type" value="product" />
                            <?php endif; ?>
                            <button type="submit" class="via-firenze-navbar__search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'via-firenze-navbar' ); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="10.5" cy="10.5" r="7"></circle>
                                    <line x1="15.5" y1="15.5" x2="21" y2="21"></line>
                                </svg>
                            </button>
                        </form>
                        <button type="button" class="via-firenze-navbar__search-close" aria-label="<?php esc_attr_e( 'Close search', 'via-firenze-navbar' ); ?>">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Mobile Drawer Overlay -->
            <div id="via-firenze-mobile-drawer" class="via-firenze-navbar__mobile-menu" aria-hidden="true">
                <div class="via-firenze-navbar__mobile-menu-inner">
                    <div class="via-firenze-navbar__mobile-header">
                        <span class="via-firenze-navbar__mobile-title"><?php echo esc_html( $site_name ); ?></span>
                        <button type="button" class="via-firenze-navbar__mobile-close" aria-label="<?php esc_attr_e( 'Close menu', 'via-firenze-navbar' ); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="via-firenze-navbar__mobile-nav">
                        <?php echo $menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>

                    <div class="via-firenze-navbar__mobile-footer">
                        <a href="<?php echo esc_url( $cart_url ); ?>" class="via-firenze-navbar__mobile-cart-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 8h14l-1.2 12.2A2 2 0 0 1 15.8 22H8.2a2 2 0 0 1-2-1.8L5 8z"></path>
                                <path d="M9 8V6a3 3 0 0 1 6 0v2"></path>
                            </svg>
                            <span><?php esc_html_e( 'SHOPPING BAG', 'via-firenze-navbar' ); ?> (<?php echo esc_html( $cart_count ); ?>)</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <?php
        return ob_get_clean();
    }

    /**
     * Get all available WordPress menus for admin dropdown
     */
    public static function get_all_menus() {
        $menus = wp_get_nav_menus();
        $options = array( '' => __( '-- Default (Via Firenze Preset) --', 'via-firenze-navbar' ) );
        if ( ! empty( $menus ) && ! is_wp_error( $menus ) ) {
            foreach ( $menus as $menu ) {
                $options[ $menu->term_id ] = $menu->name;
            }
        }
        return $options;
    }

    /**
     * Render the admin settings page
     */
    public function render_admin_page() {
        wp_enqueue_media();
        include VIA_FIRENZE_NAVBAR_PATH . 'admin/admin-page.php';
    }
}

// Initialize Plugin
Via_Firenze_Navbar::get_instance();
