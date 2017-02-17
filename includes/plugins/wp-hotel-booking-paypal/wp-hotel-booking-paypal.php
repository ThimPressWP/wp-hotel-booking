<?php
/*
  Plugin Name: WP Hotel Booking PayPal Payment
  Plugin URI: http://thimpress.com/
  Description: Payment PayPal WP Hotel Booking Addon
  Author: ThimPress
  Version: 1.0.2.1
  Author URI: http://thimpress.com
 */

define( 'TP_HB_PAYPAL_DIR', plugin_dir_path( __FILE__ ) );
define( 'TP_HB_PAYPAL_URI', plugins_url( '', __FILE__ ) );
define( 'TP_HB_PAYPAL_VER', '1.7' );

class TP_Hotel_Booking_Payment_PayPal {

    public $is_hotel_active = false;
    public $slug = 'paypal';

    function __construct() {
        add_action( 'plugins_loaded', array( $this, 'is_hotel_active' ) );
    }

    /**
     * is hotel booking activated
     * @return boolean
     */
    function is_hotel_active() {
        if ( !function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        if ( class_exists( 'TP_Hotel_Booking' ) && ( is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) || is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) ) ) {
            $this->is_hotel_active = true;
        }

        if ( !$this->is_hotel_active ) {
            add_action( 'admin_notices', array( $this, 'add_notices' ) );
        } else {
            // add payment
            add_filter( 'hb_payment_gateways', array( $this, 'add_payment_classes' ) );
            if ( $this->is_hotel_active ) {
                require_once TP_HB_PAYPAL_DIR . '/inc/class-hb-payment-gateway-paypal.php';
            }
        }

        $this->load_text_domain();
    }

    function load_text_domain() {
        $default = WP_LANG_DIR . '/plugins/wp-hotel-booking-paypal-' . get_locale() . '.mo';
        $plugin_file = TP_HB_PAYPAL_DIR . '/languages/wp-hotel-booking-paypal-' . get_locale() . '.mo';
        $file = false;
        if ( file_exists( $default ) ) {
            $file = $default;
        } else {
            $file = $plugin_file;
        }
        if ( $file ) {
            load_textdomain( 'wp-hotel-booking-paypal', $file );
        }
    }

    /**
     * filter callback add payments
     * @param array
     */
    function add_payment_classes( $payments ) {
        if ( array_key_exists( $this->slug, $payments ) )
            return $payments;

        $payments[$this->slug] = new HB_Payment_Gateway_Paypal();
        return $payments;
    }

    /**
     * notices missing tp-hotel-booking plugin
     */
    function add_notices() {
        ?>
        <div class="error">
            <p><?php _e( 'The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking PayPal</strong> add-on' ); ?></p>
        </div>
        <?php
    }

}

new TP_Hotel_Booking_Payment_PayPal();
