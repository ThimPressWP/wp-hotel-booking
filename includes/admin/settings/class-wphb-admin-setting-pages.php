<?php
/**
 * WP Hotel Booking admin setting pages.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Setting_Pages' ) ) {

	class WPHB_Admin_Setting_Pages extends WPHB_Admin_Setting_Page {

		public $id    = 'pages';
		public $title = null;

		function __construct() {

			$this->title = __( 'Pages', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters(
				'hotel_booking_admin_setting_fields_' . $this->id,
				array(
					array(
						'type'  => 'section_start',
						'id'    => 'pages_settings',
						'title' => __( 'Pages Options', 'wp-hotel-booking' ),
						'desc'  => __( 'Pages options for system.', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_rooms_page_id',
						'title' => __( 'Rooms Page', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_search_page_id',
						'title' => __( 'Search Page', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_checkout_page_id',
						'title' => __( 'Checkout Page', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_cart_page_id',
						'title' => __( 'Cart Page', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_go_page_after_booking',
						'title' => __( 'Redirect to page after book', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_account_page_id',
						'title' => __( 'Account Page', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_terms_page_id',
						'title' => __( 'Terms And Conditions Page', 'wp-hotel-booking' ),
					),
					array(
						'type'  => 'select_page',
						'id'    => 'tp_hotel_booking_thankyou_page_id',
						'title' => __( 'Thank You Page', 'wp-hotel-booking' ),
					),
					array(
						'type' => 'section_end',
						'id'   => 'pages_settings',
					),
				)
			);
		}

	}

}

return new WPHB_Admin_Setting_Pages();
