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

			$booking_cap = 'hb_bookings';

			$booking_manager = get_role( 'wphb_booking_manager' );

//			$booking_manager->add_cap( 'manage_options');

			$booking_manager->add_cap( 'edit_hb_booking');
			$booking_manager->add_cap( 'read_hb_booking');
			$booking_manager->add_cap( 'delete_hb_booking');
			$booking_manager->add_cap( 'edit_' . $booking_cap );
			$booking_manager->add_cap( 'delete_' . $booking_cap);
			$booking_manager->add_cap( 'edit_others_' . $booking_cap );
			$booking_manager->add_cap( 'publish_' . $booking_cap );

			$booking_manager->add_cap( 'edit_hb_room');
			$booking_manager->add_cap( 'read_hb_room');
			$booking_manager->add_cap( 'delete_hb_room');
			$booking_manager->add_cap( 'edit_hb_rooms');
			$booking_manager->add_cap( 'edit_hb_rooms');
			$booking_manager->add_cap( 'delete_hb_rooms');
			$booking_manager->add_cap( 'edit_others_hb_rooms');
			$booking_manager->add_cap( 'publish_hb_rooms');
			$booking_manager->add_cap( 'read');


//			$booking_manager->add_cap( 'edit_posts' );
//			$booking_manager->add_cap( 'delete_posts' );
//			$booking_manager->add_cap( 'edit_others_posts' );
//
//			$booking_manager->remove_cap('edit_hb_rooms');
//			$booking_manager->remove_cap('edit_hb_rooms');
//			$booking_manager->remove_cap('delete_hb_rooms');
//			$booking_manager->remove_cap('edit_others_hb_rooms');
//
//			$booking_manager->remove_cap('edit_posts');
//			$booking_manager->remove_cap('delete_posts');
//			$booking_manager->remove_cap('edit_others_posts');


		}

	}

}

new WPHB_Roles();