<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-01 09:45:55
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-07 10:20:39
 */

/**
 * set table name.
 */
if ( ! function_exists( 'hotel_booking_set_table_name' ) ) {
	function hotel_booking_set_table_name() {
		global $wpdb;
		$order_item = 'hotel_booking_order_items';
		$order_itemmeta = 'hotel_booking_order_itemmeta';

		$wpdb->hotel_booking_order_items = $wpdb->prefix . $order_item;
		$wpdb->hotel_booking_order_itemmeta = $wpdb->prefix . $order_itemmeta;

		$wpdb->tables[] = 'hotel_booking_order_items';
		$wpdb->tables[] = 'hotel_booking_order_itemmeta';
	}
	add_action( 'init', 'hotel_booking_set_table_name', 0 );
	add_action( 'switch_blog', 'hotel_booking_set_table_name', 0 );
}

if ( ! function_exists( 'hotel_booking_get_room_available' ) ) {
	function hotel_booking_get_room_available( $room_id = null, $args = array() ) {
		$valid = true;
		$errors = new WP_Error;
		if ( ! $room_id ) {
			$valid = false;
			$errors->add( 'room_id_invalid', __( 'Room not found.', 'tp-hotel-booking' ) );
		}

		$args = wp_parse_args( $args, array(
				'check_in_date'		=> '',
				'check_out_date'	=> ''
			));

		if ( ! $args[ 'check_in_date' ] ) {
			$valid = false;
			$errors->add( 'check_in_date_not_available', __( 'Check in date is not valid.', 'tp-hotel-booking' ) );
		} else {
			if ( ! is_numeric( $args[ 'check_in_date' ] ) ) {
				$args[ 'check_in_date' ] = strtotime( $args[ 'check_in_date' ] );
			}
		}

		if ( ! $args[ 'check_out_date' ] ) {
			$valid = false;
			$errors->add( 'check_out_date_not_available', __( 'Check out date is not valid.', 'tp-hotel-booking' ) );
		} else {
			if ( ! is_numeric( $args[ 'check_out_date' ] ) ) {
				$args[ 'check_out_date' ] = strtotime( $args[ 'check_out_date' ] );
			}
		}

		// $valid is false
		if ( $valid === false ) {
			return $errors;
		} else {
			global $wpdb;

			$sql = $wpdb->prepare("
					(
						SELECT SUM( meta.meta_value ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
							LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id
							LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS room ON order_item.order_item_id = room.hotel_booking_order_item_id
							LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id
							LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id
							LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
						WHERE
							meta.meta_key = %s
							AND room.meta_value = hb_room.ID
							AND room.meta_key = %s
							AND checkin.meta_key = %s
							AND checkout.meta_key = %s
							AND (
									( checkin.meta_value >= %d AND checkin.meta_value <= %d )
								OR 	( checkout.meta_value >= %d AND checkout.meta_value <= %d )
								OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
							)
							AND booking.post_type = %s
							AND booking.post_status IN ( %s, %s, %s )
					)
				", 'qty', 'product_id', 'check_in_date', 'check_out_date',
					$args['check_in_date'], $args['check_out_date'],
					$args['check_in_date'], $args['check_out_date'],
					$args['check_in_date'], $args['check_out_date'],
					'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
				);

			$sql = $wpdb->prepare("
					SELECT ( number.meta_value - {$sql} ) AS qty FROM $wpdb->postmeta AS number
						INNER JOIN $wpdb->posts AS hb_room ON hb_room.ID = number.post_id
					WHERE
						number.meta_key = %s
						AND hb_room.ID = %d
						AND hb_room.post_type = %s
				", '_hb_num_of_rooms', $room_id, 'hb_room' );

			$qty = absint( $wpdb->get_var( $sql ) );
			if ( $qty === 0 ) {
				$errors->add( 'check_out_date_not_available', __( 'Check out date is not valid.', 'tp-hotel-booking' ) );
				return $errors;
			}
			return $qty;
		}
	}
}

// product class process
if ( ! function_exists( 'hotel_booking_get_product_class' ) ) {

	function hotel_booking_get_product_class( $product_id = null, $params = array() ) {

        $post_type = get_post_type( $product_id );

        $product = 'HB_Product_' . implode( '_', array_map( 'ucfirst', explode( '_', $post_type ) ) );
        if( ! class_exists( $product ) ) {
            $product = 'HB_Room';
        }

        $product = apply_filters( 'hotel_booking_cart_product_class_name', $product, $product_id );
        $product = new $product( $product_id, $params );

        return apply_filters( 'hotel_booking_get_product_class', $product, $product_id, $params );

	}

}
