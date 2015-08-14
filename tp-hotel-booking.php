<?php
/*
Plugin Name: TP Hotel Booking
Plugin URI: http://thimpress.com/learnpress
Description: [Description here]
Author: ThimPress
Version: 0.9
Author URI: http://thimpress.com
*/

define( 'HB_FILE', __FILE__ );
define( 'HB_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'HB_VERSION', '0.9' );
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

    /**
     * Construction
     */
    function __construct(){
        if( self::$_instance ) return;
        $this->includes();
        $this->load_text_domain();

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_print_scripts', array( $this, 'global_js' ) );
        add_action( 'template_redirect', 'hb_handle_purchase_request', 999 );
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );

    }

    /**
     * Include a file
     *
     * @param string
     * @param bool
     * @param array
     */
    function _include( $file, $root = true, $args = array() ){
        if( $root ){
            $file = $this->plugin_path( $file );
        }
        if( is_array( $args ) ){
            extract( $args );
        }
        require_once $file;
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
        $this->_include( 'includes/class-hb-ajax.php' );
        if( is_admin() ) {
            $this->_include( 'includes/admin/class-hb-admin-menu.php' );
            $this->_include( 'includes/class-hb-meta-box.php' );
            $this->_include( 'includes/admin/hb-admin-functions.php' );
        }else{
            $this->_include( 'includes/hb-template-hooks.php' );
            $this->_include( 'includes/hb-template-functions.php' );
            $this->_include( 'includes/class-hb-shortcodes.php' );
        }
        $this->_include( 'includes/widgets/class-hb-widget-search.php' );
        $this->_include( 'includes/class-hb-post-types.php' );
        $this->_include( 'includes/hb-functions.php' );
        $this->_include( 'includes/class-hb-cart.php' );
        $this->_include( 'includes/class-hb-settings.php' );
        $this->_include( 'includes/class-hb-booking.php' );
        $this->_include( 'includes/payment-gateways/class-hb-payment-gateway-base.php' );
        $this->_include( 'includes/payment-gateways/class-hb-payment-gateway-paypal.php' );
        $this->_include( 'includes/hb-webhooks.php' );
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
        $mofile = "{$dir}{$locale}.mo";

        // In themes/plugins/mu-plugins directory
        load_textdomain( 'tp-hotel-booking', $mofile );
    }

    /**
     * Enqueue assets for the plugin
     */
    function enqueue_assets(){
        $dependencies = array(
            'jquery',
            'jquery-ui-datepicker'
        );
        wp_register_script( 'jquery-ui-datepicker', $this->plugin_url( 'includes/assets/js/jquery.ui.datepicker.min.js' ), array( 'jquery' ) );
        wp_register_style( 'jquery-ui-datepicker', $this->plugin_url( 'includes/assets/css/jquery.ui.datepicker.css' ) );
        if( is_admin() ){
            wp_register_style( 'tp-admin-hotel-booking', $this->plugin_url( 'includes/assets/css/admin.tp-hotel-booking.css' ) );
            wp_register_script( 'tp-admin-hotel-booking', $this->plugin_url( 'includes/assets/js/admin.hotel-booking.js' ), $dependencies );
            wp_localize_script( 'tp-admin-hotel-booking', 'hotel_booking_l18n', hb_admin_l18n() );
        }else{
            wp_register_style( 'tp-hotel-booking', $this->plugin_url( 'includes/assets/css/hotel-booking.css' ) );
            wp_register_script( 'tp-hotel-booking', $this->plugin_url( 'includes/assets/js/hotel-booking.js' ), $dependencies );

            wp_localize_script( 'tp-hotel-booking', 'hotel_booking_l18n', hb_l18n() );
        }

        if( is_admin() ) {
            wp_enqueue_style( 'tp-admin-hotel-booking' );
            wp_enqueue_script( 'tp-admin-hotel-booking' );
            wp_enqueue_style( 'jquery-ui-datepicker' );
        }else{
            wp_enqueue_style( 'jquery-ui-datepicker' );
            wp_enqueue_style( 'tp-hotel-booking' );
            wp_enqueue_script( 'tp-hotel-booking' );
        }
    }

    /**
     * Output global js settings
     */
    function global_js(){
    ?>
        <script type="text/javascript">
            var hotel_settings = {
                ajax: '<?php echo admin_url( 'admin-ajax.php' );?>'
            }
        </script>
    <?php
    }

    /**
     * Register widgets
     */
    function register_widgets() {
        register_widget( 'HB_Widget_Search' );
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