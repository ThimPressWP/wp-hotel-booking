<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-01 09:45:55
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-01 15:23:39
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
