<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WPHB_Ajax
 */
class WPHB_Ajax {

	/**
	 * @var bool
	 */
	protected static $_loaded = false;

	/**
	 * Constructor
	 */
	function __construct() {
		if ( self::$_loaded ) {
			return;
		}

		$ajax_actions = array(
			'fetch_customer_info'      => true,
			'place_order'              => true,
			'load_room_type_galley'    => false,
			'parse_search_params'      => true,
			'parse_booking_params'     => true,
			'apply_coupon'             => true,
			'remove_coupon'            => true,
			'ajax_add_to_cart'         => true,
			'ajax_remove_item_cart'    => true,
			'load_order_user'          => false,
			'load_room_ajax'           => false,
			'check_room_available'     => false,
			'load_order_item'          => false,
			'load_coupon_ajax'         => false,
			'admin_add_order_item'     => false,
			'admin_remove_order_item'  => false,
			'admin_remove_order_items' => false,
			'add_coupon_to_order'      => false,
			'remove_coupon_on_order'   => false,
			'load_other_full_calendar' => false,
			'dismiss_notice'           => true
		);

		foreach ( $ajax_actions as $action => $priv ) {
			add_action( "wp_ajax_hotel_booking_{$action}", array( __CLASS__, $action ) );
			if ( $priv ) {
				add_action( "wp_ajax_nopriv_hotel_booking_{$action}", array( __CLASS__, $action ) );
			}
		}

		self::$_loaded = true;
	}

	/**
	 * Dismiss remove TP Hotel Booking plugin notice
	 */
	static function dismiss_notice() {
		if ( is_multisite() ) {
			update_site_option( 'wphb_notice_remove_hotel_booking', 1 );
		} else {
			update_option( 'wphb_notice_remove_hotel_booking', 1 );
		}
		wp_send_json( array(
			'status' => 'done'
		) );
	}

	/**
	 * Fetch customer information with user email
	 */
	static function fetch_customer_info() {
		$email = hb_get_request( 'email' );
		$args  = array(
			'post_type'   => 'hb_booking',
			'meta_key'    => '_hb_customer_email',
			'meta_value'  => $email,
			'post_status' => 'any'
		);
		// set_transient( 'hotel_booking_customer_email_' . WPHB_BLOG_ID, $email, DAY_IN_SECONDS );
		WP_Hotel_Booking::instance()->cart->set_customer( 'customer_email', $email );
		if ( $posts = get_posts( $args ) ) {
			$customer       = $posts[0];
			$customer->data = array();
			$data           = get_post_meta( $customer->ID );
			foreach ( $data as $k => $v ) {
				$customer->data[ $k ] = $v[0];
			}
		} else {
			$customer = null;
		}
		hb_send_json( $customer );
		die();
	}

	/**
	 * Process the order with customer information posted via form
	 *
	 * @throws Exception
	 */
	static function place_order() {
		hb_customer_place_order();
	}

	/**
	 * Get all images for a room type
	 */
	static function load_room_type_galley() {
		$term_id        = hb_get_request( 'term_id' );
		$attachment_ids = get_option( 'hb_taxonomy_thumbnail_' . $term_id );
		$attachments    = array();
		if ( $attachment_ids ) {
			foreach ( $attachment_ids as $id ) {
				$attachment    = wp_get_attachment_image_src( $id, 'thumbnail' );
				$attachments[] = array(
					'id'  => $id,
					'src' => $attachment[0]
				);
			}
		}
		hb_send_json( $attachments );
	}

	/**
	 * Catch variables via post method and build a request param
	 */
	static function parse_search_params() {
		check_ajax_referer( 'hb_search_nonce_action', 'nonce' );
		$params = array(
			'hotel-booking'     => hb_get_request( 'hotel-booking' ),
			'check_in_date'     => hb_get_request( 'check_in_date' ),
			'check_out_date'    => hb_get_request( 'check_out_date' ),
			'hb_check_in_date'  => hb_get_request( 'hb_check_in_date' ),
			'hb_check_out_date' => hb_get_request( 'hb_check_out_date' ),
			'adults'            => hb_get_request( 'adults_capacity' ),
			'max_child'         => hb_get_request( 'max_child' )
		);

		$return = apply_filters( 'hotel_booking_parse_search_param', array(
			'success' => 1,
			'sig'     => base64_encode( serialize( $params ) ),
			'params'  => $params
		) );
		hb_send_json( $return );
	}

