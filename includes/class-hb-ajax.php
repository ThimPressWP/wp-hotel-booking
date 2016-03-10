<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class HB_Ajax
 */
class HB_Ajax {

	/**
	 * @var bool
	 */
	protected static $_loaded = false;

	/**
	 * Constructor
	 */
	function __construct() {
		if ( self::$_loaded ) return;

		$ajax_actions = array(
			'fetch_customer_info'   => true,
			'place_order'           => true,
			'load_room_type_galley' => false,
			'parse_search_params'   => true,
			'parse_booking_params'  => true,
			'apply_coupon'          => true,
			'remove_coupon'         => true,
			'ajax_add_to_cart'      => true,
			'ajax_remove_item_cart' => true
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
	 * Fetch customer information with user email
	 */
	static function fetch_customer_info() {
		$email      = hb_get_request( 'email' );
		$query_args = array(
			'post_type'  => 'hb_customer',
			'meta_query' => array(
				array(
					'key'     => '_hb_email',
					'value'   => $email,
					'compare' => 'EQUALS'
				),
			)
		);
		// set_transient( 'hotel_booking_customer_email_' . HB_BLOG_ID, $email, DAY_IN_SECONDS );
		TP_Hotel_Booking::instance()->cart->set_customer( 'customer_email', $email );
		if ( $posts = get_posts( $query_args ) ) {
			$customer       = $posts[0];
			$customer->data = array();
			$data           = get_post_meta( $customer->ID );
			foreach ( $data as $k => $v ) {
				$customer->data[$k] = $v[0];
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
		if ( $attachment_ids ) foreach ( $attachment_ids as $id ) {
			$attachment    = wp_get_attachment_image_src( $id, 'thumbnail' );
			$attachments[] = array(
				'id'  => $id,
				'src' => $attachment[0]
			);
		}
		hb_send_json( $attachments );
	}

	/**
	 * Catch variables via post method and build a request param
	 */
	static function parse_search_params() {
		check_ajax_referer( 'hb_search_nonce_action', 'nonce' );
		$params = array(
			'hotel-booking'  	=> hb_get_request( 'hotel-booking' ),
			'check_in_date'  	=> hb_get_request( 'check_in_date' ),
			'check_out_date' 	=> hb_get_request( 'check_out_date' ),
			'hb_check_in_date'	=> hb_get_request( 'hb_check_in_date' ),
			'hb_check_out_date'	=> hb_get_request( 'hb_check_out_date' ),
			'adults'         	=> hb_get_request( 'adults_capacity' ),
			'max_child'      	=> hb_get_request( 'max_child' )
		);

		$return = apply_filters( 'hotel_booking_parse_search_param', array(
				'success' 	=> 1,
				'sig'     	=> base64_encode( serialize( $params ) ),
				'params'  	=> $params
			) );
		hb_send_json( $return );
	}

	static function apply_coupon() {
		! session_id() && session_start();
		$code = hb_get_request( 'code' );
		ob_start();
		$today    = strtotime( date( 'm/d/Y' ) );
		$coupon   = hb_get_coupons_active( $today, $code );

		$output   = ob_get_clean();
		$response = array();
		if ( $coupon ) {
			$coupon   = HB_Coupon::instance( $coupon );
			$response = $coupon->validate();
			if ( $response['is_valid'] ) {
				$response['result'] = 'success';
				$response['type']   = get_post_meta( $coupon->ID, '_hb_coupon_discount_type', true );
				$response['value']  = get_post_meta( $coupon->ID, '_hb_coupon_discount_value', true );
				if ( !session_id() ) {
					session_start();
				}
				// set_transient( 'hb_user_coupon_' . session_id(), $coupon, HOUR_IN_SECONDS );
				TP_Hotel_Booking::instance()->cart->set_customer( 'coupon', $coupon->post->ID );
				hb_add_message( __( 'Coupon code applied', 'tp-hotel-booking' ) );
			}
		} else {
			$response['message'] = __( 'Coupon does not exist!', 'tp-hotel-booking' );
		}
		hb_send_json(
			$response
		);
	}

	static function remove_coupon() {
		! session_id() && session_start();
		// delete_transient( 'hb_user_coupon_' . session_id() );
		TP_Hotel_Booking::instance()->cart->set_customer( 'coupon', null );
		hb_add_message( __( 'Coupon code removed', 'tp-hotel-booking' ) );
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
		if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) )
			return;

		if ( ! isset( $_POST['room-id'] ) || ! isset( $_POST['hb-num-of-rooms'] ) )
			hb_send_json( array( 'status' => 'warning', 'message' => __( 'Room ID is not exists.', 'tp-hotel-booking' ) ) );

		if ( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_out_date'] ) )
			return;

		$product_id = absint( $_POST['room-id'] );
		$param = array();
		$param[ 'product_id' ] = sanitize_text_field( $product_id );
		if( ! isset( $_POST['hb-num-of-rooms'] ) || ! absint( sanitize_text_field( $_POST['hb-num-of-rooms'] ) ) )
		{
			hb_send_json( array( 'status' => 'warning', 'message' => __( 'Can not select zero room.', 'tp-hotel-booking' ) ) );
		}
		else
		{
			$qty = absint( sanitize_text_field( sanitize_text_field( $_POST['hb-num-of-rooms'] ) ) );
		}

		// validate checkin, checkout date
		if( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_in_date'] ) )
		{
			hb_send_json( array( 'status' => 'warning', 'message' => __( 'Checkin date, checkout date is invalid.', 'tp-hotel-booking' ) ) );
		}
		else
		{
			$param[ 'check_in_date' ] = sanitize_text_field( $_POST['check_in_date'] );
			$param[ 'check_out_date' ] = sanitize_text_field( $_POST['check_out_date'] );
		}

		$param = apply_filters( 'tp_hotel_booking_add_cart_params', $param );
		do_action( 'tp_hotel_booking_before_add_to_cart', $_POST );
		// add to cart
		$cart_item_id = TP_Hotel_Booking::instance()->cart->add_to_cart( $product_id, $param, $qty );

		if ( ! is_wp_error( $cart_item_id ) )
		{
			$cart_item = TP_Hotel_Booking::instance()->cart->get_cart_item( $cart_item_id );
			$room = $cart_item->product_data;

			do_action( 'hotel_booking_added_cart_completed', $cart_item_id, $cart_item, $_POST );

			$results = array(
				'status'     => 'success',
				'message'    => sprintf( '<label class="hb_success_message">%1$s</label>', __( 'Added successfully.', 'tp-hotel-booking' ) ),
				'id'         => $product_id,
				'permalink'  => get_permalink( $product_id ),
				'name'       => sprintf( '%s', $room->name ) . ( $room->capacity_title ? sprintf( '(%s)', $room->capacity_title ) : '' ),
				'quantity'   => $qty,
				'cart_id'	 => $cart_item_id,
				'total'      => hb_format_price( TP_Hotel_Booking::instance()->cart->get_cart_item( $cart_item_id )->amount )
			);

			$results = apply_filters( 'hotel_booking_add_to_cart_results', $results, $room );

			hb_send_json( $results );
		}
		else
		{
			hb_send_json( array( 'status' => 'warning', 'message' => __( 'Room selected. Please View Cart to change order', 'tp-hotel-booking' ) ) );
		}

	}

	// remove cart item
	static function ajax_remove_item_cart() {
		if ( ! check_ajax_referer( 'hb_booking_nonce_action', 'nonce' ) )
			return;

		$cart = TP_Hotel_Booking::instance()->cart;
		if( $cart->cart_contents && ! isset( $_POST['cart_id'] ) || ! array_key_exists( sanitize_text_field( $_POST['cart_id'] ), $cart->cart_contents ) )
		{
			hb_send_json( array( 'status' => 'warning', 'message' => __( 'Cart item is not exists.', 'tp-hotel-booking' ) ) );
		}

		if( $cart->remove_cart_item( sanitize_text_field( $_POST['cart_id'] ) ) )
		{
			$return = apply_filters( 'hotel_booking_ajax_remove_cart_item', array(
				'status'          => 'success',
				'sub_total'       => hb_format_price( $cart->sub_total ),
				'grand_total'     => hb_format_price( $cart->total ),
				'advance_payment' => hb_format_price( $cart->advance_payment )
			) );

			hb_send_json( $return );
		}
	}

}

new HB_Ajax();