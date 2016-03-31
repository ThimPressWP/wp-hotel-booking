<?php
/*
    Plugin Name: TP Hotel Booking
    Plugin URI: http://thimpress.com/
    Description: Full of professional features for a booking room system.
    Author: ThimPress
    Version: 1.1.4.1
    Author URI: http://thimpress.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

define( 'HB_FILE', __FILE__ );
define( 'HB_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'HB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'HB_VERSION', '1.1.4.1' );
define( 'HB_BLOG_ID', get_current_blog_id() );

/**
 * Class TP_Hotel_Booking
 */
class TP_Hotel_Booking {

    /**
     * Hold the instance of main class
     *
     * @var object
     */
    protected static $_instance = null;

    /**
     * Plugin path
     *
     * @var string
     */
    protected $_plugin_path = null;

    /**
     * Plugin URL
     *
     * @var string
     */
    protected $_plugin_url = null;

    public $cart = null;

    /**
     * Construction
     */
    public function __construct(){
        if( self::$_instance ) {
            return self::$_instance;
        }
        $this->includes();

        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_print_scripts', array( $this, 'global_js' ) );
        add_action( 'template_redirect', 'hb_handle_purchase_request', 999 );
        register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'install' ) );
        add_action( 'init', array( $this, 'init' ), 20 );

        // create new blog in multisite
        add_action( 'wpmu_new_blog', array( $this,'create_new_blog' ), 10, 6 );
        // multisite delete table in multisite
        add_filter( 'wpmu_drop_tables', array( $this, 'delete_blog_table' ) );
    }

    public function init(){
        // cart
        $this->cart = HB_Cart::instance();
    }

    // install hook
    public function install(){
        return HB_Install::install();
    }

    // create new blog table
    public function create_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        return HB_Install::create_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta );
    }

    // delete table when delete blog, multisite
    public function delete_blog_table( $tables ) {
        return HB_Install::delete_tables( $tables );
    }

    /**
     * Include a file
     *
     * @param string
     * @param bool
     * @param array
     */
    public function _include( $file, $root = true, $args = array(), $unique = true ) {
        if( $root ){
            $file = $this->plugin_path( $file );
        }
        if( is_array( $args ) ){
            extract( $args );
        }

        if( file_exists( $file ) )
        {
            if ( $unique ) {
                require_once $file;
            }
            else {
                require $file;
            }
        }
    }

    /**
     * Get the full path of a file
     *
     * @param string
     * @return string
     */
    public function locate( $file ){
        return $this->_plugin_path . '/' . $file;
    }

    /**
     * Includes common files and libraries
     */
    public function includes(){
        $this->_include( 'includes/class-hb-autoloader.php' );
        $this->_include( 'includes/class-hb-booking-template-loader.php' );
        $this->_include( 'includes/class-hb-ajax.php' );

        if( is_admin() ) {
            $this->admin_includes();
        }
        $this->_include( 'includes/class-hb-settings.php' );
        $this->_include( 'includes/class-hb-comments.php' );
        $this->_include( 'includes/hb-template-hooks.php' );
        $this->_include( 'includes/hb-template-functions.php' );
        $this->_include( 'includes/hb-widget-functions.php' );

        if ( ! is_admin() ) {
            $this->frontend_includes();
        }
        $this->_include( 'includes/class-hb-post-types.php' );

        $this->_include( 'includes/hb-functions.php' );
        $this->_include( 'includes/class-hb-resizer.php' );
        $this->_include( 'includes/class-hb-booking.php' );

        // products
        $this->_include( 'includes/products/class-hb-abstract-product.php' );
        $this->_include( 'includes/products/class-hb-product-room.php' );
        // // addon
        $this->_include( 'includes/plugins/tp-hb-currencies/tp-hb-currencies.php' );
        $this->_include( 'includes/plugins/tp-hb-extra/tp-hb-extra.php' );
        // // end addon
        $this->_include( 'includes/products/class-hb-room.php' );
        // end products
        $this->_include( 'includes/class-hb-sessions.php' );
        $this->_include( 'includes/class-hb-cart.php' );
        $this->_include( 'includes/payment-gateways/class-hb-payment-gateway-base.php' );

        $this->_include( 'includes/hb-webhooks.php' );
    }

    public function frontend_includes() {
        // shortcodes
        $this->_include( 'includes/class-hb-shortcodes.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-cart.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-checkout.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-lastest-reviews.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-mini-cart.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-slider.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking.php' );
        // end shortcodes

        if( ! class_exists( 'Aq_Resize' ) ) {
            $this->_include( 'includes/aq_resizer.php' );
        }
    }

    public function admin_includes() {
        $this->_include( 'includes/admin/class-hb-admin.php' );
    }

    // load payments addons
    public function plugins_loaded()
    {
        // load text domain
        $this->load_text_domain();
    }

    /**
     * Get the path of the plugin with sub path
     *
     * @param string $sub
     * @return string
     */
    public function plugin_path( $sub = '' ){
        if( ! $this->_plugin_path ) {
            $this->_plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
        }
        return $this->_plugin_path . '/' . $sub;
    }

    /**
     * Get the url of the plugin with sub path
     *
     * @param string $sub
     * @return string
     */
    public function plugin_url( $sub = '' ){
        if( ! $this->_plugin_url ) {
            $this->_plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
        }
        return $this->_plugin_url . '/' . $sub;
    }

    /**
     * Load language for the plugin
     */
    public function load_text_domain(){
        // prefix
        $prefix = basename( dirname( plugin_basename( __FILE__ ) ) );
        $locale = get_locale();
        $dir    = $this->plugin_path( 'languages' );
        $mofile = false;

        $globalFile = WP_LANG_DIR . '/plugins/' . $prefix . '-' . $locale . '.mo';
        $pluginFile = $dir . '/' . $prefix . '-' . $locale . '.mo';

        if ( file_exists( $globalFile ) ) {
            $mofile = $globalFile;
        } else if ( file_exists( $pluginFile ) ) {
            $mofile = $pluginFile;
        }

        if ( $mofile ) {
            // In themes/plugins/mu-plugins directory
            load_textdomain( 'tp-hotel-booking', $mofile );
        }
    }

    /**
     * Enqueue assets for the plugin
     */
    public function enqueue_assets(){
        $dependencies = array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-datepicker'
        );

        wp_register_style( 'tp-hotel-booking-libaries-style', $this->plugin_url( 'includes/assets/css/libraries.css' ) );

        // select2
        wp_register_script( 'tp-admin-hotel-booking-select2', $this->plugin_url( 'includes/assets/js/select2.min.js' ) );
        if( is_admin() ){
            wp_register_style( 'tp-admin-hotel-booking', $this->plugin_url( 'includes/assets/css/admin.tp-hotel-booking.min.css' ) );
            wp_register_script( 'tp-admin-hotel-booking', $this->plugin_url( 'includes/assets/js/admin.hotel-booking.js' ), $dependencies );
            wp_localize_script( 'tp-admin-hotel-booking', 'hotel_booking_i18n', hb_admin_i18n() );
            //report
            wp_register_script( 'tp-admin-hotel-booking-chartjs', $this->plugin_url( 'includes/assets/js/Chart.min.js' ) );
            wp_register_script( 'tp-admin-hotel-booking-tokenize-js', $this->plugin_url( 'includes/assets/js/jquery.tokenize.min.js' ) );
            wp_register_style( 'tp-admin-hotel-booking-tokenize-css', $this->plugin_url( 'includes/assets/css/jquery.tokenize.min.css' ) );
        } else {
            wp_register_style( 'tp-hotel-booking', $this->plugin_url( 'includes/assets/css/hotel-booking.min.css' ) );
            wp_register_script( 'tp-hotel-booking', $this->plugin_url( 'includes/assets/js/hotel-booking.min.js' ), $dependencies );

            // stripe and checkout assets
            wp_register_script( 'tp-hotel-booking-stripe-js', $this->plugin_url( 'includes/assets/js/stripe.js' ), $dependencies );
            wp_register_script( 'tp-hotel-booking-stripe-checkout-js', $this->plugin_url( 'includes/assets/js/checkout.js' ), $dependencies );

            wp_localize_script( 'tp-hotel-booking', 'hotel_booking_i18n', hb_i18n() );

            // rooms slider widget
            wp_register_script( 'tp-hotel-booking-gallery', $this->plugin_url( 'includes/libraries/camera/js/gallery.min.js' ), $dependencies );

            // owl carousel
            wp_register_script( 'tp-hotel-booking-owl-carousel', $this->plugin_url( 'includes/libraries/owl-carousel/owl.carousel.min.js' ), $dependencies );
        }

        if( is_admin() ) {
            wp_enqueue_style( 'tp-admin-hotel-booking' );
            wp_enqueue_script( 'tp-admin-hotel-booking' );

            // report
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            // report
            wp_enqueue_script( 'tp-admin-hotel-booking-chartjs' );
            wp_enqueue_script( 'tp-admin-hotel-booking-tokenize-js' );
            wp_enqueue_style( 'tp-admin-hotel-booking-tokenize-css' );
        } else {
            wp_enqueue_style( 'tp-hotel-booking' );
            wp_enqueue_script( 'tp-hotel-booking' );
            $setting = HB_Settings::instance()->get('stripe');

            if( ! empty( $setting['enable'] ) && $setting['enable'] == 'on' ) {
                // stripe
                wp_enqueue_script( 'tp-hotel-booking-stripe-js' );
                wp_enqueue_script( 'tp-hotel-booking-stripe-checkout-js' );
            }

            // rooms slider widget
            wp_enqueue_script( 'tp-hotel-booking-owl-carousel' );

            // room galleria
            wp_enqueue_script( 'tp-hotel-booking-gallery' );
        }
        wp_enqueue_style( 'tp-hotel-booking-libaries-style' );

        // select2
        wp_enqueue_script( 'tp-admin-hotel-booking-select2' );
    }

    /**
     * Output global js settings
     */
    public function global_js(){
        $upload_dir = wp_upload_dir();
        $upload_base_url = $upload_dir['baseurl'];
    ?>
        <script type="text/javascript">
            var hotel_settings = {
                ajax: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                settings: <?php echo HB_Settings::instance()->toJson( apply_filters( 'hb_settings_fields', array( 'review_rating_required' ) ) ); ?>,
                upload_base_url: '<?php echo esc_js($upload_base_url) ?>',
                meta_key: {
                    prefix: '_hb_'
                },
                nonce: '<?php echo wp_create_nonce( 'hb_booking_nonce_action' ); ?>',
                timezone: '<?php echo current_time( 'timestamp' ) ?>'
            }
        </script>
    <?php
    }

    /**
     * Create an instance of the plugin if it is not created
     *
     * @static
     * @return object|TP_Hotel_Booking
     */
    static function instance(){
        if( ! self::$_instance ){
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

$GLOBALS['tp_hotel_booking'] = TP_Hotel_Booking::instance();