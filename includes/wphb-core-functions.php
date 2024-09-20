<?php
/**
 * WP Hotel Booking core functions.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Functions
 * @category      Functions
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * set table name.
 */
if ( ! function_exists( 'hotel_booking_set_table_name' ) ) {

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

if ( ! function_exists( 'hotel_booking_get_room_available' ) ) {
	/**
	 * @param $room_id
	 * @param $args
	 *
	 * @return int|WP_Error
	 */
	function hotel_booking_get_room_available( $room_id = null, $args = array() ) {
		$qty = 0;

		try {
			// for search room in single with wpml
			$room_id = apply_filters( 'hotel_booking_get_available_room', $room_id );

			if ( ! $room_id ) {
				throw new Exception( __( 'Request is invalid!', 'wp-hotel-booking' ) );
			}

			$room = get_post( $room_id );
			if ( ! $room || WPHB_ROOM_CT !== $room->post_type ) {
				throw new Exception( __( 'Room not found!', 'wp-hotel-booking' ) );
			}

			$args = wp_parse_args(
				$args,
				array(
					'check_in_date'  => '',
					'check_out_date' => '',
					'excerpt'        => array(
						0,
					),
				)
			);

			if ( empty( $args['check_in_date'] ) || empty( $args['check_out_date'] ) ) {
				throw new Exception( __( 'Check in date and check out date is required.', 'wp-hotel-booking' ) );
			}

			$check_in_date_timestamp = $args['check_in_date'];
			if ( ! is_numeric( $args['check_in_date'] ) ) {
				$check_in_date_timestamp = strtotime( $args['check_in_date'] );
			}

			$check_out_date_timestamp = $args['check_out_date'];
			if ( ! is_numeric( $args['check_out_date'] ) ) {
				$check_out_date_timestamp = strtotime( $args['check_out_date'] );
			}

			// Get date now by timezone and check with check in date
			/*$date_now_timestamp = current_time( 'timestamp' );
			$check_in_date_timestamp = strtotime( $check_in_date_str );
			if ( $check_in_date_timestamp < $date_now_timestamp ) {
				throw new Exception(
					sprintf(
						__( 'Check in date must be greater than or equal to today %s %s.', 'wp-hotel-booking' ),
						gmdate( WPHB_Datetime::$format, $date_now_timestamp ),
						WPHB_Datetime::get_timezone_string()
					)
				);
			}*/

			$room_available_date = WPHB_Room::instance( $room_id )->get_dates_available();
			$arr_qty_available   = array();

			$checkin   = gmdate( WPHB_Datetime::$format, $check_in_date_timestamp );
			$checkout  = gmdate( WPHB_Datetime::$format, $check_out_date_timestamp );
			$date_next = $checkin;

			while ( $date_next <= $checkout ) {
				$timeStamp = strtotime( $date_next );
				if ( array_key_exists( $timeStamp, $room_available_date ) ) {
					if ( $room_available_date[ $timeStamp ] >= 1 ) {
						$room_available_date[ $timeStamp ] = $room_available_date[ $timeStamp ] - 1;
						$arr_qty_available[]               = $room_available_date[ $timeStamp ];
					}
				}
				$date_next = gmdate( WPHB_Datetime::$format, strtotime( $date_next . ' +1 day' ) );
			}

			$qty = get_post_meta( $room_id, '_hb_num_of_rooms', true );

			if ( ! empty( $arr_qty_available ) ) {
				$qty = ! empty( min( $arr_qty_available ) ) ? min( $arr_qty_available ) : 1;
			}

			$blocked_id = get_post_meta( $room_id, 'hb_blocked_id', true );
			if ( ! empty( $blocked_id ) ) {
				$dates_timestamp_blocked = get_post_meta( $blocked_id, 'hb_blocked_time', false );
				if ( ! empty( $dates_timestamp_blocked ) ) {
					/*foreach ( $date_blocked as $date ) {
						if ( $date >= $check_in_date_timestamp && $date < $check_out_date_timestamp ) {
							throw new Exception( __( 'This room is not available.', 'wp-hotel-booking' ) );
						}
					}*/
                    $dates_str_blocked = [];
                    foreach ( $dates_timestamp_blocked as $date_timestamp_blocked ) {
                        $dates_str_blocked[] = gmdate( WPHB_Datetime::$format, $date_timestamp_blocked );
                    }

                    $date_timestamp_check = $check_in_date_timestamp;
                    while ( $date_timestamp_check < $check_out_date_timestamp ) {
                        $date_check = gmdate( WPHB_Datetime::$format, $date_timestamp_check );
                        if ( in_array( $date_check, $dates_str_blocked ) ) {
                            throw new Exception( __( 'This room is not available!', 'wp-hotel-booking' ) );
                        }

                        $date_timestamp_check = strtotime( $date_check . ' +1 day' );
                    }
				}
			}

			$qty = apply_filters( 'wphb/room/check_dates_available', $qty, $room, $args );

			if ( $qty === 0 ) {
				throw new Exception( __( 'This room is not available.', 'wp-hotel-booking' ) );
			}
		} catch ( Throwable $e ) {
			$qty = new WP_Error( 'error', $e->getMessage() );
		}

		return $qty;
	}
}

/**
 * @param int $date_check_in_timestamp
 * @param int $date_check_out_timestamp
 *
 * @return string[]
 * @since 2.1.3
 * @version 1.0.0
 */
function wphb_get_nights_book( $date_check_in_timestamp = 0, $date_check_out_timestamp = 0 ) {
	$nights_book     = array();
	$date_next_night = gmdate( WPHB_Datetime::$format, $date_check_in_timestamp );
	while ( $date_check_in_timestamp < strtotime( $date_check_out_timestamp ) ) {
		$nights_book[]   = $date_next_night;
		$date_next_night = date( WPHB_Datetime::$format, strtotime( $date_next_night . ' +1 day' ) );
	}

	return $nights_book;
}

// product class process
if ( ! function_exists( 'hotel_booking_get_product_class' ) ) {
	function hotel_booking_get_product_class( $product_id = null, $params = array() ) {

		$post_type = get_post_type( $product_id );

		$product = 'WPHB_Product_' . implode( '_', array_map( 'ucfirst', explode( '_', $post_type ) ) );
		if ( ! class_exists( $product ) ) {
			$product = 'WPHB_Room';
		}

		$product = apply_filters( 'hotel_booking_cart_product_class_name', $product, $product_id );
		$product = new $product( $product_id, $params );

		return apply_filters( 'hotel_booking_get_product_class', $product, $product_id, $params );
	}
}

if ( ! function_exists( 'hb_create_page' ) ) {
	function hb_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( $page_object && 'page' === $page_object->post_type && ! in_array(
					$page_object->post_status,
					array(
						'pending',
						'trash',
						'future',
						'auto-draft',
					)
				) ) {
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
				'comment_status' => 'closed',
			);
			$page_id   = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( is_multisite() ) {
	if ( is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) && ! get_site_option( 'wphb_notice_remove_hotel_booking' ) ) {
		add_action( 'network_admin_notices', 'hb_notice_remove_hotel_booking' );
		add_action( 'admin_notices', 'hb_notice_remove_hotel_booking' );
	}
} else {
	if ( is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) && ! get_option( 'wphb_notice_remove_hotel_booking' ) ) {
		add_action( 'admin_notices', 'hb_notice_remove_hotel_booking' );
	}
}

