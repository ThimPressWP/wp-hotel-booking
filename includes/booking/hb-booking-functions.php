<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 14:42:40
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 15:37:21
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
