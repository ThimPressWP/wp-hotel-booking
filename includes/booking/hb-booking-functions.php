<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 14:42:40
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-04 10:06:55
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// get booking
if ( ! function_exists( 'hb_get_booking' ) ) {
	function hb_get_booking( $book = null ) {
		return HB_Booking::instance( $book );
	}
}

/**
 * Update booking status
 *
 * @param int
 * @param string
 */
function hb_update_booking_status( $booking_id, $status ){
    $booking = HB_Booking::instance( $booking_id );
    return $booking->update_status( $status );
}

/**
 * Creates new booking
 *
 * @param array $args
 * @return mixed|WP_Error
 */
function hb_create_booking() {

    // return WP_Error if cart is empty
    if( TP_Hotel_Booking::instance()->cart->cart_items_count === 0 ){
        return new WP_Error( 'hotel_booking_cart_empty', __( 'Your cart is empty.', 'tp-hotel-booking' ) );
    }

    $args = array(
        'status'        => '',
        'customer_id'   => null,
        'customer_note' => null,
        'booking_id'    => 0,
        'parent'        => 0
    );

    if( TP_Hotel_Booking::instance()->cart->customer_id ){
        $args['customer_id'] = absint( TP_Hotel_Booking::instance()->cart->customer_id );
    }

    TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );

    $booking = HB_Booking::instance( $args['booking_id'] );
    $booking->post->post_title      = sprintf( __( 'Booking ', 'tp-hotel-booking' ) );
    $booking->post->post_content    = hb_get_request( 'addition_information' ) ? hb_get_request( 'addition_information' ) : __( 'Empty Booking Notes', 'tp-hotel-booking' ) ;
    $booking->post->post_status     = 'hb-' . apply_filters( 'hb_default_order_status', 'pending' );

    if ( $args['status'] ) {
        if ( ! in_array( 'hb-' . $args['status'], array_keys( hb_get_booking_statuses() ) ) ) {
            return new WP_Error( 'hb_invalid_booking_status', __( 'Invalid booking status', 'tp-hotel-booking' ) );
        }
        $booking->post->post_status  = 'hb-' . $args['status'];
    }

    $booking_info = array(
        '_hb_booking_key'              => apply_filters( 'hb_generate_booking_key', uniqid( 'booking' ) )
    );

    if( TP_Hotel_Booking::instance()->cart->coupon ){
        $booking_info['_hb_coupon'] = TP_Hotel_Booking::instance()->cart->coupon;
    }

    $booking->set_booking_info(
        $booking_info
    );

    $booking_id = $booking->update();

    // set session booking id
    TP_Hotel_Booking::instance()->cart->set_booking( 'booking_id', $booking_id );

    // do action
    do_action( 'hotel_booking_create_booking', $booking_id );
    return $booking_id;
}

/**
 * Gets all statuses that room supported
 *
 * @return array
 */
function hb_get_booking_statuses() {
    $booking_statuses = array(
        'hb-cancelled'  => _x( 'Cancelled', 'Booking status', 'tp-hotel-booking' ),
        'hb-pending'    => _x( 'Pending', 'Booking status', 'tp-hotel-booking' ),
        'hb-processing' => _x( 'Processing', 'Booking status', 'tp-hotel-booking' ),
        'hb-completed'  => _x( 'Completed', 'Booking status', 'tp-hotel-booking' ),
    );
    return apply_filters( 'hb_booking_statuses', $booking_statuses );
}

if ( ! function_exists( 'hb_get_booking_meta' ) ) {
    function hb_get_booking_meta( $booking_id = null, $meta_key = null, $uniqid = false ) {
        return get_post_meta( $booking_id, $meta_key, $uniqid );
    }
}

if ( ! function_exists( 'hb_get_order_item' ) ) {
    function hb_get_order_items( $order_id = null, $item_type = 'line_item', $parent = null ) {
        global $wpdb;

        if ( ! $parent ) {
            $query = $wpdb->prepare("
                    SELECT booking.* FROM $wpdb->hotel_booking_order_items AS booking
                        RIGHT JOIN $wpdb->posts AS post ON booking.order_id = post.ID
                    WHERE post.ID = %d
                        AND booking.order_item_type = %s
                ", $order_id, $item_type );
        } else {
            $query = $wpdb->prepare("
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
if ( ! function_exists( 'hb_add_order_item' ) ) {
    function hb_add_order_item( $booking_id = null, $param = array() ) {
        global $wpdb;

        $booking_id = absint( $booking_id );

        if ( ! $booking_id )
            return false;

        $defaults = array(
            'order_item_name'       => '',
            'order_item_type'       => 'line_item',
        );

        $param = wp_parse_args( $param, $defaults );

        $wpdb->insert(
            $wpdb->prefix . 'hotel_booking_order_items',
            array(
                'order_item_name'       => $param['order_item_name'],
                'order_item_type'       => $param['order_item_type'],
                'order_item_parent'     => isset( $param['order_item_parent'] ) ? $param['order_item_parent'] : null,
                'order_id'              => $booking_id
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
if ( ! function_exists( 'hb_update_order_item' ) ) {
    function hb_update_order_item( $item_id = null, $param = array() ) {
        global $wpdb;

        $update = $wpdb->update( $wpdb->prefix . 'hotel_booking_order_items', $param, array( 'order_item_id' => $item_id ) );

        if ( false === $update ) {
            return false;
        }

        do_action( 'hotel_booking_update_order_item', $item_id, $args );

        return true;
    }
}

// add order item meta
if ( ! function_exists( 'hb_add_order_item_meta' ) ) {
    function hb_add_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $unique = false ) {
        return add_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $unique );
    }
}

// update order item meta
if ( ! function_exists( 'hb_update_order_item_meta' ) ) {
    function hb_update_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $prev_value = false ) {
        return update_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $prev_value );
    }
}

// get order item meta
function hb_get_order_item_meta( $item_id = null, $key = nul, $single = true ) {
    return get_metadata( 'hotel_booking_order_item', $item_id, $key, $single );
}

// delete order item meta
function hb_delete_order_item_meta( $item_id = null, $meta_key = null, $meta_value = '', $delete_all = false ) {
    return delete_metadata( 'hotel_booking_order_item', $item_id, $meta_key, $meta_value, $delete_all );
}

// get sub total booking
function hb_booking_subtotal( $booking_id = null ) {
    if ( ! $booking_id ) {
        throw new Exception( __( 'Booking is not found.', 'tp-hotel-booking' ) );
    }
    $booking = HB_Booking::instance( $booking_id );

    return $booking->sub_total();
}
// get total booking
function hb_booking_total( $booking_id = null ) {
    if ( ! $booking_id ) {
        throw new Exception( __( 'Booking is not found.', 'tp-hotel-booking' ) );
    }
    $booking = HB_Booking::instance( $booking_id );

    return $booking->total();
}
// get total booking
function hb_booking_tax_total( $booking_id = null ) {
    if ( ! $booking_id ) {
        throw new Exception( __( 'Booking is not found.', 'tp-hotel-booking' ) );
    }
    $booking = HB_Booking::instance( $booking_id );

    return $booking->tax_total();
}