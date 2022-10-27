<?php
/**
 * WP Hotel Booking Extra Admin.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Extra/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'HB_Extra_Admin' ) ) {
	/**
	 * Class HB_Extra_Admin
	 */
	class HB_Extra_Admin {

		/**
		 * HB_Extra_Admin constructor.
		 */
		public function __construct() {
			add_filter( 'hotel_booking_addon_menus', array( $this, 'extra_settings' ) );
			add_action( 'hotel_booking_extra_settings', array( $this, 'extra_settings_build' ) );
		}

		/**
		 * Other settings tab
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function extra_settings( $settings ) {
			$settings['hotel_booking_extra_settings'] = __( 'Extra Room Packages', 'wp-hotel-booking' );

			return $settings;
		}

		/**
		 * Extra settings build
		 */
		public function extra_settings_build() {
			WPHB_Extra_Factory::instance()->_include( WPHB_EXTRA_INC . '/admin/views/extra.php' );
		}
	}
}

new HB_Extra_Admin();