<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 16:44:29
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-04 12:00:07
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'HB_INSTALLING' ) || HB_INSTALLING !== true ) {
	exit();
}

global $wpdb;

/**
 * Upgrade booking item
 * @var
 */
$sql = $wpdb->prepare("
        SELECT param.* FROM $wpdb->posts AS booking
            INNER JOIN $wpdb->postmeta AS param ON booking.ID = param.post_id
        WHERE
            booking.post_type = %s
            AND param.meta_key IN ( %s, %s )
            GROUP BY booking.ID
    ", 'hb_booking', '_hb_booking_params', '_hb_booking_cart_params' );

$params = $wpdb->get_results( $sql );

if ( $params ) {

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
                                'check_in_date'     => $meta['check_in_date'],
                                'check_out_date'    => $meta['check_out_date'],
                                'quantity'          => $meta['quantity']
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
                                    'order_item_parent'     => $order_item_id
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
}
/**
 * End upgrade booking
 */

/**
 * Upgrade customer
 */

global $wpdb;
$sql = $wpdb->prepare("
        SELECT customer.ID AS cus_ID, bookmeta.post_id as book_ID FROM $wpdb->posts AS customer
            LEFT JOIN $wpdb->postmeta AS meta ON customer.ID = meta.post_id
            LEFT JOIN $wpdb->postmeta AS bookmeta ON bookmeta.meta_value = customer.ID
        WHERE
            customer.post_type = %s
            AND meta.meta_key = %s
            AND bookmeta.meta_key = %s
    ", 'hb_customer', '_hb_email', '_hb_customer_id' );
$customers = $wpdb->get_results( $sql );

$query = "INSERT INTO $wpdb->postmeta
    ( post_id, meta_key, meta_value )
    VALUES
";

if ( $customers ) {
    $insert = array();
    foreach ( $customers as $k => $customer ) {
        if ( $customer->book_ID && $customer->cus_ID ) {
            $sql = $wpdb->prepare("
                    SELECT meta.meta_key, meta.meta_value FROM $wpdb->postmeta AS meta
                        LEFT JOIN $wpdb->posts AS customer ON customer.ID = meta.post_id
                    WHERE
                        customer.post_type = %s
                        AND customer.ID = %d
                ", 'hb_customer', $customer->cus_ID );

            $customer_info = $wpdb->get_results( $sql );
            if ( $customer_info ) {
                foreach ( $customer_info as $k => $cus_info ) {
                    $meta_key = str_replace( '_hb_', '_hb_customer_', $cus_info->meta_key );
                    $insert[] = "(". $customer->book_ID . ",'".$meta_key."','".$cus_info->meta_value."' )";
                }
            }
        }

    }
    $query .= implode( ',', $insert );
}
$wpdb->query( $query );
/**
 * End upgrade customer
 */
