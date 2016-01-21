<?php
/*
Plugin Name: TP Booking WooCommerce
Plugin URI: http://thimpress.com/
Description: Support paying for a booking with the payment methods provided by woocommerce
Author: ThimPress
Version: 0.9
Author URI: http://thimpress.com
Requires at least: 3.5
Tested up to: 4.3

Text Domain: tp-hotel-booking-woocommerce
Domain Path: /lang/
*/

/**
 * Class TP_Hotel_Booking_Woocommerce
 *
 * Main class
 */

class TP_Hotel_Booking_Woocommerce {
	/**
	 * @var null
	 *
	 * Hold the instance of TP_Hotel_Booking_Woocommerce
	 */
	protected static $_instance = null;

	protected static $_wc_loaded = false;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->_defines();

		require_once "includes/functions.php";
		require_once "includes/class-hb-wc-settings.php";
		if( self::wc_enable() ) {
			$this->_includes();
			/**
			 * define plugin enable
			 */
			define( 'HB_WC_ENABLE', TRUE );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			//add_action( 'hb_after_checkout_form', array( $this, 'checkout_form' ) );

			//add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'add_to_cart_handler' ), 10, 2 );
			add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
			//add_action( 'woocommerce_add_to_cart_handler_WC_Product_LPR_Course', array( $this, 'add_to_cart_handler_course' ) );
			// add_action( 'woocommerce_new_order', array( $this, 'woo_add_order' ) );
			//add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 3 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );
			// add_action( 'woocommerce_remove_cart_item', array( $this, 'remove_cart_item' ), 10, 2 );

			//add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'after_cart_item_quantity_update' ), 10, 3 );
			// add_filter( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 10, 4 );

			// tax enable
			add_filter( 'hotel_booking_extra_tax_enable', array( $this, 'tax_enable' ) );
			/**
			 * woommerce currency
			 */
			// currency hotel booking as WC currency
			add_filter( 'hb_currency', array( $this, 'woocommerce_currency' ), 50 );
			add_filter( 'tp_hotel_booking_payment_current_currency', array( $this, 'woocommerce_currency' ), 50 );
			add_filter( 'hb_currency_symbol', array( $this, 'woocommerce_currency_symbol' ), 50, 2 );
			add_filter( 'hb_price_format', array( $this, 'woocommerce_price_format' ), 50, 3 );
			/*
			 * filter price
			 */
			add_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10, 2 );
			add_filter( 'tp_hb_extra_package_regular_price_tax', array( $this, 'packages_regular_price_tax' ), 10, 3 ); // extra package regular

