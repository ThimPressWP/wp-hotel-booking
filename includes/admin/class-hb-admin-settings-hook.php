<?php

class HB_Admin_Settings_Hook extends HB_Settings {

	public function __construct()
	{
		parent::__construct();
		add_action( 'hb_update_settings_my-rooms', array( $this, 'create_pages' ) );
		add_action( 'hb_update_settings_checkout', array( $this, 'create_pages' ) );
		add_action( 'tp_hotel_booking_chart_sidebar', array($this, 'report_sidebar' ), 10, 2 );
		add_action( 'tp_hotel_booking_chart_canvas', array($this, 'report_canvas' ), 10, 2 );

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

	/**
	 * @param $tab, $range
	 * @return file if file exists
	 */
	function report_sidebar( $tab = '', $range = '' )
	{
		if( ! $tab || ! $range )
			return;

		$file = apply_filters( "tp_hotel_booking_chart_sidebar_{$tab}_{$range}", '', $tab, $range );

		if( ! $file || ! file_exists( $file ) )
			$file = apply_filters( "tp_hotel_booking_chart_sidebar_layout", '', $tab, $range );

		if( file_exists( $file ) )
			require $file;
	}

	/**
	 * @param $tab, $range
	 * @return html file canvas
	 */
	function report_canvas( $tab = '', $range = '' )
	{
		if( ! $tab || ! $range )
			return;

		$file = apply_filters( "tp_hotel_booking_chart_{$tab}_{$range}_canvas", '', $tab, $range );

		if( ! $file || ! file_exists( $file ) )
			$file = apply_filters( "tp_hotel_booking_chart_layout_canvas", '', $tab, $range );

		if( file_exists( $file ) )
			require $file;
	}

}

new HB_Admin_Settings_Hook();