	static function apply_coupon() {
		! session_id() && session_start();
		$code = hb_get_request( 'code' );
		ob_start();
		$today  = strtotime( date( 'm/d/Y' ) );
		$coupon = hb_get_coupons_active( $today, $code );

		$output   = ob_get_clean();
		$response = array();
		if ( $coupon ) {
			$coupon   = HB_Coupon::instance( $coupon );
			$response = $coupon->validate();
			if ( $response['is_valid'] ) {
				$response['result'] = 'success';
				$response['type']   = get_post_meta( $coupon->ID, '_hb_coupon_discount_type', true );
				$response['value']  = get_post_meta( $coupon->ID, '_hb_coupon_discount_value', true );
				if ( ! session_id() ) {
					session_start();
				}
				// set session
				WP_Hotel_Booking::instance()->cart->set_customer( 'coupon', $coupon->post->ID );
				hb_add_message( __( 'Coupon code applied', 'wp-hotel-booking' ) );
			}
		} else {
			$response['message'] = __( 'Coupon does not exist!', 'wp-hotel-booking' );
		}
		hb_send_json(
			$response
		);
	}

	static function remove_coupon() {
		! session_id() && session_start();
		// delete_transient( 'hb_user_coupon_' . session_id() );
		WP_Hotel_Booking::instance()->cart->set_customer( 'coupon', null );
		hb_add_message( __( 'Coupon code removed', 'wp-hotel-booking' ) );
		hb_send_json(
			array(
				'result' => 'success'
			)
		);
	}

	static function parse_booking_params() {

		check_ajax_referer( 'hb_booking_nonce_action', 'nonce' );

		$check_in     = hb_get_request( 'check_in_date' );
		$check_out    = hb_get_request( 'check_out_date' );
		$num_of_rooms = hb_get_request( 'hb-num-of-rooms' );

		$params = array(
			'hotel-booking'   => hb_get_request( 'hotel-booking' ),
			'check_in_date'   => $check_in,
			'check_out_date'  => $check_out,
			'hb-num-of-rooms' => $num_of_rooms
		);

		//print_r($params);
		hb_send_json(
			array(
				'success' => 1,
				'sig'     => base64_encode( serialize( $params ) )
			)
		);
	}

