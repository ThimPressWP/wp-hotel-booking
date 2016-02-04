<?php
/*
    Plugin Name: TP Hotel Booking
    Plugin URI: http://thimpress.com/
    Description: Full of professional features for a booking room system.
    Author: ThimPress
    Version: 1.1.1
    Author URI: http://thimpress.com
*/

define( 'HB_FILE', __FILE__ );
define( 'HB_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'HB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'HB_VERSION', '1.1.1' );
define( 'HB_BLOG_ID', get_current_blog_id() );
/**
 * Class TP_Hotel_Booking
 */
class TP_Hotel_Booking{
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
    function __construct(){
        if( self::$_instance ) return;
        $this->includes();
        $this->load_text_domain();

        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_print_scripts', array( $this, 'global_js' ) );
        add_action( 'template_redirect', 'hb_handle_purchase_request', 999 );
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        register_activation_hook( __FILE__, array( $this, 'install' ) );
        add_action( 'init', array( $this, 'init' ), 20 );
        // $this->install();
    }

    function init() {
        // cart
        $this->cart = HB_Cart::instance();
    }

    function install(){
        if( ! function_exists( 'hb_create_page' ) )
        {
            $this->_include( 'includes/admin/hb-admin-functions.php' );
            $this->_include( 'includes/hb-functions.php' );
        }
        $this->_include( 'includes/install.php' );
    }

