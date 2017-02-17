<?php

/**
 * Plugin Name: WP Booking Booking Room
 * Plugin URI: http://thimpress.com/
 * Description: Support book room without search room
 * Author: ThimPress
 * Version: 1.7
 * Text Domain: wp-hotel-booking-room
 * Domain Path: /languages/
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

define( 'TP_HB_BOOKING_ROOM_PATH', plugin_dir_path( __FILE__ ) );
define( 'TP_HB_BOOKING_ROOM_URI', plugin_dir_url( __FILE__ ) );
define( 'TP_HB_BOOKING_ROOM_INC_PATH', plugin_dir_path( __FILE__ ) . 'inc' );

class TP_Hotel_Booking_Room {

    static $instance = null;
    public $available = false;
    public $booking = null;

    function __construct() {
        // loaded
        add_action( 'plugins_loaded', array( $this, 'loaded' ) );
    }

    function loaded() {
        if ( !function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        if ( class_exists( 'TP_Hotel_Booking' ) && ( is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) || is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) ) ) {
            $this->available = true;
        }

        if ( !$this->available ) {
            add_action( 'admin_notices', array( $this, 'admin_notice' ) );
        } else {
            // include files
            $this->_includes( 'functions.php' );
            $this->_includes( 'class-hb-booking-room.php' );

            // load text domain
            $this->load_textdomain();
            
            $this->booking = TP_Hotel_Booking_Room_Extenstion::instance();
        }
    }

    /**
     * load text domain
     * @return boolean
     */
    function load_textdomain() {

        $prefix = $text_domain = basename( TP_HB_BOOKING_ROOM_PATH );
        $locale = get_locale();
        $file_name = $prefix . '-' . $locale . '.mo';

        $file = $plugin_file = TP_HB_BOOKING_ROOM_PATH . '/languages/' . $file_name;
        $wp_file = WP_LANG_DIR . '/plugins/' . $file_name;

        if ( file_exists( $wp_file ) ) {
            $file = $wp_file;
        }
        // loaded
        return load_textdomain( $text_domain, $file );
    }

    function _includes( $file = '' ) {
        $file = TP_HB_BOOKING_ROOM_INC_PATH . '/' . $file;
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }

    function admin_notice() {
        print( '<div class="error">
					<p>The <strong>WP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking Room</strong> add-on</p>
				</div>' );
    }

    static function instance() {
        if ( is_null( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }

}

function TP_Hotel_Booking_Room() {
    return TP_Hotel_Booking_Room::instance();
}

$GLOBALS['TP_Hotel_Booking_Room'] = TP_Hotel_Booking_Room();
