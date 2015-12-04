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
		$this->_includes();

		if( self::wc_enable() ) {
			/**
			 * define plugin enable
			 */
			define( 'HB_WC_ENABLE', TRUE );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			//add_action( 'hb_after_checkout_form', array( $this, 'checkout_form' ) );

			//add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'add_to_cart_handler' ), 10, 2 );
			add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
			//add_action( 'woocommerce_add_to_cart_handler_WC_Product_LPR_Course', array( $this, 'add_to_cart_handler_course' ) );
			//add_action( 'woocommerce_new_order', array( $this, 'add_order' ) );
			//add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 3 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );
			// add_action( 'woocommerce_remove_cart_item', array( $this, 'remove_cart_item' ), 10, 2 );

			//add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'after_cart_item_quantity_update' ), 10, 3 );
			// add_filter( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 10, 4 );


			/**
			 * woommerce currency
			 */
			add_filter( 'hb_currency', array( $this, 'woocommerce_currency' ), 50 );
			add_filter( 'hb_currency_symbol', array( $this, 'woocommerce_currency_symbol' ), 50, 2 );
			add_filter( 'hb_price_format', array( $this, 'woocommerce_price_format' ), 50, 3 );
			/**
			 * hook to tp-hotel-booking core
			 * create cart item
			 * remove cart item
			 */
			add_action( 'tp_hotel_booking_add_to_cart', array( $this, 'hotel_add_to_cart' ), 10, 3 );
			add_action( 'tp_hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 4 );

			/**
			 * Woocommerce hook
			 * woocommerce_remove_cart_item remove
			 * woocommerce_update_cart_validation update
			 */
			add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'woocommerce_update_cart' ), 10, 4 );
		}
		else
		{
			define( 'HB_WC_ENABLE', FALSE );
		}

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

		$cart_item_id = $woocommerce->cart->generate_cart_id( $session['id'], null, array(), $woo_cart_item_data );

		if( ! empty( $woocommerce->cart->get_cart_item( $cart_item_id ) ) )
		{
			if( isset( $session['quantity'] ) && $session['quantity'] == 0 )
			{
				return $woocommerce->cart->remove_cart_item( $cart_item_id );
			}
			$woocommerce->cart->cart_contents[ $cart_item_id ]['quantity'] = 0;

		}

		return $cart_item_id = $woocommerce->cart->add_to_cart( $session['id'], $session['quantity'], null, array(), $woo_cart_item_data );
	}

	/**
	 * hotel booking system remove cart item
	 * @param  [int] $room_id
	 * @param  [string] $time_key
	 * @param  [string] $check_in_date
	 * @param  [string] $check_out_date
	 * @return [boolean]
	 */
	public function hotel_remove_cart_item( $room_id, $time_key, $check_in_date, $check_out_date  )
	{
		global $woocommerce;
		$woo_cart_item_data = array(
				'search_key'		=> $time_key,
				'check_in_date' 	=> $check_in_date,
				'check_out_date' 	=> $check_out_date
			);

		$cart_item_id = $woocommerce->cart->generate_cart_id( $room_id, null, array(), $woo_cart_item_data );

		if( ! empty( $woocommerce->cart->get_cart_item( $cart_item_id ) ) )
			return $woocommerce->cart->remove_cart_item( $cart_item_id );
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
		remove_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item') );
		if( $cart_item = $cart->get_cart_item( $cart_item_key ) )
		{
			$room_id 		= $cart_item['product_id'];
			$search_key 	= $cart_item['search_key'];

			add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
			return HB_Cart::instance()->remove_cart_item( $room_id, $search_key );
		}
	}

	public function woocommerce_update_cart( $return, $cart_item_key, $values, $quantity )
	{
		global $woocommerce;
		if( $cart_item = $woocommerce->cart->get_cart_item( $cart_item_key ) )
		{
			$room_id 		= $cart_item[ 'product_id' ];
			$quantity 		= $quantity;
			$check_in_date 	= $cart_item[ 'check_in_date' ];
			$check_out_date	= $cart_item[ 'check_out_date' ];

			HB_Cart::instance()->add_to_cart( $room_id, $quantity, $check_in_date, $check_out_date );
		}

		return $return;
	}

	function get_cart_item_from_session( $session_data, $values, $key ){
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

	function product_class( $classname, $product_type, $post_type, $product_id ){
		if( 'hb_room' == $post_type ){
			$classname = 'HB_WC_Product_Room';
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
		if ( !self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function load(){

		if (!function_exists('is_plugin_active')) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}
		if( class_exists('WC_Install') && is_plugin_active('woocommerce/woocommerce.php') ){
			self::$_wc_loaded = true;
		}
		TP_Hotel_Booking_Woocommerce::instance();
		if( !self::$_wc_loaded ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		}
	}

	public static function admin_notice(){
		hb_wc_admin_view( 'wc-is-not-installed' );
	}

	public static function wc_enable(){
		return self::$_wc_loaded && HB_Settings::instance()->get('wc_enable') == 'yes';
	}

	public function frontend_scripts(){
		wp_enqueue_script( 'hb_wc_checkout', HB_WC_PLUGIN_URL . 'assets/js/frontend/checkout.js', array( 'jquery' ) );
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
		require_once "includes/functions.php";
		require_once "includes/class-hb-wc-settings.php";
		require_once "includes/class-hb-wc-product-room.php";
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
}

add_action( 'plugins_loaded', array( 'TP_Hotel_Booking_Woocommerce', 'load' ) );