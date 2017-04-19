<?php
/*
  Plugin Name: WP Hotel Booking WPML Support
  Plugin URI: http://thimpress.com/
  Description: Multilnguage CMS support
  Author: ThimPress
  Version: 1.7
  Author URI: http://thimpress.com
 */

define( 'HOTELBOOKING_WMPL_DIR', plugin_dir_path( __FILE__ ) );
define( 'HOTELBOOKING_WMPL_URI', plugins_url( '', __FILE__ ) );
define( 'HOTELBOOKING_WMPL_VER', '1.7' );

class WP_Hotel_Booking_Wpml_Support {

	public $is_hotel_active = false;
	public $slug = 'stripe';

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

		if ( ( class_exists( 'TP_Hotel_Booking' ) && is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) ) || ( is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && class_exists( 'WP_Hotel_Booking' ) ) ) {
			$this->is_hotel_active = true;
		}

		if ( !$this->is_hotel_active || !class_exists( 'SitePress' ) ) {
			add_action( 'admin_notices', array( $this, 'add_notices' ) );
		} else {
			require_once HOTELBOOKING_WMPL_DIR . 'inc/class-hbwp-support.php';
		}

		$this->load_text_domain();
	}

	function load_text_domain() {
		$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-wpml-support-' . get_locale() . '.mo';
		$plugin_file = HOTELBOOKING_WMPL_DIR . '/languages/wp-hotel-booking-wpml-support-' . get_locale() . '.mo';
		$file        = false;
		if ( file_exists( $default ) ) {
			$file = $default;
		} else {
			$file = $plugin_file;
		}
		if ( $file ) {
			load_textdomain( 'wp-hotel-booking-wpml-support', $file );
		}
	}

	/**
	 * notices missing tp-hotel-booking plugin
	 */
	function add_notices() {
		?>
        <div class="error">
            <p><?php _e( '<strong>WP Hotel Booking / WPML Multilingual CMS</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>WP Hotel Booking support WPML</strong> add-on' ); ?></p>
        </div>
		<?php
	}

}

new WP_Hotel_Booking_Wpml_Support();