	static function ajax_add_to_cart() {
		if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) ) {
			return;
		}

		if ( ! isset( $_POST['room-id'] ) || ! isset( $_POST['hb-num-of-rooms'] ) ) {
			hb_send_json( array(
				'status'  => 'warning',
				'message' => __( 'Room ID is not exists.', 'wp-hotel-booking' )
			) );
		}

		if ( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_out_date'] ) ) {
			return;
		}

		$product_id          = absint( $_POST['room-id'] );
		$param               = array();
		$param['product_id'] = sanitize_text_field( $product_id );
		if ( ! isset( $_POST['hb-num-of-rooms'] ) || ! absint( sanitize_text_field( $_POST['hb-num-of-rooms'] ) ) ) {
			hb_send_json( array(
				'status'  => 'warning',
				'message' => __( 'Can not select zero room.', 'wp-hotel-booking' )
			) );
		} else {
			$qty = absint( sanitize_text_field( sanitize_text_field( $_POST['hb-num-of-rooms'] ) ) );
		}

		// validate checkin, checkout date
		if ( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_in_date'] ) ) {
			hb_send_json( array(
				'status'  => 'warning',
				'message' => __( 'Checkin date, checkout date is invalid.', 'wp-hotel-booking' )
			) );
		} else {
			$param['check_in_date']  = sanitize_text_field( $_POST['check_in_date'] );
			$param['check_out_date'] = sanitize_text_field( $_POST['check_out_date'] );
		}

		$param = apply_filters( 'hotel_booking_add_cart_params', $param );
		do_action( 'hotel_booking_before_add_to_cart', $_POST );
		// add to cart
		$cart_item_id = WP_Hotel_Booking::instance()->cart->add_to_cart( $product_id, $param, $qty );

		if ( ! is_wp_error( $cart_item_id ) ) {
			$cart_item = WP_Hotel_Booking::instance()->cart->get_cart_item( $cart_item_id );
			$room      = $cart_item->product_data;

			do_action( 'hotel_booking_added_cart_completed', $cart_item_id, $cart_item, $_POST );

			$results = array(
				'status'    => 'success',
				'message'   => sprintf( '<label class="hb_success_message">%1$s</label>', __( 'Added successfully.', 'wp-hotel-booking' ) ),
				'id'        => $product_id,
				'permalink' => get_permalink( $product_id ),
				'name'      => sprintf( '%s', $room->name ) . ( $room->capacity_title ? sprintf( '(%s)', $room->capacity_title ) : '' ),
				'quantity'  => $qty,
				'cart_id'   => $cart_item_id,
				'total'     => hb_format_price( WP_Hotel_Booking::instance()->cart->get_cart_item( $cart_item_id )->amount )
			);

			$results = apply_filters( 'hotel_booking_add_to_cart_results', $results, $room );

			hb_send_json( $results );
		} else {
			hb_send_json( array(
				'status'  => 'warning',
				'message' => __( 'Room selected. Please View Cart to change order', 'wp-hotel-booking' )
			) );
		}
	}

	// remove cart item
	static function ajax_remove_item_cart() {
		if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) ) {
			return;
		}

		$cart = WP_Hotel_Booking::instance()->cart;

		if ( empty( $cart->cart_contents ) || ! isset( $_POST['cart_id'] ) || ! array_key_exists( sanitize_text_field( $_POST['cart_id'] ), $cart->cart_contents ) ) {
			hb_send_json( array(
				'status'  => 'warning',
				'message' => __( 'Cart item is not exists.', 'wp-hotel-booking' )
			) );
		}

		if ( $cart->remove_cart_item( sanitize_text_field( $_POST['cart_id'] ) ) ) {
			$return = apply_filters( 'hotel_booking_ajax_remove_cart_item', array(
				'status'          => 'success',
				'sub_total'       => hb_format_price( $cart->sub_total ),
				'grand_total'     => hb_format_price( $cart->total ),
				'advance_payment' => hb_format_price( $cart->advance_payment )
			) );

			hb_send_json( $return );
		}
	}

	// ajax load user in booking details
	static function load_order_user() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hb_booking_nonce_action' ) || ! isset( $_POST['user_name'] ) ) {
			return;
		}

		$user_name = sanitize_text_field( $_POST['user_name'] );
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT user.ID, user.user_email, user.user_login FROM $wpdb->users AS user
				WHERE
					user.user_login LIKE %s
			", '%' . $wpdb->esc_like( $user_name ) . '%' );

		$users = $wpdb->get_results( $sql );
		wp_send_json( $users );
		die();
	}

	// ajax load room in booking details
	static function load_room_ajax() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hb_booking_nonce_action' ) || ! isset( $_POST['room'] ) ) {
			return;
		}

		$title = sanitize_text_field( $_POST['room'] );
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT room.ID AS ID, room.post_title AS post_title FROM $wpdb->posts AS room
				WHERE
					room.post_title LIKE %s
					AND room.post_type = %s
					AND room.post_status = %s
					GROUP BY room.post_name
			", '%' . $wpdb->esc_like( $title ) . '%', 'hb_room', 'publish' );

		$rooms = $wpdb->get_results( $sql );
		wp_send_json( $rooms );
		die();
	}

	// ajax check available room in booking details
	static function check_room_available() {

		if ( ! isset( $_POST['hotel-admin-check-room-available'] ) || ! wp_verify_nonce( $_POST['hotel-admin-check-room-available'], 'hotel_admin_check_room_available' ) ) {
			return;
		}

		//hotel_booking_get_room_available
		if ( ! isset( $_POST['product_id'] ) || ! $_POST['product_id'] ) {
			wp_send_json( array(
				'status'  => false,
				'message' => __( 'Room not found.', 'wp-hotel-booking' )
			) );
		}

		if ( ! isset( $_POST['check_in_date_timestamp'] ) || ! isset( $_POST['check_out_date_timestamp'] ) ) {
			wp_send_json( array(
				'status'  => false,
				'message' => __( 'Please select check in date and checkout date.', 'wp-hotel-booking' )
			) );
		}

		$product_id = absint( $_POST['product_id'] );
		$qty        = hotel_booking_get_room_available( $product_id, array(
			'check_in_date'  => sanitize_text_field( $_POST['check_in_date_timestamp'] ),
			'check_out_date' => sanitize_text_field( $_POST['check_out_date_timestamp'] ),
			'excerpt'        => array(
				isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0
			)
		) );

		if ( $qty && ! is_wp_error( $qty ) ) {

			// HB_Room_Extra instead of HB_Room
			$room_extra = HB_Room_Extra::instance( $product_id );

			$room_extra = $room_extra->get_extra();

			$args = apply_filters( 'hotel_booking_check_room_available', array(
				'status'       => true,
				'qty'          => $qty,
				'qty_selected' => isset( $_POST['order_item_id'] ) ? hb_get_order_item_meta( $_POST['order_item_id'], 'qty', true ) : 0,
				'product_id'   => $product_id,
				'extra'        => $room_extra
			) );
			wp_send_json( $args );
		} else {
			wp_send_json( array(
				'status'  => false,
				'message' => $qty->get_error_message()
			) );
		}
	}

	// ajax load oder item to edit
	static function load_order_item() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hb_booking_nonce_action' ) ) {
			return;
		}

		if ( ! isset( $_POST['order_item_id'] ) ) {
			wp_send_json( array() );
		}

		$order_id      = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$order_item_id = absint( $_POST['order_item_id'] );
		$product_id    = hb_get_order_item_meta( $order_item_id, 'product_id', true );
		$checkin       = hb_get_order_item_meta( $order_item_id, 'check_in_date', true );
		$checkout      = hb_get_order_item_meta( $order_item_id, 'check_out_date', true );

		// extra hook
		$args = apply_filters( 'hotel_booking_admin_load_order_item', array(
			'status'                   => true,
			'modal_title'              => __( 'Edit order item', 'wp-hotel-booking' ),
			'order_id'                 => $order_id,
			'order_item_id'            => $order_item_id,
			'product_id'               => $product_id,
			'room'                     => array(
				'ID'         => $product_id,
				'post_title' => get_the_title( hb_get_order_item_meta( $order_item_id, 'product_id', true ) )
			),
			'check_in_date'            => date_i18n( hb_get_date_format(), $checkin ),
			'check_out_date'           => date_i18n( hb_get_date_format(), $checkout ),
			'check_in_date_timestamp'  => $checkin,
			'check_out_date_timestamp' => $checkout,
			'qty'                      => hotel_booking_get_room_available( $product_id, array(
				'check_in_date'  => $checkin,
				'check_out_date' => $checkout,
				'excerpt'        => array( $order_id )
			) ),
			'qty_selected'             => hb_get_order_item_meta( $order_item_id, 'qty', true ),
			'post_type'                => get_post_type( $product_id )
		) );
		wp_send_json( $args );
	}

	// ajax load coupons code
	static function load_coupon_ajax() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hb_booking_nonce_action' ) ) {
			return;
		}

		$code = sanitize_text_field( $_POST['coupon'] );
		$time = time();

		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT coupon.ID, coupon.post_title FROM $wpdb->posts AS coupon
					INNER JOIN $wpdb->postmeta AS start ON start.post_id = coupon.ID
					INNER JOIN $wpdb->postmeta AS end ON end.post_id = coupon.ID
				WHERE
					coupon.post_type = %s
					AND coupon.post_title LIKE %s
					AND coupon.post_status = %s
					AND start.meta_key = %s
					AND end.meta_key = %s
					AND ( start.meta_value <= %d AND end.meta_value >= %d )
			", 'hb_coupon', '%' . $wpdb->esc_like( $code ) . '%', 'publish', '_hb_coupon_date_from_timestamp', '_hb_coupon_date_to_timestamp', $time, $time
		);

		wp_send_json( apply_filters( 'hotel_admin_get_coupons', $wpdb->get_results( $sql ) ) );
	}

	// book mamunal add order item
	static function admin_add_order_item() {
		if ( ! isset( $_POST['hotel-admin-check-room-available'] ) && ! wp_verify_nonce( $_POST['hotel-admin-check-room-available'], 'hotel_admin_check_room_available' ) ) {
			return;
		}

		$errors     = new WP_Error();
		$order_id   = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$product_id = isset( $_POST['product_id'] ) ? $_POST['product_id'] : 0;
		$qty        = isset( $_POST['qty'] ) ? absint( $_POST['qty'] ) : 0;

		if ( ! $qty ) {
			wp_send_json( array(
				'status'  => false,
				'message' => __( 'Can not add item with zero quantity.', 'wp-hotel-booking' )
			) );
		}

		$order_item_id = 0;
		$return        = true;
		if ( isset( $_POST['order_item_id'] ) && $_POST['order_item_id'] ) {
			$order_item_id = absint( $_POST['order_item_id'] );
		}

		if ( isset( $_POST['check_in_date_timestamp'] ) ) {
			$check_in_date = absint( $_POST['check_in_date_timestamp'] );
		} else {
			$return = false;
			$errors->add( 'check_in_date_invalid', __( 'Check in date is invalid', 'wp-hotel-booking' ) );
		}

		if ( isset( $_POST['check_out_date_timestamp'] ) ) {
			$check_out_date = absint( $_POST['check_out_date_timestamp'] );
		} else {
			$return = false;
			$errors->add( 'check_out_date_invalid', __( 'Check in date is invalid', 'wp-hotel-booking' ) );
		}

		if ( $return === false ) {
			return $errors;
		}

		$args = array(
			'order_item_name'   => get_the_title( $product_id ),
			'order_item_type'   => isset( $_POST['order_item_type'] ) && $_POST['order_item_type'] ? sanitize_title( $_POST['order_item_type'] ) : 'line_item',
			'order_item_parent' => isset( $_POST['order_item_parent'] ) && $_POST['order_item_parent'] ? absint( $_POST['order_item_parent'] ) : null
		);
		if ( ! $order_item_id ) {
			// add new order item
			$order_item_id = hb_add_order_item( $order_id, $args );
		} else {
			// update order item
			hb_update_order_item( $order_item_id, $args );
		}

		// update order item meta
		hb_update_order_item_meta( $order_item_id, 'check_in_date', $check_in_date );
		hb_update_order_item_meta( $order_item_id, 'check_out_date', $check_out_date );
		// product_id
		hb_update_order_item_meta( $order_item_id, 'product_id', $product_id );
		hb_update_order_item_meta( $order_item_id, 'qty', $qty );

		$params        = array(
			'check_in_date'  => $check_in_date,
			'check_out_date' => $check_out_date,
			'quantity'       => $qty,
			'order_item_id'  => $order_item_id
		);
		$product_class = hotel_booking_get_product_class( $product_id, $params );

		// update subtotal, total
		$subtotal = $product_class->amount_exclude_tax();
		$total    = $product_class->amount_include_tax();
		hb_update_order_item_meta( $order_item_id, 'subtotal', $subtotal );
		hb_update_order_item_meta( $order_item_id, 'total', $total );
		hb_update_order_item_meta( $order_item_id, 'tax_total', $total - $subtotal );

		// allow hook
		do_action( 'hotel_booking_updated_order_item', $order_id, $order_item_id );

		$post = get_post( $order_id );
		ob_start();
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items.php';
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items-template-js.php';
		$html = ob_get_clean();
		wp_send_json( array(
			'status' => true,
			'html'   => $html
		) );
	}

	// remove order item
	static function admin_remove_order_item() {
		// verify nonce
		if ( ! check_ajax_referer( 'hotel-booking-confirm', 'hotel_booking_confirm' ) ) {
			return;
		}

		$order_item_id = isset( $_POST['order_item_id'] ) ? absint( $_POST['order_item_id'] ) : 0;
		$order_id      = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		if ( $order_item_id ) {
			hb_remove_order_item( $order_item_id );

			$post = get_post( $order_id );
			ob_start();
			require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items.php';
			require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items-template-js.php';
			$html = ob_get_clean();
			wp_send_json( array(
				'status' => true,
				'html'   => $html
			) );
		}
	}

	// remove list order items
	static function admin_remove_order_items() {
		// verify nonce
		if ( ! check_ajax_referer( 'hotel-booking-confirm', 'hotel_booking_confirm' ) ) {
			return;
		}

		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;

		if ( isset( $_POST['order_item_id'] ) && is_array( $_POST['order_item_id'] ) ) {
			foreach ( $_POST['order_item_id'] as $key => $o_i_d ) {
				hb_remove_order_item( $o_i_d );
			}
		}

		$post = get_post( $order_id );
		ob_start();
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items.php';
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items-template-js.php';
		$html = ob_get_clean();
		wp_send_json( array(
			'status' => true,
			'html'   => $html
		) );
	}

	// add new coupon
	static function add_coupon_to_order() {
		if ( ! check_ajax_referer( 'hotel_admin_get_coupon_available', 'hotel-admin-get-coupon-available' ) || ! class_exists( 'HB_Coupon' ) ) {
			return;
		}

		if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['coupon_id'] ) ) {
			return;
		}

		$order_id  = absint( $_POST['order_id'] );
		$coupon_id = absint( $_POST['coupon_id'] );

		$coupon   = HB_Coupon::instance( $coupon_id );
		$subtotal = hb_booking_subtotal( $order_id, false ); // subtotal without coupon

		add_post_meta( $order_id, '_hb_coupon_id', $coupon_id );
		add_post_meta( $order_id, '_hb_coupon_code', $coupon->coupon_code );
		add_post_meta( $order_id, '_hb_coupon_value', $coupon->get_discount_value( $subtotal ) );

		$post = get_post( $order_id );
		ob_start();
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items.php';
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items-template-js.php';
		$html = ob_get_clean();
		wp_send_json( array(
			'status' => true,
			'html'   => $html
		) );
	}

	// remove coupon order
	static function remove_coupon_on_order() {
		if ( ! check_ajax_referer( 'hotel-booking-confirm', 'hotel_booking_confirm' ) ) {
			return;
		}

		if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['coupon_id'] ) ) {
			return;
		}

		$order_id = absint( $_POST['order_id'] );

		delete_post_meta( $order_id, '_hb_coupon_id' );
		delete_post_meta( $order_id, '_hb_coupon_code' );
		delete_post_meta( $order_id, '_hb_coupon_value' );

		$post = get_post( $order_id );
		ob_start();
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items.php';
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items-template-js.php';
		$html = ob_get_clean();
		wp_send_json( array(
			'status' => true,
			'html'   => $html
		) );
	}

	static function load_other_full_calendar() {
		check_ajax_referer( 'hb_booking_nonce_action', 'nonce' );

		if ( ! isset( $_POST['room_id'] ) ) {
			wp_send_json( array(
				'status'  => fasle,
				'message' => __( 'Room is not exists.', 'wp-hotel-booking' )
			) );
		}

		$room_id = absint( $_POST['room_id'] );
		if ( ! isset( $_POST['date'] ) ) {
			wp_send_json( array(
				'status'  => fasle,
				'message' => __( 'Date is not exists.', 'wp-hotel-booking' )
			) );
		}
		$date = sanitize_text_field( $_POST['date'] );

		wp_send_json( array(
			'status'     => true,
			'events'     => hotel_booking_print_pricing_json( $room_id, date( 'm/d/Y', strtotime( $date ) ) ),
			'next'       => date( 'm/d/Y', strtotime( '+1 month', strtotime( $date ) ) ),
			'prev'       => date( 'm/d/Y', strtotime( '-1 month', strtotime( $date ) ) ),
			'month_name' => date_i18n( 'F, Y', strtotime( $date ) )
		) );
	}

}

new WPHB_Ajax();
