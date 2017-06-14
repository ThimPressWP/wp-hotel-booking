<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_Admin_Menu {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'register' ) );

	}

	function register() {
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
			'pricing_table' => array(
				'tp_hotel_booking',
				__( 'Pricing Plans', 'wp-hotel-booking' ),
				__( 'Pricing Plans', 'wp-hotel-booking' ),
				'manage_hb_booking',
				'tp_hotel_booking_pricing',
				array( $this, 'pricing_table' )
			)
		);

		/**
		 * recive all addons menu in settings Other settings menu
		 */
		$addon_menus = apply_filters( 'hotel_booking_addon_menus', array() );
		if ( $addon_menus ) {
			$menu_items[] = array(
				'tp_hotel_booking',
				__( 'Addition Packages', 'wp-hotel-booking' ),
				__( 'Addition Packages', 'wp-hotel-booking' ),
				'manage_hb_booking',
				'tp_hotel_booking_other_settings',
				array( $this, 'other_settings' )
			);
		}

		$menu_items['settings'] = array(
			'tp_hotel_booking',
			__( 'Settings', 'wp-hotel-booking' ),
			__( 'Settings', 'wp-hotel-booking' ),
			'manage_hb_booking',
			'tp_hotel_booking_settings',
			array( $this, 'settings_page' )
		);

		// Third-party can be add more items
		$menu_items = apply_filters( 'hotel_booking_menu_items', $menu_items );

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

	function settings_page() {
		WPHB_Admin_Settings::output();
	}

	function pricing_table() {
		wp_enqueue_script( 'wp-util' );
		WP_Hotel_Booking::instance()->_include( 'includes/admin/views/pricing-table.php' );
	}

	function other_settings() {
		WP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/other_settings.php' );
	}
}

new WPHB_Admin_Menu();
