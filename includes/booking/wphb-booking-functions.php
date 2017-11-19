<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-03-31 14:42:40
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-25 16:27:58
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

// get booking
if ( !function_exists( 'hb_get_booking' ) ) {
	function hb_get_booking( $book = null ) {
		return WPHB_Booking::instance( $book );
	}
}

/**
 * Update booking status
 *
 * @param int
 * @param string
 */
if ( !function_exists( 'hb_update_booking_status' ) ) {
	function hb_update_booking_status( $booking_id, $status ) {
		$booking = WPHB_Booking::instance( $booking_id );
		return $booking->update_status( $status );
	}
}

/**
 * Creates new booking
 *
 * @param array $args
 *
 * @return mixed|WP_Error
 */
if ( !function_exists( 'hb_create_booking' ) ) {
	function hb_create_booking( $booking_info = array(), $order_items = array() ) {

		$booking_info = wp_parse_args( $booking_info, array(
			'_hb_tax'                     => '',
			'_hb_advance_payment'         => '',
			'_hb_advance_payment_setting' => '',
			'_hb_currency'                => '',
			'_hb_user_id'                 => get_current_blog_id(),
			'_hb_method'                  => '',
			'_hb_method_title'            => '',
			'_hb_method_id'               => '',
			// customer
			'_hb_customer_title'          => '',
			'_hb_customer_first_name'     => '',
			'_hb_customer_last_name'      => '',
			'_hb_customer_address'        => '',
			'_hb_customer_city'           => '',
			'_hb_customer_state'          => '',
			'_hb_customer_postal_code'    => '',
			'_hb_customer_country'        => '',
			'_hb_customer_phone'          => '',
			'_hb_customer_email'          => '',
			'_hb_customer_fax'            => ''
		) );
		// return WP_Error if cart is empty
		if ( WP_Hotel_Booking::instance()->cart->cart_items_count === 0 ) {
			return new WP_Error( 'hotel_booking_cart_empty', __( 'Your cart is empty.', 'wp-hotel-booking' ) );
		}

		$args = array(
			'status'        => '',
			'user_id'       => get_current_user_id(),
			'customer_note' => null,
			'booking_id'    => 0,
			'parent'        => 0
		);

		WP_Hotel_Booking::instance()->_include( 'includes/class-wphb-room.php' );

		$booking                     = WPHB_Booking::instance( $args['booking_id'] );
		$booking->post->post_title   = sprintf( __( 'Booking ', 'wp-hotel-booking' ) );
		$booking->post->post_content = hb_get_request( 'addition_information' ) ? hb_get_request( 'addition_information' ) : __( 'Empty Booking Notes', 'wp-hotel-booking' );
		$booking->post->post_status  = 'hb-' . apply_filters( 'hb_default_order_status', 'pending' );

		if ( $args['status'] ) {
			if ( !in_array( 'hb-' . $args['status'], array_keys( hb_get_booking_statuses() ) ) ) {
				return new WP_Error( 'hb_invalid_booking_status', __( 'Invalid booking status', 'wp-hotel-booking' ) );
			}
			$booking->post->post_status = 'hb-' . $args['status'];
		}

		$booking_info['_hb_booking_key'] = apply_filters( 'hb_generate_booking_key', uniqid() );

		if ( WP_Hotel_Booking::instance()->cart->coupon ) {
			$booking_info['_hb_coupon_id']    = WP_Hotel_Booking::instance()->cart->coupon;
			$coupon                           = HB_Coupon::instance( $booking_info['_hb_coupon_id'] );
			$booking_info['_hb_coupon_code']  = $coupon->coupon_code;
			$booking_info['_hb_coupon_value'] = $coupon->discount_value;
		}

		$booking->set_booking_info(
			$booking_info
		);

		$booking_id = $booking->update( $order_items );

		// set session booking id
		WP_Hotel_Booking::instance()->cart->set_booking( 'booking_id', $booking_id );

		// do action
		do_action( 'hotel_booking_create_booking', $booking_id, $booking_info, $order_items );
		return $booking_id;
	}
}

/**
 * Gets all statuses that room supported
 *
 * @return array
 */
