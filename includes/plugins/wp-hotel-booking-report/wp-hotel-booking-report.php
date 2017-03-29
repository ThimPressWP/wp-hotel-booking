<?php
/*
  Plugin Name: WP Hotel Booking Report
  Plugin URI: http://thimpress.com/
  Description: WP Hotel Booking Report
  Author: ThimPress
  Version: 1.7
 */
define( 'TP_HB_REPORT_DIR', plugin_dir_path( __FILE__ ) );
define( 'TP_HB_REPORT_URI', plugin_dir_url( __FILE__ ) );
define( 'TP_HB_REPORT_VER', '1.7' );

class WP_Hotel_Booking_Report {

    public $is_hotel_active = false;

    function __construct() {
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
    }

    /**
     * load_textdomain
     * @return null
     */
    public function load_textdomain() {
        $default = WP_LANG_DIR . '/plugins/wp-hotel-booking-report-' . get_locale() . '.mo';
        $plugin_file = TP_HB_REPORT_DIR . '/languages/wp-hotel-booking-report-' . get_locale() . '.mo';
        $file = false;
        if ( file_exists( $default ) ) {
            $file = $default;
        } else {
            $file = $plugin_file;
        }
        if ( $file ) {
            load_textdomain( 'wp-hotel-booking-report', $file );
        }
    }

    /**
     * plugin loaded
     * @return null
     */
    function plugins_loaded() {
        if ( !function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

		if ( class_exists( 'TP_Hotel_Booking' ) && ( is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) || is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && class_exists( 'WP_Hotel_Booking' ) ) ) {
            $this->is_hotel_active = true;
        }

        if ( !$this->is_hotel_active ) {
            add_action( 'admin_notices', array( $this, 'add_notices' ) );
        } else {
            if ( $this->is_hotel_active ) {
                require_once TP_HB_REPORT_DIR . '/inc/functions.php';
                require_once TP_HB_REPORT_DIR . '/inc/class-hb-report.php';
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                add_action( 'admin_init', array( $this, 'init' ) );
            }
        }
        /**
         * text-domain
         */
        $this->load_textdomain();
    }

    function init() {
        require_once TP_HB_REPORT_DIR . '/inc/class-hb-report-price.php';
        require_once TP_HB_REPORT_DIR . '/inc/class-hb-report-room.php';
    }

    /**
     * notice messages
     */
    function add_notices() {
        ?>
        <div class="error">
            <p><?php _e( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Report</strong> add-on.' ); ?></p>
        </div>
        <?php
    }

    function enqueue_scripts() {

        wp_register_script( 'tp-admin-hotel-booking-chartjs', TP_HB_REPORT_URI . 'assets/js/Chart.min.js' );
        wp_register_script( 'tp-admin-hotel-booking-tokenize-js', TP_HB_REPORT_URI . 'assets/js/jquery.tokenize.min.js' );
        wp_register_style( 'tp-admin-hotel-booking-tokenize-css', TP_HB_REPORT_URI . 'assets/css/jquery.tokenize.min.css' );

        wp_enqueue_script( 'tp-admin-hotel-booking-chartjs' );
        wp_enqueue_script( 'tp-admin-hotel-booking-tokenize-js' );
        wp_enqueue_style( 'tp-admin-hotel-booking-tokenize-css' );
    }

}

$hotel_block = new WP_Hotel_Booking_Report();
