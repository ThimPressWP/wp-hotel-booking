<?php

/*
  Plugin Name: WP Hotel Booking WooCommerce
  Plugin URI: http://thimpress.com/
  Description: Support paying for a booking with the payment system provided by WooCommerce
  Author: ThimPress
  Version: 1.7
  Author URI: http://thimpress.com
  Requires at least: 3.5
  Tested up to: 4.3

  Text Domain: wp-hotel-booking-woocommerce
  Domain Path: /lang/
 */

/**
 * Class WP_Hotel_Booking_Woocommerce
 *
 * Main class
 */
class WP_Hotel_Booking_Woocommerce {

	/**
	 * @var null
	 *
	 * Hold the instance of WP_Hotel_Booking_Woocommerce
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
		if ( self::wc_enable() ) {
			$this->_includes();
			/**
			 * define plugin enable
			 */
			define( 'HB_WC_ENABLE', TRUE );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			// /**
			//  * woommerce currency
			//  */
			// // currency hotel booking as WC currency
			add_filter( 'hb_currency', array( $this, 'woocommerce_currency' ), 50 );
			add_filter( 'hotel_booking_payment_current_currency', array( $this, 'woocommerce_currency' ), 50 );
			add_filter( 'hb_currency_symbol', array( $this, 'woocommerce_currency_symbol' ), 50, 2 );
			add_filter( 'hb_price_format', array( $this, 'woocommerce_price_format' ), 50, 3 );
			// /*
			//  * filter price
			//  */
			// room price
			add_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10, 2 );
			// extra package price
			add_filter( 'hotel_booking_extra_package_regular_price_incl_tax', array( $this, 'packages_regular_price_tax' ), 10, 3 );
			// cart amount(item total price)
			add_filter( 'hotel_booking_cart_item_total_amount', array( $this, 'hotel_booking_cart_item_total_amount' ), 10, 4 );
			// add_filter( 'hotel_booking_cart_item_amount_singular', array( $this, 'hotel_booking_cart_item_amount_singular' ), 10, 4 );
			// tax enable
			add_filter( 'hb_price_including_tax', array( $this, 'hb_price_including_tax' ), 10, 2 );
			/**
			 * hook to tp-hotel-booking core
			 * create cart item
			 * remove cart item
			 * remove extra packages
			 */
			// trigger WC cart room item
			add_filter( 'hotel_booking_added_cart', array( $this, 'hotel_add_to_cart' ), 10, 2 );
			// trigger WC remove cart room item
			add_action( 'hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 2 );
			// return cart url
			add_filter( 'hb_cart_url', array( $this, 'hotel_cart_url' ) );
			// return checkout url
			add_filter( 'hb_checkout_url', array( $this, 'hotel_checkout_url' ), 999 );
			// display tax price
			add_filter( 'hotel_booking_cart_tax_display', array( $this, 'cart_tax_display' ) );
			add_filter( 'hotel_booking_get_cart_total', array( $this, 'cart_total_result_display' ) );
			add_action( 'hb_booking_status_changed', array( $this, 'hb_booking_status_changed' ), 10, 3 );
			add_action( 'template_redirect', array( $this, 'template_redirect' ), 50 );
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
			add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );
			// sort room - product item
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'woo_sort_rooms' ), 999 );

			add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 2 );
			// // tax enable
			add_filter( 'hotel_booking_extra_tax_enable', array( $this, 'tax_enable' ) );
		} else {
			define( 'HB_WC_ENABLE', FALSE );
		}
	}

	// tax enable
	function tax_enable( $enable ) {
		// woocommercer option
		if ( get_option( 'woocommerce_tax_display_shop' ) === 'incl' )
			return true;

		return false;
	}

	/**
	 * add_to_cart action
	 *
	 * @param [array] $session array room session
	 * @param [array] $posts   array of $GLOBALS[_POST];
	 */
	public function hotel_add_to_cart( $cart_item_id, $params ) {
		// remove_action( 'hotel_booking_added_cart', array( $this, 'hotel_add_to_cart' ), 10, 2 );

		global $woocommerce;

		if ( !$woocommerce || !$woocommerce->cart ) return '';

		$cart_items = $woocommerce->cart->get_cart();

		$woo_cart_param = array(
			'product_id'     => $params['product_id'],
			'check_in_date'  => $params['check_in_date'],
			'check_out_date' => $params['check_out_date']
		);

		if ( isset( $params['parent_id'] ) ) {
			$woo_cart_param['parent_id'] = $params['parent_id'];
		}

		$woo_cart_param = apply_filters( 'hotel_booking_wc_cart_params', $woo_cart_param, $cart_item_id );

		$woo_cart_id = $woocommerce->cart->generate_cart_id( $woo_cart_param['product_id'], null, array(), $woo_cart_param );
		if ( array_key_exists( $woo_cart_id, $cart_items ) ) {
			$woocommerce->cart->set_quantity( $woo_cart_id, $params['quantity'] );
		} else {
			// WC()->cart->add_to_cart( $woo_cart_param['product_id'], $params['quantity'] );
			$woo_cart_id = $woocommerce->cart->add_to_cart( $woo_cart_param['product_id'], $params['quantity'], null, array(), $woo_cart_param );
		}

		// add_action( 'hotel_booking_added_cart', array( $this, 'hotel_add_to_cart' ), 10, 2 );

		do_action( 'hb_wc_after_add_to_cart', $cart_item_id, $params );

		return $cart_item_id;
	}

	/**
	 * hotel booking system remove cart item
	 *
	 * @param  [int] $room_id
	 * @param  [string] $time_key
	 * @param  [string] $check_in_date
	 * @param  [string] $check_out_date
	 *
	 * @return boolean
	 */
	public function hotel_remove_cart_item( $cart_item_id, $remove_params ) {
		remove_action( 'hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 2 );
		global $woocommerce;

		$woo_cart_items = $woocommerce->cart->cart_contents;

		$woo_cart_id = $woocommerce->cart->generate_cart_id( $remove_params['product_id'], null, array(), $remove_params );

		if ( array_key_exists( $woo_cart_id, $woo_cart_items ) ) {
			$woocommerce->cart->remove_cart_item( $woo_cart_id );
		}

		if ( !isset( $remove_params['parent_id'] ) ) {
			foreach ( $woo_cart_items as $cart_id => $cart_item ) {
				# code...
				if ( !isset( $cart_item['check_in_date'] ) || !isset( $cart_item['check_out_date'] ) || !isset( $cart_item['parent_id'] ) ) {
					continue;
				}

				if ( $cart_item['parent_id'] === $cart_item_id ) {
					$woocommerce->cart->remove_cart_item( $cart_id );
				}
			}
		}

		add_action( 'hotel_booking_remove_cart_item', array( $this, 'hotel_remove_cart_item' ), 10, 2 );
		do_action( 'hb_wc_remove_cart_room_item', $cart_item_id );
	}

	/**
	 * woocommerce_remove_cart_item
	 * do remove cart item in hotel booking cart
	 *
	 * @param  $cart_item_key (string)
	 * @param  $cart          (object class)
	 *
	 * @return boolean
	 */
	public function woocommerce_remove_cart_item( $cart_item_key, $cart ) {
		remove_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
		if ( $cart_item = $cart->get_cart_item( $cart_item_key ) ) {
			if ( !isset( $cart_item['check_in_date'] ) && !isset( $cart_item['check_out_date'] ) )
				return;

			add_action( 'woocommerce_remove_cart_item', array( $this, 'woocommerce_remove_cart_item' ), 10, 2 );
			$hotel_cart_param = array(
				'product_id'     => $cart_item['product_id'],
				'check_in_date'  => $cart_item['check_in_date'],
				'check_out_date' => $cart_item['check_out_date']
			);

			if ( isset( $cart_item['parent_id'] ) ) {
				$hotel_cart_param['parent_id'] = $cart_item['parent_id'];
			}

			$hotel_cart_id = WP_Hotel_Booking::instance()->cart->generate_cart_id( $hotel_cart_param );

			$hotel_cart_contents = WP_Hotel_Booking::instance()->cart->cart_contents;

			if ( array_key_exists( $hotel_cart_id, $hotel_cart_contents ) ) {
				WP_Hotel_Booking::instance()->cart->remove_cart_item( $hotel_cart_id );
			}
		}
	}

	/**
	 * woocommerce_update_cart update cart
	 *
	 * @param  [type] $return
	 * @param  [type] $cart_item_key
	 * @param  [type] $values
	 * @param  [type] $quantity
	 *
	 * @return boolean
	 */
	public function woocommerce_update_cart( $return, $cart_item_key, $values, $quantity ) {
		global $woocommerce;
		if ( $cart_item = $woocommerce->cart->get_cart_item( $cart_item_key ) ) {
			if ( !isset( $cart_item['check_in_date'] ) && !isset( $cart_item['check_out_date'] ) )
				return $return;

			$hotel_cart_items = WP_Hotel_Booking::instance()->cart->cart_contents;

			// param render hotel cart id
			$hotel_cart_param = array(
				'product_id'     => $cart_item['product_id'],
				'check_in_date'  => $cart_item['check_in_date'],
				'check_out_date' => $cart_item['check_out_date']
			);

			if ( isset( $cart_item['parent_id'] ) ) {
				$hotel_cart_param['parent_id'] = $cart_item['parent_id'];
			}

			// hotel cart id
			$hotel_cart_id = WP_Hotel_Booking::instance()->cart->generate_cart_id( $hotel_cart_param );
			WP_Hotel_Booking::instance()->cart->update_cart_item( $hotel_cart_id, $quantity );
		}

		do_action( 'hb_wc_update_cart', $return, $cart_item_key, $values, $quantity );

		return apply_filters( 'hb_wc_update_cart_return', $return, $cart_item_key, $values, $quantity );
	}

	/**
	 * woocommerce_restore_cart_item undo remove cart item
	 *
	 * @param    string $cart_item_id
	 * @param           object class WC_Cart
	 *
	 * @return    boolean
	 */
	public function woocommerce_restore_cart_item( $cart_item_id, $cart ) {
		if ( !$cart_item = $cart->get_cart_item( $cart_item_id ) )
			return;

		if ( !isset( $cart_item['check_in_date'] ) || !isset( $cart_item['check_out_date'] ) )
			return;

		do_action( 'hb_wc_restore_cart_item', $cart_item_id, $cart );

		// param render hotel cart id
		$hotel_cart_param = array(
			'product_id'     => $cart_item['product_id'],
			'check_in_date'  => $cart_item['check_in_date'],
			'check_out_date' => $cart_item['check_out_date']
		);

		if ( isset( $cart_item['parent_id'] ) ) {
			$hotel_cart_param['parent_id'] = $cart_item['parent_id'];
		}

		WP_Hotel_Booking::instance()->cart->add_to_cart( $cart_item['product_id'], $hotel_cart_param, $cart_item['quantity'] );

		do_action( 'hb_wc_restored_cart_item', $cart_item_id, $cart );
		return true;
	}

	function get_cart_item_from_session( $session_data, $values, $key ) {
		$session_data['data']->data = $values;
		return $session_data;
	}

	/**
	 * woocommerce_cart_package_item_class
	 *
	 * @param  string $class
	 * @param  array  $cart_item
	 * @param  string $cart_item_key
	 *
	 * @return string $class
	 */
	function woocommerce_cart_package_item_class( $class, $cart_item, $cart_item_key ) {

		$class = array(
			$class
		);

		if ( !isset( $cart_item['check_in_date'] ) || !isset( $cart_item['check_in_date'] ) ) {
			return implode( ' ', $class );
		}

		if ( !isset( $cart_item['parent_id'] ) ) {
			$class[] = 'hb_wc_cart_room_item';
		} else {
			$class[] = 'hb_wc_cart_package_item';
		}

		return implode( ' ', $class );
	}

	// woo change status
	function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
		if ( $booking_id = hb_get_post_id_meta( '_hb_woo_order_id', $order_id ) ) {
			if ( in_array( $new_status, array( 'completed', 'pending', 'processing', 'cancelled' ) ) ) {
				WPHB_Booking::instance( $booking_id )->update_status( $new_status );
			} else {
				WPHB_Booking::instance( $booking_id )->update_status( 'pending' );
			}
		}
	}

	/**
	 * hotel_cart_url
	 *
	 * @param  string url address
	 *
	 * @return woocommerce cart url
	 */
	public function hotel_cart_url( $url ) {
		global $woocommerce;
		if ( !$woocommerce->cart ) {
			return $url;
		}

		$url = $woocommerce->cart->get_cart_url() ? $woocommerce->cart->get_cart_url() : $url;
		return $url;
	}

	/**
	 * hotel_checkout_url
	 *
	 * @param  string url address
	 *
	 * @return woocommerce checkout url
	 */
	public function hotel_checkout_url( $url ) {
		global $woocommerce;
		if ( !$woocommerce->cart )
			return $url;

		$url = $woocommerce->cart->get_checkout_url() ? $woocommerce->cart->get_checkout_url() : $url;
		return $url;
	}

	// woo product class process
	function product_class( $classname, $product_type, $post_type, $product_id ) {
		if ( 'hb_room' == get_post_type( $product_id ) ) {
			$classname = 'HB_WC_Product_Room';
		} else if ( 'hb_extra_room' == get_post_type( $product_id ) ) {
			$classname = 'HB_WC_Product_Package';
		}
		return $classname;
	}

	private function _parse_request() {
		$segments = parse_url( hb_get_request( '_wp_http_referer' ) );
		$request  = false;
		if ( !empty( $segments['query'] ) ) {
			parse_str( $segments['query'], $params );
			if ( !empty( $params['hotel-booking-params'] ) ) {
				$param_str = base64_decode( $params['hotel-booking-params'] );
				$request   = unserialize( $param_str );
			}
		}
		return $request;
	}

	// add product class param
	function add_cart_item( $cart_item, $cart_id ) {
		if ( in_array( $cart_item['data']->post->post_type, array( 'hb_room', 'hb_extra_room' ) ) ) {
			$cart_item['data']->data = array(
				'product_id'     => $cart_item['product_id'],
				'check_in_date'  => $cart_item['check_in_date'],
				'check_out_date' => $cart_item['check_out_date'],
				'woo_cart_id'    => $cart_id
			);
			if ( $cart_item['data']->post->post_type === 'hb_extra_room' ) {
				$cart_item['data']->data['parent_id'] = $cart_item['parent_id'];
			}
		}

		return $cart_item;
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

	public static function load() {

		if ( !function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ( class_exists( 'TP_Hotel_Booking' ) && is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) ) || ( is_plugin_active( 'wp-hotel-booking/wp-hotel-booking.php' ) && class_exists( 'WP_Hotel_Booking' ) ) ) {
			self::$_wc_loaded = true;
		}

		if ( self::$_wc_loaded === TRUE && class_exists( 'WC_Install' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			self::$_wc_loaded = true;
		} else {
			self::$_wc_loaded = false;
		}

		WP_Hotel_Booking_Woocommerce::instance();
		if ( !self::$_wc_loaded ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		}

		self::load_text_domain();
	}

	public static function load_text_domain() {
		$default     = WP_LANG_DIR . '/plugins/wp-hotel-booking-woocommerce-' . get_locale() . '.mo';
		$plugin_file = HB_WC_PLUGIN_PATH . '/languages/wp-hotel-booking-woocommerce-' . get_locale() . '.mo';
		$file        = false;
		if ( file_exists( $default ) ) {
			$file = $default;
		} else {
			$file = $plugin_file;
		}
		if ( $file ) {
			load_textdomain( 'wp-hotel-booking-woocommerce', $file );
		}
	}

	public static function admin_notice() {
		if ( !class_exists( 'WPHB_Settings' ) )
			return;

		if ( function_exists( 'hb_wc_admin_view' ) ) {
			hb_wc_admin_view( 'wc-is-not-installed' );
		}
	}

	public static function wc_enable() {
		if ( !class_exists( 'WPHB_Settings' ) )
			return;
		return self::$_wc_loaded && WPHB_Settings::instance()->get( 'wc_enable' ) == 'yes';
	}

	public function frontend_scripts() {
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
		if ( !class_exists( 'WPHB_Settings' ) )
			return;

		require_once "includes/class-hb-wc-product-room.php";
		require_once "includes/class-hb-wc-product-package.php";
		require_once "includes/class-hb-wc-checkout.php";
		require_once "includes/class-hb-wc-booking.php";
		$this->settings = WPHB_Settings::instance();
	}

	/**
	 * return currency of woocommerce setting
	 *
	 * @param  string CURRENCY
	 *
	 * @return string CURRENCY
	 */
	public function woocommerce_currency( $currency ) {
		return get_woocommerce_currency();
	}

	/**
	 * return currency symbol of woocommerce setting
	 *
	 * @param  string $symbol   symbol
	 * @param  string $currency currency
	 *
	 * @return string $symbol   symbol
	 */
	public function woocommerce_currency_symbol( $symbol, $currency ) {
		return get_woocommerce_currency_symbol( $currency );
	}

	/**
	 * woocommerce_price_format get price within currency format using woocommerce setting
	 *
	 * @param  price formated $price_format
	 * @param  (float) $price         price
	 * @param  (string) $with_currency currency setting tp-hotel-booking
	 *
	 * @return string price formated
	 */
	public function woocommerce_price_format( $price_format, $price, $with_currency ) {
		return wc_price( $price );
	}

	/**
	 * room tax
	 *
	 * @param  [type] $tax_price
	 * @param  [type] $room
	 * @param  [type] $tax
	 *
	 * @return [type]
	 */
	function room_price_tax( $tax_price, $room ) {
		remove_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10, 2 );
		// woo get price
		$product = new WC_Product( $room->post->ID );

		add_filter( 'hotel_booking_room_total_price_incl_tax', array( $this, 'room_price_tax' ), 10, 2 );

		$price_incl_tax = $product->get_price_including_tax( $room->get_data( 'quantity' ), $room->amount_singular_exclude_tax );
		$price_excl_tax = $product->get_price_excluding_tax( $room->get_data( 'quantity' ), $room->amount_singular_exclude_tax );

		return $price_incl_tax - $price_excl_tax;
	}

	/**
	 * package tax price
	 *
	 * @param  [type] $tax_price [description]
	 * @param  [type] $room      [description]
	 * @param  [type] $tax       [description]
	 *
	 * @return [type]            [description]
	 */
	function packages_regular_price_tax( $tax_price, $price, $package ) {
		$product = new WC_Product( $package->ID );
		$price   = $package->amount_singular_exclude_tax();
		// $price = $product->get_price();
		$price_incl_tax = $product->get_price_including_tax( 1, $price );
		$price_excl_tax = $product->get_price_excluding_tax( 1, $price );

		return $price_incl_tax - $price_excl_tax;
	}

	function hb_price_including_tax( $tax, $cart ) {
		if ( !$cart ) {
			return $tax;
		}
		if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
			$tax = true;
		}
		return $tax;
	}

	/*
	  | Cart item total amount
	 */

	function hotel_booking_cart_item_total_amount( $amount, $cart_id, $cart_item, $product ) {
		return $amount;
	}

	function hotel_booking_cart_item_amount_singular( $amount, $cart_id, $cart_item, $product ) {
		if ( wc_tax_enabled() && get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
			// woo get price
			if ( get_post_type( $cart_item->product_id ) === 'hb_room' ) {
				$woo_product = new WC_Product( $cart_item->product_id );
				$price       = $product->get_total( $cart_item->check_in_date, $product->check_out_date, $cart_item->quantity, false, false );

				$amount = $woo_product->get_price_including_tax( $price, $product->quantity );
			}
		}
		return $amount;
	}

	/**
	 * cart_tax_display return tax price total
	 *
	 * @param  [type] $display [description]
	 *
	 * @return [type]          [description]
	 */
	function cart_tax_display( $display ) {
		global $woocommerce;
		return wc_price( $woocommerce->cart->get_taxes_total() );
	}

	/**
	 * cart result total
	 *
	 * @param  [type] $display [description]
	 *
	 * @return [type]          [description]
	 */
	function cart_total_result_display( $display ) {
		global $woocommerce;
		return wc_price( $woocommerce->cart->total );
	}

	// change status booking order
	function hb_booking_status_changed( $booking_id, $old_status, $new_status ) {
		remove_action( 'hb_booking_status_changed', array( $this, 'hb_booking_status_changed' ), 10, 3 );

		$booking = WPHB_Booking::instance( $booking_id );

		$woo_order_id = $booking->woo_order_id;

		if ( $woo_order_id ) {
			$order = new WC_Order( $woo_order_id );
			if ( in_array( $new_status, array( 'completed', 'pending', 'processing' ) ) ) {
				$order->update_status( $new_status );
			} else {
				$order->update_status( 'pending' );
			}
		}

		add_action( 'hb_booking_status_changed', array( $this, 'hb_booking_status_changed' ), 10, 3 );
	}

	// redỉrect room-checkout, my-rooms => cart, checkout woocommerce page
	function template_redirect() {
		global $post;
		if ( !$post ) {
			return;
		}
		if ( $post->ID == hb_get_page_id( 'cart' ) ) {
			wp_redirect( WC()->cart->get_cart_url() );
			exit();
		} else if ( $post->ID == hb_get_page_id( 'checkout' ) ) {
			wp_redirect( WC()->cart->get_checkout_url() );
			exit();
		}
	}

	/**
	 * woo_sort_rooms sort room as product with extra packages
	 * @return null
	 */
	public function woo_sort_rooms() {
		global $woocommerce;

		$woo_cart_contents = array();

		// cart contents items
		$cart_items = $woocommerce->cart->cart_contents;

		foreach ( $cart_items as $cart_id => $item ) {

			if ( !isset( $item['check_in_date'] ) || !isset( $item['check_out_date'] ) ) {
				$woo_cart_contents[$cart_id] = $item;
				continue;
			}

			if ( !isset( $item['parent_id'] ) ) {
				$woo_cart_contents[$cart_id] = $item;

				$param     = array(
					'product_id'     => $item['product_id'],
					'check_in_date'  => $item['check_in_date'],
					'check_out_date' => $item['check_out_date'],
				);
				$parent_id = WP_Hotel_Booking::instance()->cart->generate_cart_id( $param );

				foreach ( $cart_items as $cart_package_id => $package ) {
					if ( !isset( $package['parent_id'] ) || !isset( $package['check_in_date'] ) || !isset( $package['check_out_date'] ) )
						continue;

					if ( $package['parent_id'] === $parent_id ) {
						$woo_cart_contents[$cart_package_id] = $package;
					}
				}
			}
		}

		$woocommerce->cart->cart_contents = $woo_cart_contents;
	}

}

add_action( 'plugins_loaded', array( 'WP_Hotel_Booking_Woocommerce', 'load' ) );
