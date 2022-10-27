<?php
/**
 * WP Hotel Booking admin menu class.
 *
 * @class       WPHB_Admin_Menu
 * @version     1.9.7.4
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Menu' ) ) {
	/**
	 * Class WPHB_Admin_Menu
	 */
	class WPHB_Admin_Menu {

		/**
		 * WPHB_Admin_Menu constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register' ) );

		}

		/**
		 * Register menu.
		 */
		public function register() {
			add_menu_page(
				__( 'WP Hotel Booking', 'wp-hotel-booking' ),
				__( 'WP Hotel Booking', 'wp-hotel-booking' ),
				'edit_hb_bookings',
				'tp_hotel_booking',
				'',
				'dashicons-calendar',
				'3.99'
			);

			$menu_items = array(
				// do not use: minhpd 30-5-2022
				// 'pricing_table' => array(
				// 'tp_hotel_booking',
				// __( 'Pricing Plans', 'wp-hotel-booking' ),
				// __( 'Pricing Plans', 'wp-hotel-booking' ),
				// 'manage_hb_booking',
				// 'tp_hotel_booking_pricing',
				// array( $this, 'pricing_table' )
				// ),
				'settings'         => array(
					'tp_hotel_booking',
					__( 'Settings', 'wp-hotel-booking' ),
					__( 'Settings', 'wp-hotel-booking' ),
					'manage_hb_booking',
					'tp_hotel_booking_settings',
					array( $this, 'settings_page' ),
				),
				'calendar_manager' => array(
					'tp_hotel_booking',
					__( 'Calendar Manager', 'wp-hotel-booking' ),
					__( 'Calendar Manager', 'wp-hotel-booking' ),
					'manage_hb_booking',
					'tp_hotel_booking_calender_manager',
					array( $this, 'calendar_manager' ),
				),
			);

			// Third-party can be add more items
			$menu_items = apply_filters( 'hotel_booking_menu_items', $menu_items );

			if ( is_array( $menu_items ) ) {
				$menu_items['tools'] = array(
					'tp_hotel_booking',
					__( 'Tools', 'wp-hotel-booking' ),
					__( 'Tools', 'wp-hotel-booking' ),
					'manage_hb_booking',
					'wphb-tools',
					array( $this, 'tools_page' ),
				);
			}

			if ( $menu_items ) {
				foreach ( $menu_items as $item ) {
					call_user_func_array( 'add_submenu_page', $item );
				}
			}

			// get user role
			$user_roles = wp_get_current_user()->roles;

			if ( $user_roles ) {
				if ( $user_roles == array( 'wphb_booking_editor' ) || $user_roles == array( 'wphb_hotel_manager' ) ) {
					remove_menu_page( 'edit.php' ); // Posts
					remove_menu_page( 'upload.php' ); // Media
					remove_menu_page( 'edit-comments.php' ); // Comments
					remove_menu_page( 'tools.php' ); // Tools
				}
			}
		}

		/**
		 * Settings page view.
		 */
		public function settings_page() {
			WPHB_Admin_Settings::output();
		}

		/**
		 * Calendar Manager
		 */
		public function calendar_manager() {
			WP_Hotel_Booking::instance()->_include( 'includes/admin/views/calendar-manager.php' );
		}

		/**
		 * Pricing table view.
		 * do not use: minhpd 30-5-2022
		 */
		// public function pricing_table() {
		// wp_enqueue_script( 'wp-util' );
		// WP_Hotel_Booking::instance()->_include( 'includes/admin/views/pricing-table.php' );
		// }

		/**
		 * Other settings view.
		 */
		public function other_settings() {
			WP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/other_settings.php' );
		}

		/**
		 * Tools page view.
		 */
		public function tools_page() {
			WPHB_Admin_Tools::output();
		}
	}
}

new WPHB_Admin_Menu();