    /**
     * Include a file
     *
     * @param string
     * @param bool
     * @param array
     */
    function _include( $file, $root = true, $args = array(), $unique = true ){
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
    function locate( $file ){
        return $this->_plugin_path . '/' . $file;
    }

    /**
     * Includes common files and libraries
     */
    function includes(){
        $this->_include( 'includes/class-hb-autoloader.php' );
        $this->_include( 'includes/class-hb-booking-template-loader.php' );
        $this->_include( 'includes/class-hb-ajax.php' );
        if( is_admin() ) {
            $this->_include( 'includes/admin/class-hb-admin-menu.php' );
            $this->_include( 'includes/class-hb-meta-box.php' );
            $this->_include( 'includes/admin/hb-admin-functions.php' );
            $this->_include( 'includes/admin/class-hb-admin-settings-hook.php' );
            $this->_include( 'includes/admin/class-hb-customer.php' );
        }
        $this->_include( 'includes/class-hb-comments.php' );
        $this->_include( 'includes/hb-template-hooks.php' );
        $this->_include( 'includes/hb-template-functions.php' );

        // shortcodes
        $this->_include( 'includes/class-hb-shortcodes.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-cart.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-checkout.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-lastest-reviews.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-mini-cart.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking-slider.php' );
        $this->_include( 'includes/shortcodes/class-hb-shortcode-hotel-booking.php' );
        // end shortcodes

        $this->_include( 'includes/widgets/class-hb-widget-search.php' );
        $this->_include( 'includes/widgets/class-hb-widget-room-carousel.php' );
        $this->_include( 'includes/widgets/class-hb-widget-best-reviews.php' );
        $this->_include( 'includes/widgets/class-hb-widget-lastest-reviews.php' );
        $this->_include( 'includes/widgets/class-hb-widget-mini-cart.php' );
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
        // // $this->_include( 'includes/class-hb-settings.php' );

        $this->_include( 'includes/hb-webhooks.php' );

        if( ! class_exists( 'Aq_Resize' ) ) {
            $this->_include( 'includes/aq_resizer.php' );
        }
    }

    // load payments addons
    function plugins_loaded()
    {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        // load payment gateways
        $this->load_payments();
        // load reports
        $this->load_reports();
    }

    // load all payment gateways support
    function load_payments()
    {
        $this->_include( 'includes/payment-gateways/class-hb-payment-gateway-base.php' );

        $plugins = get_plugins();
        $payments_path = HB_PLUGIN_PATH . '/includes/payment-gateways';
        foreach ( glob( $payments_path . '/class-hb-payment-gateway-*.php' ) as $key => $file ) {
            $file_name = basename( $file );
            if( $file_name === 'class-hb-payment-gateway-base.php' )
                continue;

            $name = str_replace( 'class-hb-payment-gateway-', 'tp-hotel-booking-', $file_name );
            $plugin_dir = str_replace( '.php', '', $name );
            $plugin_file = $plugin_dir . '/' . $name;

            if( ! array_key_exists( $plugin_file, $plugins ) || ! is_plugin_active( $plugin_file ) )
            {
                $this->_include( 'includes/payment-gateways/' . $file_name );
            }
        }
    }

    // load report
    function load_reports()
    {
        $plugins = get_plugins();
        $report = 'tp-hotel-booking-report/tp-hotel-booking-report.php';
        if( ! array_key_exists( $report, $plugins ) || ! is_plugin_active( $report ) )
        {
            $this->_include( 'includes/class-hb-report.php' );
            $this->_include( 'includes/class-hb-report-price.php' );
            $this->_include( 'includes/class-hb-report-room.php' );
        }
    }

    /**
     * Get the path of the plugin with sub path
     *
     * @param string $sub
     * @return string
     */
    function plugin_path( $sub = '' ){
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
    function plugin_url( $sub = '' ){
        if( ! $this->_plugin_url ) {
            $this->_plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
        }
        return $this->_plugin_url . '/' . $sub;
    }

    /**
     * Load language for the plugin
     */
    function load_text_domain(){
        $locale = get_locale();
        $dir    = $this->plugin_path( 'languages' );
        $mofile = "{$dir}/{$locale}.mo";

        // In themes/plugins/mu-plugins directory
        load_textdomain( 'tp-hotel-booking', $mofile );
    }

    /**
     * Enqueue assets for the plugin
     */
    function enqueue_assets(){
        $dependencies = array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-datepicker'
        );

        wp_register_script( 'jquery-ui-datepicker', $this->plugin_url( 'includes/assets/js/jquery.ui.datepicker.min.js' ), array( 'jquery' ) );
        wp_register_style( 'tp-hotel-booking-libaries-style', $this->plugin_url( 'includes/assets/css/libraries.css' ) );

        // select2
        wp_register_script( 'tp-admin-hotel-booking-select2', $this->plugin_url( 'includes/assets/js/select2.min.js' ) );
        if( is_admin() ){
            wp_register_style( 'tp-admin-hotel-booking', $this->plugin_url( 'includes/assets/css/admin.tp-hotel-booking.min.css' ) );
            wp_register_script( 'tp-admin-hotel-booking', $this->plugin_url( 'includes/assets/js/admin.hotel-booking.min.js' ), $dependencies );
            wp_localize_script( 'tp-admin-hotel-booking', 'hotel_booking_l18n', hb_admin_l18n() );
            //report
            wp_register_script( 'tp-admin-hotel-booking-chartjs', $this->plugin_url( 'includes/assets/js/Chart.min.js' ) );
            wp_register_script( 'tp-admin-hotel-booking-tokenize-js', $this->plugin_url( 'includes/assets/js/jquery.tokenize.js' ) );
            wp_register_style( 'tp-admin-hotel-booking-tokenize-css', $this->plugin_url( 'includes/assets/css/jquery.tokenize.css' ) );
        }else{
            wp_register_style( 'tp-hotel-booking', $this->plugin_url( 'includes/assets/css/hotel-booking.min.css' ) );
            wp_register_script( 'tp-hotel-booking', $this->plugin_url( 'includes/assets/js/hotel-booking.js' ), $dependencies );

            // stripe and checkout assets
            wp_register_script( 'tp-hotel-booking-stripe-js', $this->plugin_url( 'includes/assets/js/stripe.js' ), $dependencies );
            wp_register_script( 'tp-hotel-booking-stripe-checkout-js', $this->plugin_url( 'includes/assets/js/checkout.js' ), $dependencies );

            wp_localize_script( 'tp-hotel-booking', 'hotel_booking_l18n', hb_l18n() );

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
        }else{
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
    function global_js(){
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
     * Register widgets
     */
    function register_widgets() {
        register_widget( 'HB_Widget_Search' );
        register_widget( 'HB_Widget_Room_Carousel' );
        register_widget( 'HB_Widget_Room_Best_Reviews' );
        register_widget( 'HB_Widget_Room_Lastest_Reviews' );
        register_widget( 'HB_Widget_Cart' );
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