if ( !function_exists( 'hb_get_booking_statuses' ) ) {

	function hb_get_booking_statuses() {
		$booking_statuses = array(
			'hb-cancelled'  => _x( 'Cancelled', 'Booking status', 'wp-hotel-booking' ),
			'hb-pending'    => _x( 'Pending', 'Booking status', 'wp-hotel-booking' ),
			'hb-processing' => _x( 'Processing', 'Booking status', 'wp-hotel-booking' ),
			'hb-completed'  => _x( 'Completed', 'Booking status', 'wp-hotel-booking' ),
		);
		return apply_filters( 'hb_booking_statuses', $booking_statuses );
	}
}

if ( !function_exists( 'hb_get_booking_meta' ) ) {
	function hb_get_booking_meta( $booking_id = null, $meta_key = null, $uniqid = false ) {
		return get_post_meta( $booking_id, $meta_key, $uniqid );
	}
}

if ( !function_exists( 'hb_get_order_items' ) ) {
	function hb_get_order_items( $order_id = null, $item_type = 'line_item', $parent = null ) {
		global $wpdb;

		if ( !$parent ) {
			$query = $wpdb->prepare( "
                    SELECT booking.* FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                    WHERE post.ID = %d
                        AND booking.order_item_type = %s
                ", $order_id, $item_type );
		} else {
			$query = $wpdb->prepare( "
                    SELECT booking.* FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                    WHERE post.ID = %d
                        AND booking.order_item_type = %s
                        AND booking.order_item_parent = %d
                ", $order_id, $item_type, $parent );
		}

		return $wpdb->get_results( $query );
	}
}

// insert order item
if ( !function_exists( 'hb_add_order_item' ) ) {
	function hb_add_order_item( $booking_id = null, $param = array() ) {
		global $wpdb;

		$booking_id = absint( $booking_id );

		if ( !$booking_id )
			return false;

		$defaults = array(
			'order_item_name' => '',
			'order_item_type' => 'line_item',
		);

		$param = wp_parse_args( $param, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'hotel_booking_order_items',
			array(
				'order_item_name'   => $param['order_item_name'],
				'order_item_type'   => $param['order_item_type'],
				'order_item_parent' => isset( $param['order_item_parent'] ) ? $param['order_item_parent'] : null,
				'order_id'          => $booking_id
			),
			array(
				'%s', '%s', '%d', '%d'
			)
		);

		$item_id = absint( $wpdb->insert_id );

		do_action( 'hotel_booking_new_order_item', $item_id, $param, $booking_id );

		return $item_id;
	}
}

// update order item
if ( !function_exists( 'hb_update_order_item' ) ) {
	function hb_update_order_item( $item_id = null, $param = array() ) {
		global $wpdb;

		$update = $wpdb->update( $wpdb->prefix . 'hotel_booking_order_items', $param, array( 'order_item_id' => $item_id ) );

		if ( false === $update ) {
			return false;
		}

		do_action( 'hotel_booking_update_order_item', $item_id, $param );

		return true;
	}
}

if ( !function_exists( 'hb_remove_order_item' ) ) {
	function hb_remove_order_item( $order_item_id = null ) {
		global $wpdb;

		$wpdb->delete( $wpdb->hotel_booking_order_items, array(
			'order_item_id' => $order_item_id
		), array( '%d' ) );


		$wpdb->delete( $wpdb->hotel_booking_order_itemmeta, array(
			'hotel_booking_order_item_id' => $order_item_id
		), array( '%d' ) );

		do_action( 'hotel_booking_remove_order_item', $order_item_id );
	}
}

if ( !function_exists( 'hb_get_parent_order_item' ) ) {
	function hb_get_parent_order_item( $order_item_id = null ) {
		global $wpdb;
		$query = $wpdb->prepare( "
                SELECT order_item.order_item_parent FROM $wpdb->hotel_booking_order_items AS order_item
                WHERE
                    order_item.order_item_id = %d
                    LIMIT 1
            ", $order_item_id );

		return $wpdb->get_var( $query );
	}
}

if ( !function_exists( 'hb_get_sub_item_order_item_id' ) ) {
	function hb_get_sub_item_order_item_id( $order_item_id = null ) {
		global $wpdb;
		$query = $wpdb->prepare( "
                SELECT order_item.order_item_id FROM $wpdb->hotel_booking_order_items AS order_item
                WHERE
                    order_item.order_item_parent = %d
            ", $order_item_id );

		return $wpdb->get_col( $query );
	}
}

if ( !function_exists( 'hb_empty_booking_order_items' ) ) {
	function hb_empty_booking_order_items( $booking_id = null ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
                DELETE hb_order_item, hb_order_itemmeta
                    FROM $wpdb->hotel_booking_order_items as hb_order_item
                    LEFT JOIN $wpdb->hotel_booking_order_itemmeta as hb_order_itemmeta ON hb_order_item.order_item_id = hb_order_itemmeta.hotel_booking_order_item_id
                WHERE
                    hb_order_item.order_id = %d
            ", $booking_id );

		return $wpdb->query( $sql );
	}
}

// add order item meta
if ( !function_exists( 'hb_add_order_item_meta' ) ) {
	function hb_add_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $unique = false ) {
		return add_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $unique );
	}
}

// update order item meta
if ( !function_exists( 'hb_update_order_item_meta' ) ) {
	function hb_update_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $prev_value = false ) {
		return update_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $prev_value );
	}
}

