<?php
/**
 * WP Hotel Booking admin setting advanced.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      DoNgocPhuc
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WPHB_Admin_Setting_Advanced' ) ) {

	class WPHB_Admin_Setting_Advanced extends WPHB_Admin_Setting_Page {

		public $id = 'advanced';

		public $title = null;

		function __construct() {

			$this->title = __( 'Advanced', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters(
				'hotel_booking_admin_setting_fields_' . $this->id,
				array(

					array(
						'type'  => 'section_start',
						'id'    => 'room_filter_setting',
						'title' => __( 'Room Filter Options', 'wp-hotel-booking' ),
						'desc'  => __( 'Room filter used in room list ( room archive page, booking search page ).', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_filter_price_enable',
						'title'   => __( 'Enable room filter', 'wp-hotel-booking' ),
						'desc'    => __( 'Enable/disable room filter in search page.', 'wp-hotel-booking' ),
						'type'    => 'checkbox',
						'default' => 1,
					),
					array(
						'id'      => 'tp_hotel_booking_filter_price_min',
						'type'    => 'number',
						'default' => 0,
						'min'     => 0,
						'title'   => __( 'Min Price', 'wp-hotel-booking' ),
						'desc'    => __( 'Minimum price for price field.', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_filter_price_max',
						'type'    => 'number',
						'default' => 100,
						'min'     => 0,
						'title'   => __( 'Max Price', 'wp-hotel-booking' ),
						'desc'    => __( 'Maximum price for price field.', 'wp-hotel-booking' ),
					),

					array(
						'id'      => 'tp_hotel_booking_filter_price_step',
						'type'    => 'number',
						'default' => 1,
						'min'     => 1,
						'title'   => __( 'Step Price', 'wp-hotel-booking' ),
						'desc'    => __( 'Step price for price field.', 'wp-hotel-booking' ),
					),

					array(
						'type' => 'section_end',
						'id'   => 'room_filter_setting',
					),
				)
			);
		}
	}
}

return new WPHB_Admin_Setting_Advanced();
