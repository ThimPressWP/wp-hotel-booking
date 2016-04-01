<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 16:44:29
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-01 16:15:51
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'HB_INSTALLING' ) || HB_INSTALLING !== true ) {
	exit();
}

global $wpdb;

$sql = $wpdb->prepare("
        SELECT param.* FROM $wpdb->posts AS booking
            INNER JOIN $wpdb->postmeta AS param ON booking.ID = param.post_id
        WHERE
            booking.post_type = %s
            AND param.meta_key IN ( %s, %s )
            GROUP BY booking.ID
    ", 'hb_booking', '_hb_booking_params', '_hb_booking_cart_params' );

$params = $wpdb->get_results( $sql );

foreach ( $params as $param ) {
    $booking_id = $param->post_id;
    $params = maybe_unserialize( $param->meta_value );
    if ( $param->meta_key === '_hb_booking_params' ) {
        foreach ( $params as $param ) {
            foreach ( $param as $id => $meta ) {
                if ( is_numeric( $id ) ) {
                    $order_item_id = hb_add_order_item( $booking_id, array(
                            'order_item_name'       => get_the_title( $id ),
                            'order_item_type'       => 'line_item'
                        ));
                    hb_add_order_item_meta( $order_item_id, 'product_id', $id );
                    hb_add_order_item_meta( $order_item_id, 'qty', $meta['quantity'] );
                    hb_add_order_item_meta( $order_item_id, 'check_in_date', strtotime( $meta['check_in_date'] ) );
                    hb_add_order_item_meta( $order_item_id, 'check_out_date', strtotime( $meta['check_out_date'] ) );

                    $room = HB_Room::instance( $id, array(
                    		'check_in_date'		=> $meta['check_in_date'],
                    		'check_out_date'	=> $meta['check_out_date'],
                    		'quantity'			=> $meta['quantity']
                    	) );

                    $subtotal = $room->amount_exclude_tax();
                    $total = $room->amount_include_tax();
                    // new meta
                    hb_add_order_item_meta( $order_item_id, 'subtotal', $subtotal );
                    hb_add_order_item_meta( $order_item_id, 'total', $total );
            		hb_add_order_item_meta( $order_item_id, 'tax_total', $total - $subtotal );

                    if ( isset( $meta[ 'extra_packages' ] ) && ! empty( $meta[ 'extra_packages' ] ) ) {
                    	foreach ( $meta[ 'extra_packages' ] as $package_id => $qty ) {
	                    	$order_package_item_id = hb_add_order_item( $booking_id, array(
	                            'order_item_name'       => get_the_title( $package_id ),
	                            'order_item_type'       => 'sub_item',
	                            'order_item_parent'		=> $order_item_id
	                        ));
	                        hb_add_order_item_meta( $order_package_item_id, 'product_id', $package_id );
		                    hb_add_order_item_meta( $order_package_item_id, 'qty', $qty );
		                    hb_add_order_item_meta( $order_package_item_id, 'check_in_date', strtotime( $meta['check_in_date'] ) );
		                    hb_add_order_item_meta( $order_package_item_id, 'check_out_date', strtotime( $meta['check_out_date'] ) );

		                    if ( class_exists( 'HB_Extra_Package' ) ) {
		                    	$package = HB_Extra_Package::instance( $package_id, $meta['check_in_date'], $meta['check_out_date'], $meta['quantity'], $qty );
		                    	// new meta
		                    	$subtotal = $package->amount_exclude_tax();
		                    	$total = $package->amount_include_tax();
		                    	hb_add_order_item_meta( $order_package_item_id, 'subtotal', $subtotal );
			                    hb_add_order_item_meta( $order_package_item_id, 'total', $total );
			            		hb_add_order_item_meta( $order_package_item_id, 'tax_total', $total - $subtotal );
		                    }
                    	}
                    }
                }
            }
        }
        // delete old meta data
        delete_post_meta( $booking_id, '_hb_booking_params' );
    } else if ( $param->meta_key === '_hb_booking_cart_params' ) {
        $parents = array();
        foreach ( $params as $cart_id => $param ) {
            $meta = array(
                            'order_item_name'       => get_the_title( $id ),
                            'order_item_type'       => get_post_type( $id ) === 'hb_room' ? 'line_item' : 'sub_item',
                            'order_item_parent'     => isset( $parents[ $cart_id ] ) ? $parents[ $cart_id ] : null
                        );
            $order_item_id = hb_add_order_item( $booking_id, $meta );
            $parents[ $cart_id ] = $order_item_id;

            // add order item meta
            hb_add_order_item_meta( $order_item_id, 'product_id', $param->product_id );
            hb_add_order_item_meta( $order_item_id, 'qty', $param->quantity );
            hb_add_order_item_meta( $order_item_id, 'check_in_date', strtotime( $param->check_in_date ) );
            hb_add_order_item_meta( $order_item_id, 'check_out_date', strtotime( $param->check_out_date ) );
            hb_add_order_item_meta( $order_item_id, 'subtotal', $param->amount_exclude_tax );
            hb_add_order_item_meta( $order_item_id, 'total', $param->amount_include_tax );
            hb_add_order_item_meta( $order_item_id, 'tax_total', $param->amount_tax );
        }
        // delete old meta data
        delete_post_meta( $booking_id, '_hb_booking_cart_params' );
    }
}