/**
 * Show notice required remove tp hotel booking plugin and add-ons
 */

if ( ! function_exists( 'hb_notice_remove_hotel_booking' ) ) {
	function hb_notice_remove_hotel_booking() {
		?>
        <div class="notice notice-error hb-dismiss-notice is-dismissible">
            <p>
				<?php echo wp_kses( '<strong>WP Hotel Booking</strong> plugin version ' . WPHB_VERSION . ' is an upgrade of <strong>TP Hotel Booking</strong> plugin. Please deactivate and delete <strong>TP Hotel Booking/TP Hotel Booking add-ons</strong> and replace by <strong>WP Hotel Booking/WP Hotel Booking add-ons</strong>.', array( 'strong' => array() ), 'wp-hotel-booking' ); ?>
            </p>

        </div>
		<?php
	}
}

if ( ! function_exists( 'hb_extra_types' ) ) {
	/**
	 * @return array|mixed
	 */
	function hb_extra_types() {
		$types = apply_filters(
			'hb_extra_type',
			array(
				'trip'   => __( 'Trip', 'wp-hotel-booking' ),
				'number' => __( 'Number', 'wp-hotel-booking' ),
			)
		);

		return is_array( $types ) ? $types : array();
	}
}

add_action( 'widgets_init', 'hotel_booking_widget_init' );

if ( ! function_exists( 'hotel_booking_widget_init' ) ) {
	/**
	 * Register widgets.
	 */
	function hotel_booking_widget_init() {
		register_widget( 'HB_Widget_Search' );
		register_widget( 'HB_Widget_Room_Carousel' );
		register_widget( 'HB_Widget_Best_Reviews' );
		register_widget( 'HB_Widget_Lastest_Reviews' );
		register_widget( 'HB_Widget_Mini_Cart' );
	}
}
