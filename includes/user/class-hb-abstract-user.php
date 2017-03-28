<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 14:55:56
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-13 17:01:24
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

abstract class WPHB_User_Abstract {

    public $user = null;
    public $id = null;

    function __construct( $user = null ) {

        if ( is_numeric( $user ) && ( $user = get_user_by( 'ID', $user ) ) ) {
            $this->user = $user;
            $this->id = $this->user->ID;
        } else if ( $user instanceof WP_User ) {
            $this->user = $user;
            $this->id = $this->user->ID;
        }

        if ( !$user ) {
            $current_user = wp_get_current_user();
            $this->id = $current_user->ID;
        }

        if ( !$this->id ) {
            // throw new Exception( sprintf( __( 'User %s is not exists.', 'wp-hotel-booking' ), $user ) );
        }
    }

    function __get( $key ) {
        if ( !isset( $this->{$key} ) || !method_exists( $this, $key ) ) {
            return get_user_meta( $this->id, '_hb_' . $key, true );
        }
    }

    // get all booking of user
    function get_bookings() {
        if ( !$this->id ) {
            return null;
        }

        global $wpdb;

        $query = $wpdb->prepare( "
				SELECT booking.ID FROM $wpdb->posts AS booking
					INNER JOIN $wpdb->postmeta AS bookingmeta ON bookingmeta.post_ID = booking.ID AND bookingmeta.meta_key = %s
					INNER JOIN $wpdb->users AS users ON users.ID = bookingmeta.meta_value
				WHERE
					booking.post_type = %s
					AND bookingmeta.meta_value = %d
					ORDER BY booking.ID DESC
			", '_hb_user_id', 'hb_booking', $this->id );

        $results = $wpdb->get_col( $query );

        $bookings = array();

        if ( !empty( $results ) ) {
            foreach ( $results as $k => $booking_id ) {
                $bookings[] = hb_get_booking( $booking_id );
            }
        }

        return $bookings;
    }

}
