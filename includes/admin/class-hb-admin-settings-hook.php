<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HB_Admin_Settings_Hook extends HB_Settings {

	public function __construct()
	{
		parent::__construct();
		add_action( 'hb_update_settings_my-rooms', array( $this, 'create_pages' ), 10, 2 );
		add_action( 'hb_update_settings_checkout', array( $this, 'create_pages' ), 10, 2 );
	}

	/**
	 * Create pages that the plugin relies on, storing page id's in variables.
	 */
	public function create_pages( $name, $value ) {

		$pages = array();

		if( empty( $name ) )
			return;

		if( $name == 'my-rooms' && $value == 1 )
		{
			$pages['my-rooms'] = array(
				'name'    => _x( 'my-rooms', 'Page slug', 'tp-hotel-booking' ),
				'title'   => _x( 'My Rooms', 'Page title', 'tp-hotel-booking' ),
				'content' => '[' . apply_filters( 'hotel_booking_cart_shortcode_tag', 'hotel_booking_cart' ) . ']'
			);
		}

		if( $name === 'checkout' && $value == 1 )
		{
			$pages['checkout'] = array(
				'name'    => _x( 'room-checkout', 'Page slug', 'tp-hotel-booking' ),
				'title'   => _x( 'Checkout', 'Page title', 'tp-hotel-booking' ),
				'content' => '[' . apply_filters( 'hotel_booking_checkout_shortcode_tag', 'hotel_booking_checkout' ) . ']'
			);
		}

		$pages = apply_filters( 'hotel_booking_create_pages', $pages );

		if( empty( $pages ) )
			return;

		foreach ( $pages as $key => $page ) {
			$pageId = hb_create_page( esc_sql( $page['name'] ), 'hotel_booking_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? hb_get_page_id( $page['parent'] ) : '' );
			$this->set( $key.'_page_id', $pageId );
		}
	}
}

new HB_Admin_Settings_Hook();