			/**
			 * hook to tp-hotel-booking core
			 * create cart item
			 * remove cart item
			 * remove extra packages
			 */
			// trigger WC cart room item
			add_action( 'tp_hotel_booking_add_to_cart', array( $this, 'hotel_add_to_cart' ), 10, 3 );
			// trigger WC remove cart room item
			add_action( 'tp_hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 4 );
			// trigger remove WC cart package item
			add_action( 'hb_extra_remove_package', array( $this, 'hotel_remove_package' ), 10, 3 );
			// return cart url
			add_filter( 'hb_cart_url', array( $this, 'hotel_cart_url' ) );
			// return checkout url
			add_filter( 'hb_checkout_url', array( $this, 'hotel_checkout_url' ) );
			// display tax price
			add_filter( 'hotel_booking_cart_tax_display', array( $this, 'cart_tax_display' ) );
			add_filter( 'hotel_booking_get_cart_total', array( $this, 'cart_result_display' ) );

			/**
			 * Woocommerce hook
			 * woocommerce_remove_cart_item remove
			 * woocommerce_update_cart_validation update
			 * woocommerce_restore_cart_item undo remove
			 */
			add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'woocommerce_update_cart' ), 10, 4 );
			add_action( 'woocommerce_restore_cart_item', array( $this, 'woocommerce_restore_cart_item' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_class', array( $this, 'woocommerce_cart_package_item_class' ), 10, 3 );
			// sort room - product item
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'woo_sort_rooms' ), 999 );

			// init share product hotel booking and WC
			add_action( 'wp_head', array( $this, 'init' ) );
		}
		else
		{
			define( 'HB_WC_ENABLE', FALSE );
		}

	}

	function init()
	{
		global $woocommerce;
		$hb_cart = HB_Cart::instance();
		if( $rooms = $hb_cart->get_rooms() )
		{
			foreach ( $rooms as $key => $room ) {
				$woo_cart_item_data = array(
						'search_key'		=> $room->search_key,
						'check_in_date' 	=> $room->check_in_date,
						'check_out_date' 	=> $room->check_out_date
					);

				$woo_cart_item_data = apply_filters( 'hb_wc_add_cart_data_item', $woo_cart_item_data );
				$cart_item_id = $woocommerce->cart->generate_cart_id( $room->ID, null, array(), $woo_cart_item_data );

				// get cart item id. add new item if it not exists
				if( ! empty( $woocommerce->cart->get_cart_item( $cart_item_id ) ) )
				{
					// $cart_item_id = $woocommerce->cart->add_to_cart( $room->ID, $room->quantity, null, array(), $woo_cart_item_data );
					if( $room->quantity == 0 )
					{
						$woocommerce->cart->remove_cart_item( $cart_item_id );
					}

					// set quantity = 0. if exists
					$woocommerce->cart->cart_contents[ $cart_item_id ]['quantity'] = 0;
				}

				if( $room->quantity )
				{
					// add new
					$cart_item_id = $woocommerce->cart->add_to_cart( $room->ID, $room->quantity, null, array(), $woo_cart_item_data );
				}

				// add extra packages to WC Cart
				if( $room->extra_packages )
				{
					foreach ( $room->extra_packages as $package_id => $package_quantity )
					{
						$package_cart_item_data = array(
								'cart_room_id'		=> $cart_item_id,
								'room_id'			=> $room->ID,
								'search_key'		=> $room->search_key,
								'check_in_date'		=> $room->check_in_date,
								'check_out_date' 	=> $room->check_out_date
							);
						$package_cart_item_data = apply_filters( 'hb_wc_add_cart_package_data_item', $package_cart_item_data );
						$package_cart_id = $woocommerce->cart->generate_cart_id( $package_id, null, array(), $package_cart_item_data );

						if( ! empty( $woocommerce->cart->get_cart_item( $package_cart_id ) ) )
						{

							if( $package_quantity == 0 || ( isset( $room->quantity ) && $room->quantity == 0 ) )
							{
								$woocommerce->cart->remove_cart_item( $package_cart_id );
							}

							// set quantity = 0. if exists
							$woocommerce->cart->cart_contents[ $package_cart_id ]['quantity'] = 0;

						}

						// add new package
						$woocommerce->cart->add_to_cart( $package_id, $package_quantity, null, array(), $package_cart_item_data );
					}
				}

			}
		}
		else
		{
			$cart_rooms = $woocommerce->cart->get_cart();
			foreach ( $cart_rooms as $cart_id => $room ) {
				if( isset( $room['search_key'] ) && isset( $room['check_in_date'] ) && isset( $room['check_out_date'] ) )
					$woocommerce->cart->remove_cart_item( $cart_id );
			}
		}
	}

	// tax enable
	function tax_enable( $enable )
	{
		// woocommercer option
		if( get_option( 'woocommerce_tax_display_shop' ) === 'incl' )
			return true;

		return false;
	}

	/**
	 * add_to_cart action
	 * @param [array] $session array room session
	 * @param [array] $posts   array of $GLOBALS[_POST];
	 */
	public function hotel_add_to_cart( $session, $posts )
	{
		global $woocommerce;
		$cart_items = $woocommerce->cart->get_cart();

		$woo_cart_item_data = array(
				'search_key'		=> $session['search_key'],
				'check_in_date' 	=> $session['check_in_date'],
				'check_out_date' 	=> $session['check_out_date']
			);

		$woo_cart_item_data = apply_filters( 'hb_wc_add_cart_data_item', $woo_cart_item_data );

		$cart_item_id = $woocommerce->cart->generate_cart_id( $session['id'], null, array(), $woo_cart_item_data );

		if( ! empty( $woocommerce->cart->get_cart_item( $cart_item_id ) ) )
		{

			if( isset( $session['quantity'] ) && $session['quantity'] == 0 )
			{
				do_action( 'hb_wc_before_add_zero_to_cart', $session, $posts, $woo_cart_item_data );
				$woocommerce->cart->remove_cart_item( $cart_item_id );
			}

			$woocommerce->cart->cart_contents[ $cart_item_id ]['quantity'] = 0;

		}

		if( $session['quantity'] )
		{
			do_action( 'hb_wc_before_add_to_cart', $session, $posts, $woo_cart_item_data );

			$cart_item_id = $woocommerce->cart->add_to_cart( $session['id'], $session['quantity'], null, array(), $woo_cart_item_data );
		}

		if( $cart_item_id && isset( $session['extra_packages'] ) && ! empty( $session['extra_packages'] ) )
		{
			foreach ( $session['extra_packages'] as $package_id => $package_quantity )
			{

				$package_cart_item_data = array(
						'cart_room_id'		=> $cart_item_id,
						'room_id'			=> $session['id'],
						// 'room_quantity'		=> $session['quantity'],
						'search_key'		=> $session['search_key'],
						'check_in_date'		=> $session['check_in_date'],
						'check_out_date' 	=> $session['check_out_date']
					);

				$package_cart_item_data = apply_filters( 'hb_wc_add_cart_package_data_item', $package_cart_item_data );
				$package_cart_id = $woocommerce->cart->generate_cart_id( $package_id, null, array(), $package_cart_item_data );

				if( ! empty( $woocommerce->cart->get_cart_item( $package_cart_id ) ) )
				{

					if( $package_quantity == 0 || ( isset( $session['quantity'] ) && $session['quantity'] == 0 ) )
					{
						$woocommerce->cart->remove_cart_item( $package_cart_id );
					}

					$woocommerce->cart->cart_contents[ $package_cart_id ]['quantity'] = 0;

				}

				$woocommerce->cart->add_to_cart( $package_id, $package_quantity, null, array(), $package_cart_item_data );
			}
		}

		do_action( 'hb_wc_after_add_to_cart', $session, $posts, $woo_cart_item_data );
	}

	/**
	 * hotel booking system remove cart item
	 * @param  [int] $room_id
	 * @param  [string] $time_key
	 * @param  [string] $check_in_date
	 * @param  [string] $check_out_date
	 * @return boolean
	 */
	public function hotel_remove_cart_item( $room_id, $time_key, $check_in_date, $check_out_date  )
	{
		global $woocommerce;
		$woo_cart_item_data = array(
				'search_key'		=> $time_key,
				'check_in_date' 	=> $check_in_date,
				'check_out_date' 	=> $check_out_date
			);

		$woo_cart_item_data = apply_filters( 'hb_wc_remove_cart_room_data_item', $woo_cart_item_data );

		$cart_item_id = $woocommerce->cart->generate_cart_id( $room_id, null, array(), $woo_cart_item_data );

		if( ! empty( $woocommerce->cart->get_cart_item( $cart_item_id ) ) )
			$woocommerce->cart->remove_cart_item( $cart_item_id );

		do_action( 'hb_wc_remove_cart_room_item', $cart_item_id );
	}

	/**
	 * woocommerce_remove_cart_item
	 * do remove cart item in hotel booking cart
	 * @param  $cart_item_key (string)
	 * @param  $cart 			(object class)
	 * @return boolean
	 */
	public function woocommerce_remove_cart_item( $cart_item_key, $cart )
	{
		remove_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item'), 10, 2 );
		if( $cart_item = $cart->get_cart_item( $cart_item_key ) )
		{
			if( ! isset( $cart_item['check_in_date'] ) && ! isset( $cart_item['check_out_date'] ) )
				return;

			$product_id 	= $cart_item['product_id'];
			$search_key 	= $cart_item['search_key'];

			add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );

			if( ! isset( $cart_item['cart_room_id'] ) )
			{
				HB_Cart::instance()->remove_cart_item( $product_id, $search_key );

				$cart_items = $cart->get_cart();

				foreach ( $cart_items as $cart_id => $item ) {
					if( isset( $item[ 'cart_room_id' ], $item[ 'room_id' ] )
						&& isset( $item[ 'check_in_date' ], $item[ 'check_out_date' ] )
						&& $item[ 'cart_room_id' ] === $cart_item_key
					)
					{
						$cart->remove_cart_item( $cart_id );
					}
				}
			}
			else
			{
				$room_id 	= $cart_item['room_id'];
				return HB_Extra_Cart::instance()->remove_package_item( $search_key, $room_id, $product_id );
			}
		}
	}

	/**
	 * woocommerce_update_cart update cart
	 * @param  [type] $return
	 * @param  [type] $cart_item_key
	 * @param  [type] $values
	 * @param  [type] $quantity
	 * @return boolean
	 */
	public function woocommerce_update_cart( $return, $cart_item_key, $values, $quantity )
	{
		global $woocommerce;
		if( $cart_item = $woocommerce->cart->get_cart_item( $cart_item_key ) )
		{
			if( ! isset( $cart_item['check_in_date'] ) && ! isset( $cart_item['check_out_date'] ) )
				return $return;

			if( ! isset( $cart_item['room_id'] ) && ! isset( $cart_item['cart_room_id'] ) )
			{
				$room_id 		= $cart_item[ 'product_id' ];
				$quantity 		= $quantity;
				$check_in_date 	= $cart_item[ 'check_in_date' ];
				$check_out_date	= $cart_item[ 'check_out_date' ];

				HB_Cart::instance()->add_to_cart( $room_id, $quantity, $check_in_date, $check_out_date );
			}
			else if( isset( $cart_item[ 'room_id' ] ) )
			{
				HB_Extra_Cart::instance()->add_room_package( $cart_item[ 'search_key' ], $cart_item[ 'room_id' ], $cart_item[ 'product_id' ], $quantity );
			}
		}

		do_action( 'hb_wc_update_cart', $return, $cart_item_key, $values, $quantity );

		return apply_filters( 'hb_wc_update_cart_return', $return, $cart_item_key, $values, $quantity );
	}

	/**
	 * woocommerce_restore_cart_item undo remove cart item
	 * @param  	string $cart_item_id
	 * @param 	object class WC_Cart
	 * @return 	boolean
	 */
	public function woocommerce_restore_cart_item( $cart_item_id, $cart )
	{
		if( ! $cart_item = $cart->get_cart_item( $cart_item_id ) )
			return;

		if( ! isset( $cart_item[ 'check_in_date' ] ) || ! isset( $cart_item[ 'check_out_date' ] ) || ! isset( $cart_item[ 'search_key' ] ) ) return;

		do_action( 'hb_wc_restore_cart_item', $cart_item_id, $cart );

		if( ! isset( $cart_item[ 'cart_room_id' ] ) && ! isset( $cart_item[ 'room_id' ] ) )
		{
			/**
			 * room restore
			 * @var
			 */
			$hb_cart = HB_Cart::instance();

			$results = $hb_cart->add_to_cart( $cart_item['product_id'], $cart_item['quantity'], $cart_item[ 'check_in_date' ], $cart_item[ 'check_out_date' ] );

			// $rooms = $results->get_products();

			// if( ! isset( $rooms[ $cart_item[ 'search_key' ] ] )
			// 	|| ! isset( $cart_item[ 'search_key' ][ $cart_item['product_id'] ] )
			// 	|| ! isset( $cart_item[ 'search_key' ][ $cart_item['product_id'] ] )
				// ) return;


		}
		else
		{
			$package = HB_Extra_Cart::instance();
			return $package->add_room_package( $cart_item[ 'search_key' ], $cart_item[ 'room_id' ], $cart_item[ 'product_id' ], $cart_item[ 'quantity' ] );
		}

		do_action( 'hb_wc_restored_cart_item', $cart_item_id, $cart );
		return true;
	}

	/**
	 * remove extra package
	 * @return [type] [description]
	 */
	function hotel_remove_package( $package_id, $room_id, $time_key )
	{
		list( $start_in, $end_in ) = explode( '_', $time_key );

		$start_in = date( 'm/d/Y', $start_in );
		$end_in = date( 'm/d/Y', $end_in );

		global $woocommerce;

		$woo_cart_item_data = array(
				'search_key'		=> $time_key,
				'check_in_date' 	=> $start_in,
				'check_out_date' 	=> $end_in
			);

		$woo_cart_item_data = apply_filters( 'hb_wc_add_cart_data_item', $woo_cart_item_data );

		$cart_room_item_id = $woocommerce->cart->generate_cart_id( $room_id, null, array(), $woo_cart_item_data );

		$package_cart_item_data = array(
				'cart_room_id'		=> $cart_room_item_id,
				'room_id'			=> $room_id,
				'search_key'		=> $time_key,
				'check_in_date' 	=> $start_in,
				'check_out_date' 	=> $end_in
			);

		$package_cart_item_data = apply_filters( 'hb_wc_add_cart_package_data_item', $package_cart_item_data );
		$package_cart_id = $woocommerce->cart->generate_cart_id( $package_id, null, array(), $package_cart_item_data );

		if( $woocommerce->cart->get_cart_item( $package_cart_id ) )
		{
			$woocommerce->cart->remove_cart_item( $package_cart_id );
		}
	}

	function get_cart_item_from_session( $session_data, $values, $key )
	{
		$session_data['data']->data = $values;
		return $session_data;
	}

	/**
	 * add cart item data
	 * @param [type] $cart_item [description]
	 * @param [type] $cart_id   [description]
	 */
	function add_cart_item( $cart_item, $cart_id ){
		if( $cart_item['data']->post->post_type == 'hb_room' ) {
			$cart_item['data']->data = array(
				'search_key'     => $cart_item['search_key'],
				// 'quantity'       => $cart_item['quantity'], // quantity generate error cart_item_key if it exists
				'check_in_date'  => $cart_item['check_in_date'],
				'check_out_date' => $cart_item['check_out_date'],
				'wc_cart_id'	=> $cart_id
			);

		}
		return $cart_item;
	}

	/**
	 * woocommerce_cart_package_item_class
	 * @param  string $class
	 * @param  array $cart_item
	 * @param  string $cart_item_key
	 * @return string $class
	 */
	function woocommerce_cart_package_item_class( $class, $cart_item, $cart_item_key )
	{

		$class = array(
				$class
			);

		if( isset( $cart_item['search_key'] ) && ! isset( $cart_item['cart_room_id'] ) )
			$class[] = 'hb_wc_cart_room_item';

		if( isset( $cart_item['cart_room_id'] ) && isset( $cart_item['room_id'] ) )
			$class[] = 'hb_wc_cart_package_item';

		return implode( ' ', $class );
	}

	/**
	 * woo_add_order
	 * @param $order_id
	 */
	public function woo_add_order( $order_id )
	{
		// var_dump($order_id); die();
	}

	/**
	 * hotel_cart_url
	 * @param  string url address
	 * @return woocommerce cart url
	 */
	public function hotel_cart_url( $url )
	{
		global $woocommerce;
		if( ! $woocommerce->cart )
			return $url;

		$url = $woocommerce->cart->get_cart_url();
		return $url;
	}

	/**
	 * hotel_checkout_url
	 * @param  string url address
	 * @return woocommerce checkout url
	 */
	public function hotel_checkout_url( $url )
	{
		global $woocommerce;
		if( ! $woocommerce->cart )
			return $url;

		$url = $woocommerce->cart->get_checkout_url();
		return $url;
	}

	function product_class( $classname, $product_type, $post_type, $product_id ){
		if( 'hb_room' == $post_type ){
			$classname = 'HB_WC_Product_Room';
		}
		else if( 'hb_extra_room' == $post_type )
		{
			$classname = 'HB_WC_Product_Package';
		}
		return $classname;
	}

	function add_to_cart_handler_booking( $type, $product ){
		if( get_post_type( $product->id ) == 'hb_room' ) {
			return 'HB_WC_Product_Room';
		}
		return $type;
	}

	private function _parse_request(){
		$segments = parse_url( hb_get_request( '_wp_http_referer' ) );
		$request = false;
		if( !empty( $segments['query'] ) ){
			parse_str( $segments['query'], $params );
			if( !empty( $params['hotel-booking-params'] ) ){
				$param_str = base64_decode( $params['hotel-booking-params'] );
				$request = unserialize( $param_str );
			}
		}
		return $request;
	}

	/**
	 * Ensure that only one instance of TP_Hotel_Booking_Woocommerce is loaded in a process
	 *
	 * @return null|TP_Hotel_Booking_Woocommerce
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function load(){

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if( class_exists( 'TP_Hotel_Booking' ) && is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) )
		{
			self::$_wc_loaded = true;
		}

		if( self::$_wc_loaded === TRUE && class_exists('WC_Install') && is_plugin_active('woocommerce/woocommerce.php') ){
			self::$_wc_loaded = true;
		}
		else
		{
			self::$_wc_loaded = false;
		}

		TP_Hotel_Booking_Woocommerce::instance();
		if( ! self::$_wc_loaded ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		}
	}

	public static function admin_notice(){
		if( ! class_exists( 'HB_Settings' ) )
			return;

		if( function_exists( 'hb_wc_admin_view' ) )
		{
			hb_wc_admin_view( 'wc-is-not-installed' );
		}
	}

	public static function wc_enable(){
		if( ! class_exists( 'HB_Settings' ) )
			return;
		return self::$_wc_loaded && HB_Settings::instance()->get('wc_enable') == 'yes';
	}

	public function frontend_scripts(){
		wp_enqueue_script( 'hb_wc_checkout', HB_WC_PLUGIN_URL . 'assets/js/frontend/checkout.js', array( 'jquery' ) );
		wp_enqueue_style( 'hb_wc_site', HB_WC_PLUGIN_URL . 'assets/css/frontend/site.css' );
	}

	/**
	 * Define constants
	 */
	private function _defines() {
		define( 'HB_WC_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'HB_WC_PLUGIN_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
	}

	/**
	 * Including library files
	 */
	private function _includes() {
		if( ! class_exists( 'HB_Settings' ) ) return;

		require_once "includes/class-hb-wc-product-room.php";
		require_once "includes/class-hb-wc-product-package.php";
		require_once "includes/class-hb-wc-checkout.php";
		require_once "includes/class-hb-wc-booking.php";
		$this->settings = HB_Settings::instance();
	}

	/**
	 * return currency of woocommerce setting
	 * @param  string CURRENCY
	 * @return string CURRENCY
	 */
	public function woocommerce_currency( $currency )
	{
		return get_woocommerce_currency();
	}

	/**
	 * return currency symbol of woocommerce setting
	 * @param  string $symbol   symbol
	 * @param  string $currency currency
	 * @return string $symbol   symbol
	 */
	public function woocommerce_currency_symbol( $symbol, $currency )
	{
		return get_woocommerce_currency_symbol();
	}

	/**
	 * woocommerce_price_format get price within currency format using woocommerce setting
	 * @param  price formated $price_format
	 * @param  (float) $price         price
	 * @param  (string) $with_currency currency setting tp-hotel-booking
	 * @return string price formated
	 */
	public function woocommerce_price_format( $price_format, $price, $with_currency )
	{
		return wc_price( $price );
	}

	/**
	 * room tax
	 * @param  [type] $tax_price
	 * @param  [type] $room
	 * @param  [type] $tax
	 * @return [type]
	 */
	function room_price_tax( $tax_price, $room )
	{
		// woo get price
		$product = new WC_Product( $room->post->ID );
		$price = $room->get_total( $room->check_in_date, $room->check_out_date, 1, false, false );

		$price_incl_tax = $product->get_price_including_tax( $price, $room->quantity );
		$price_excl_tax = $product->get_price_excluding_tax( $price, $room->quantity );

		return $price_incl_tax - $price_excl_tax;
	}

	/**
	 * package tax price
	 * @param  [type] $tax_price [description]
	 * @param  [type] $room      [description]
	 * @param  [type] $tax       [description]
	 * @return [type]            [description]
	 */
	function packages_regular_price_tax( $tax_price, $price, $package )
	{
		$product = new WC_Product( $package->ID );
		$price = $package->get_regular_price();
		// $price = $product->get_price();
		$price_incl_tax = $product->get_price_including_tax( $price, 1 );
		$price_excl_tax = $product->get_price_excluding_tax( $price, 1 );

		return $price_incl_tax - $price_excl_tax;
	}

	/**
	 * cart_tax_display return tax price total
	 * @param  [type] $display [description]
	 * @return [type]          [description]
	 */
	function cart_tax_display( $display )
	{
		global $woocommerce;
		return wc_price( $woocommerce->cart->get_taxes_total() );
	}

	/**
	 * cart result total
	 * @param  [type] $display [description]
	 * @return [type]          [description]
	 */
	function cart_result_display( $display )
	{
		global $woocommerce;
		return $woocommerce->cart->subtotal;
	}

	/**
	 * woo_sort_rooms sort room as product with extra packages
	 * @return null
	 */
	public function woo_sort_rooms()
	{
		global $woocommerce;

		$woo_cart_contents = array();

		// cart contents items
		$cart_items = $woocommerce->cart->cart_contents;

		foreach ( $cart_items as $cart_id => $item ) {

			if( ! isset( $item['search_key'] ) )
			{
				$woo_cart_contents[ $cart_id ] = $item;
				continue;
			}

			if( ! isset( $item[ 'cart_room_id' ] ) && ! isset( $item[ 'room_id' ] ) )
			{
				$woo_cart_contents[ $cart_id ] = $item;

				foreach ( $cart_items as $cart_package_id => $package ) {
					if( ! isset( $package[ 'search_key' ] ) || ! isset( $package[ 'cart_room_id' ] ) || ! isset( $package[ 'room_id' ] ) )
						continue;

					if( $package[ 'cart_room_id' ] === $cart_id && $package[ 'room_id' ] === $item[ 'product_id' ] )
					{
						$woo_cart_contents[ $cart_package_id ] = $package;
					}
				}
			}

		}

		$woocommerce->cart->cart_contents = $woo_cart_contents;
	}

}

add_action( 'plugins_loaded', array( 'TP_Hotel_Booking_Woocommerce', 'load' ) );
