<?php

/**
 * @Author: ducnvtt
 * @Date  :   2016-04-01 09:45:55
 * @Last  Modified by:   someone
 * @Last  Modified time: 2016-05-13 15:23:06
 */
/**
 * set table name.
 */
if ( !function_exists( 'hotel_booking_set_table_name' ) ) {

	function hotel_booking_set_table_name() {
		global $wpdb;
		$order_item          = 'hotel_booking_order_items';
		$order_itemmeta      = 'hotel_booking_order_itemmeta';
		$hotel_booking_plans = 'hotel_booking_plans';

		$wpdb->hotel_booking_order_items    = $wpdb->prefix . $order_item;
		$wpdb->hotel_booking_order_itemmeta = $wpdb->prefix . $order_itemmeta;
		$wpdb->hotel_booking_plans          = $wpdb->prefix . $hotel_booking_plans;

		$wpdb->tables[] = 'hotel_booking_order_items';
		$wpdb->tables[] = 'hotel_booking_order_itemmeta';
		$wpdb->tables[] = 'hotel_booking_plans';
	}

	add_action( 'init', 'hotel_booking_set_table_name', 0 );
	add_action( 'switch_blog', 'hotel_booking_set_table_name', 0 );
}

if ( !function_exists( 'hotel_booking_get_room_available' ) ) {

	function hotel_booking_get_room_available( $room_id = null, $args = array() ) {
		$valid  = true;
		$errors = new WP_Error;
		if ( !$room_id ) {
			$valid = false;
			$errors->add( 'room_id_invalid', __( 'Room not found.', 'wp-hotel-booking' ) );
		}

		$args = wp_parse_args( $args, array(
			'check_in_date'  => '',
			'check_out_date' => '',
			'excerpt'        => array(
				0
			)
		) );

		if ( !$args['check_in_date'] ) {
			$valid = false;
			$errors->add( 'check_in_date_not_available', __( 'Check in date is not valid.', 'wp-hotel-booking' ) );
		} else {
			if ( !is_numeric( $args['check_in_date'] ) ) {
				$args['check_in_date'] = strtotime( $args['check_in_date'] );
			}
		}

		if ( !$args['check_out_date'] ) {
			$valid = false;
			$errors->add( 'check_out_date_not_available', __( 'Check out date is not valid.', 'wp-hotel-booking' ) );
		} else {
			if ( !is_numeric( $args['check_out_date'] ) ) {
				$args['check_out_date'] = strtotime( $args['check_out_date'] );
			}
		}

		// $valid is false
		if ( $valid === false ) {
			return $errors;
		} else {
			global $wpdb;

			$not = $wpdb->prepare( "
					SELECT SUM( meta.meta_value ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
						LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS room ON order_item.order_item_id = room.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id
						LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
					WHERE
						meta.meta_key = %s
						AND room.meta_value = %d
						AND room.meta_key = %s
						AND checkin.meta_key = %s
						AND checkout.meta_key = %s
						AND (
								( checkin.meta_value >= %d AND checkin.meta_value < %d )
							OR 	( checkout.meta_value > %d AND checkout.meta_value <= %d )
							OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
						)
						AND booking.post_type = %s
						AND booking.post_status IN ( %s, %s, %s )
						AND order_item.order_id NOT IN( %s )
				", 'qty', $room_id, 'product_id', 'check_in_date', 'check_out_date', $args['check_in_date'], $args['check_out_date'], $args['check_in_date'], $args['check_out_date'], $args['check_in_date'], $args['check_out_date'], 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending', implode( ',', $args['excerpt'] )
			);

			$sql = $wpdb->prepare( "
					SELECT number.meta_value AS qty FROM $wpdb->postmeta AS number
						INNER JOIN $wpdb->posts AS hb_room ON hb_room.ID = number.post_id
					WHERE
						number.meta_key = %s
						AND hb_room.ID = %d
						AND hb_room.post_type = %s
				", '_hb_num_of_rooms', $room_id, 'hb_room' );

			$qty = absint( $wpdb->get_var( $sql ) ) - absint( $wpdb->get_var( $not ) );
			if ( $qty === 0 ) {
				$errors->add( 'zero', __( 'This room is not available.', 'wp-hotel-booking' ) );
				return $errors;
			}
			return apply_filters( 'hotel_booking_get_room_available', $qty, $room_id, $args );
		}
	}

}

// product class process
if ( !function_exists( 'hotel_booking_get_product_class' ) ) {

	function hotel_booking_get_product_class( $product_id = null, $params = array() ) {

		$post_type = get_post_type( $product_id );

		$product = 'WPHB_Product_' . implode( '_', array_map( 'ucfirst', explode( '_', $post_type ) ) );
		if ( !class_exists( $product ) ) {
			$product = 'WPHB_Room';
		}

		$product = apply_filters( 'hotel_booking_cart_product_class_name', $product, $product_id );
		$product = new $product( $product_id, $params );

		return apply_filters( 'hotel_booking_get_product_class', $product, $product_id, $params );
	}

}

if ( !function_exists( 'hb_create_page' ) ) {
	function hb_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( $page_object && 'page' === $page_object->post_type && !in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
				// Valid page is already in place
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		$valid_page_found = apply_filters( 'hotel_booking_create_page_id', $valid_page_found, $slug, $page_content );

		if ( $valid_page_found ) {
			if ( $option ) {
				update_option( $option, $valid_page_found );
			}
			return $valid_page_found;
		}

		// Search for a matching valid trashed page
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = array(
				'ID'          => $page_id,
				'post_status' => 'publish',
			);
			wp_update_post( $page_data );
		} else {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed'
			);
			$page_id   = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}
}

if ( is_multisite() ) {
	if ( file_exists( ABSPATH . 'wp-content/plugins/tp-hotel-booking/tp-hotel-booking.php' ) && !get_site_option( 'wphb_notice_remove_hotel_booking' ) ) {
		add_action( 'network_admin_notices', 'hb_notice_remove_hotel_booking' );
		add_action( 'admin_notices', 'hb_notice_remove_hotel_booking' );
	}
} else {
	if ( file_exists( ABSPATH . 'wp-content/plugins/tp-hotel-booking/tp-hotel-booking.php' ) && !get_option( 'wphb_notice_remove_hotel_booking' ) ) {
		add_action( 'admin_notices', 'hb_notice_remove_hotel_booking' );
	}
}

/**
 * Show notice required remove tp hotel booking plugin and add-ons
 */

if ( !function_exists( 'hb_notice_remove_hotel_booking' ) ) {
	function hb_notice_remove_hotel_booking() { ?>
        <div class="notice notice-error hb-dismiss-notice is-dismissible">
            <p>
				<?php echo __( wp_kses( '<strong>WP Hotel Booking</strong> plugin version ' . WPHB_VERSION . ' is an upgrade of <strong>TP Hotel Booking</strong> plugin. Please deactivate and delete <strong>TP Hotel Booking/TP Hotel Booking add-ons</strong> and replace by <strong>WP Hotel Booking/WP Hotel Booking add-ons</strong>.', array( 'strong' => array() ) ), 'wp-hotel-booking' ); ?>
            </p>

        </div>
	<?php }
}