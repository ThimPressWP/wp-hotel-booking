<?php

class HB_Admin_Settings_Hook extends HB_Settings {

	public function __construct()
	{
		parent::__construct();
		add_action( 'hb_update_settings_my-rooms', array( $this, 'create_pages' ) );
		add_action( 'hb_update_settings_checkout', array( $this, 'create_pages' ) );
	}

	/**
	 * Create pages that the plugin relies on, storing page id's in variables.
	 */
	public function create_pages( $param ) {

		$pages = array();

		if( empty( $param ) )
			return;

		if( $param == 'my-rooms' )
		{
			$pages['my-rooms'] = array(
				'name'    => _x( 'my-rooms', 'Page slug', 'tp-hotel-booking' ),
				'title'   => _x( 'My Rooms', 'Page title', 'tp-hotel-booking' ),
				'content' => '[' . apply_filters( 'hotel_booking_cart_shortcode_tag', 'hotel_booking_cart' ) . ']'
			);
		}

		if( $param === 'checkout' )
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