<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPHB_Roles' ) ) {

	/**
	 * Class WPHB_Roles
	 */
	class WPHB_Roles {


		/**
		 * WPHB_Roles constructor.
		 */
		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'add_roles' ) );

		}


		/**
		 * Add user roles.
		 */
		public static function add_roles() {

			add_role(
				'wphb_booking_manager',
				__( 'Booking Manager' ),
				array()
			);

			$room_cap    = 'hb_rooms';
			$booking_cap = 'hb_bookings';

			$booking_manager = get_role( 'wphb_booking_manager' );

			$admin = get_role( 'administrator' );

			// add capability for admin
			$admin->add_cap( 'delete_' . $room_cap );
			$admin->add_cap( 'delete_published_' . $room_cap );
			$admin->add_cap( 'delete_private_' . $room_cap );
			$admin->add_cap( 'edit_others_' . $room_cap );
			$admin->add_cap( 'edit_' . $room_cap );
			$admin->add_cap( 'edit_published_' . $room_cap );
			$admin->add_cap( 'edit_private_' . $room_cap );
			$admin->add_cap( 'edit_others_' . $room_cap );

			$admin->add_cap( 'delete_' . $booking_cap );
			$admin->add_cap( 'delete_published_' . $booking_cap );
			$admin->add_cap( 'delete_private_' . $booking_cap );
			$admin->add_cap( 'edit_others_' . $booking_cap );
			$admin->add_cap( 'edit_' . $booking_cap );
			$admin->add_cap( 'edit_published_' . $booking_cap );
			$admin->add_cap( 'edit_private_' . $booking_cap );
			$admin->add_cap( 'edit_others_' . $booking_cap );

		}

	}

}

new WPHB_Roles();