// get order item meta
if ( !function_exists( 'hb_get_order_item_meta' ) ) {

	function hb_get_order_item_meta( $item_id = null, $key = nul, $single = true ) {
		return get_metadata( 'hotel_booking_order_item', $item_id, $key, $single );
	}
}

// delete order item meta
if ( !function_exists( 'hb_delete_order_item_meta' ) ) {

	function hb_delete_order_item_meta( $item_id = null, $meta_key = null, $meta_value = '', $delete_all = false ) {
		return delete_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $delete_all );
	}
}

// get sub total booking
if ( !function_exists( 'hb_booking_subtotal' ) ) {

	function hb_booking_subtotal( $booking_id = null ) {
		if ( !$booking_id ) {
			throw new Exception( __( 'Booking is not found.', 'wp-hotel-booking' ) );
		}
		$booking = WPHB_Booking::instance( $booking_id );

		return $booking->sub_total();
	}
}

// get total booking
if ( !function_exists( 'hb_booking_total' ) ) {

	function hb_booking_total( $booking_id = null ) {
		if ( !$booking_id ) {
			throw new Exception( __( 'Booking is not found.', 'wp-hotel-booking' ) );
		}
		$booking = WPHB_Booking::instance( $booking_id );

		return $booking->total();
	}
}
// get total booking
if ( !function_exists( 'hb_booking_tax_total' ) ) {

	function hb_booking_tax_total( $booking_id = null ) {
		if ( !$booking_id ) {
			throw new Exception( __( 'Booking is not found.', 'wp-hotel-booking' ) );
		}
		$booking = WPHB_Booking::instance( $booking_id );

		return $booking->tax_total();
	}
}

/**
 * Checks to see if a user is booked room
 *
 * @param string $customer_email
 * @param int    $room_id
 *
 * @return bool
 */
if ( !function_exists( 'hb_customer_booked_room' ) ) {

	function hb_customer_booked_room( $room_id ) {
		return apply_filters( 'hb_customer_booked_room', true, $room_id );
	}
}

if ( !function_exists( 'hb_get_booking_id_by_key' ) ) {

	function hb_get_booking_id_by_key( $booking_key ) {
		global $wpdb;

		$booking_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_hb_booking_key' AND meta_value = %s", $booking_key ) );

		return $booking_id;
	}
}

if ( !function_exists( 'hb_get_booking_status_label' ) ) {

	function hb_get_booking_status_label( $booking_id ) {
		$statuses = hb_get_booking_statuses();
		if ( is_numeric( $booking_id ) ) {
			$status = get_post_status( $booking_id );
		} else {
			$status = $booking_id;
		}
		return !empty( $statuses[$status] ) ? $statuses[$status] : __( 'Cancelled', 'wp-hotel-booking' );
	}
}

if ( !function_exists( 'hb_booking_get_check_in_date' ) ) {
	// get min check in date of booking order
	function hb_booking_get_check_in_date( $booking_id = null ) {
		if ( !$booking_id ) {
			return;
		}

		$order_items = hb_get_order_items( $booking_id );
		$data        = array();
		foreach ( $order_items as $item ) {
			$data[] = hb_get_order_item_meta( $item->order_item_id, 'check_in_date', true );
		}
		sort( $data );
		return array_shift( $data );

	}
}

if ( !function_exists( 'hb_booking_get_check_out_date' ) ) {
	// get min check in date of booking order
	function hb_booking_get_check_out_date( $booking_id = null ) {
		if ( !$booking_id ) {
			return;
		}

		$order_items = hb_get_order_items( $booking_id );
		$data        = array();
		foreach ( $order_items as $item ) {
			$data[] = hb_get_order_item_meta( $item->order_item_id, 'check_out_date', true );
		}
		sort( $data );
		return array_pop( $data );

	}
}
