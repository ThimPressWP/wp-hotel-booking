<?php
/**
 * WP Hotel Booking room functions.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Functions
 * @category    Core Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'hb_room_get_pricing_plans' ) ) {

	// get pricing plans
	function hb_room_get_pricing_plans( $room_id = null ) {
		if ( ! $room_id ) {
			// throw new Exception( __( 'Room id is not exists.', 'wp-hotel-booking' ), 503 );
		}

		global $wpdb;

		if ( ! isset( $wpdb->hotel_booking_plans ) ) {
			hotel_booking_set_table_name();
		}

		$sql = $wpdb->prepare(
			"
				SELECT plans.* FROM $wpdb->hotel_booking_plans AS plans
					INNER JOIN $wpdb->posts AS room ON room.ID = plans.room_id
				WHERE
					plans.room_id = %d
					AND room.post_type = %s
					AND room.post_status = %s
				ORDER BY plan_id DESC
			",
			$room_id,
			'hb_room',
			'publish'
		);

		$cols  = $wpdb->get_results( $sql );
		$plans = array();

		foreach ( $cols as $k => $plan ) {
			$pl = new stdClass();

			$pl->ID     = $plan->plan_id;
			$pl->start  = $plan->start_time;
			$pl->end    = $plan->end_time;
			$pl->prices = maybe_unserialize( $plan->pricing );

			$plans[ $plan->plan_id ] = $pl;
		}

		return apply_filters( 'hb_room_get_pricing_plans', $plans, $room_id );
	}
}

if ( ! function_exists( 'hb_room_set_pricing_plan' ) ) {

	/**
	 * hb_room_set_pricing_plan set new pricing plans
	 *
	 * @param array $args
	 *
	 * @start_time
	 * @end_time
	 * @pricing param
	 * @plan id if update
	 * @return plan id
	 */
	function hb_room_set_pricing_plan( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'start_time' => null,
				'end_time'   => null,
				'pricing'    => null,
				'room_id'    => null,
				'plan_id'    => null,
			)
		);

		global $wpdb;

		if ( $args['plan_id'] && $args['plan_id'] > 0 ) {
			$wpdb->update(
				$wpdb->hotel_booking_plans,
				array(
					'start_time' => $args['start_time'] ? date( 'Y-m-d H:i:s', $args['start_time'] ) : null,
					'end_time'   => $args['end_time'] ? date( 'Y-m-d H:i:s', $args['end_time'] ) : null,
					'pricing'    => maybe_serialize( $args['pricing'] ),
				),
				array( 'plan_id' => $args['plan_id'] ),
				array( '%s', '%s', '%s' ),
				array( '%d' )
			);
			$plan_id = $args['plan_id'];
		} else {
			$wpdb->insert(
				$wpdb->hotel_booking_plans,
				array(
					'start_time' => $args['start_time'] ? date( 'Y-m-d H:i:s', $args['start_time'] ) : null,
					'end_time'   => $args['end_time'] ? date( 'Y-m-d H:i:s', $args['end_time'] ) : null,
					'pricing'    => maybe_serialize( $args['pricing'] ),
					'room_id'    => $args['room_id'],
				),
				array( '%s', '%s', '%s', '%d' )
			);

			$plan_id = absint( $wpdb->insert_id );
		}

		do_action( 'hotel_booking_created_pricing_plan', $plan_id, $args );

		return $plan_id;
	}
}

if ( ! function_exists( 'hb_room_get_selected_plan' ) ) {

	function hb_room_get_selected_plan( $room_id = null, $date = null ) {
		if ( ! $room_id ) {
			return null;
		}

		if ( ! $date ) {
			$date = time();
		}
		$regular_plan  = null;
		$selected_plan = null;

		$plans = hb_room_get_pricing_plans( $room_id );

		if ( $plans ) {
			foreach ( $plans as $plan ) {
				if ( $plan->start && $plan->end ) {
					$start = strtotime( $plan->start );
					$end   = strtotime( $plan->end );

					if ( $date >= $start && $date <= $end ) {
						$selected_plan = $plan;
						break;
					}
				} elseif ( ! $regular_plan ) {
					$selected_plan = $regular_plan = $plan;
				}
			}
		}
		//      echo '<pre>';
		//      print_r( $plans );
		//      echo '</pre>';
		//      echo '<pre>';
		//      print_r( $selected_plan );
		//      echo '</pre>';

		return apply_filters( 'hb_room_get_selected_plan', $selected_plan );
	}
}

if ( ! function_exists( 'hb_room_get_regular_plan' ) ) {

	function hb_room_get_regular_plan( $room_id = null ) {
		if ( ! $room_id ) {
			return null;
		}

		$plans        = hb_room_get_pricing_plans( $room_id );
		$regular_plan = null;
		if ( $plans ) {
			foreach ( $plans as $plan ) {
				if ( ! $plan->start && ! $plan->end ) {
					$regular_plan = $plan;
				}
			}
		}

		return apply_filters( 'hb_room_get_regular_plan', $regular_plan );
	}
}

if ( ! function_exists( 'hb_room_remove_pricing' ) ) {

	/**
	 * hb_room_remove_pricing
	 * remove pricing plan by id of table $wpdb->hotel_booking_plans
	 *
	 * @param  $plan_id integer
	 *
	 * @return null if $plan_id invalid and plan id if valid
	 */
	function hb_room_remove_pricing( $plan_id = null ) {

		if ( ! $plan_id ) {
			return;
		}

		global $wpdb;
		$wpdb->delete( $wpdb->hotel_booking_plans, array( 'plan_id' => $plan_id ), array( '%d' ) );

		do_action( 'hb_room_remove_pricing', $plan_id );

		return $plan_id;
	}
}

if ( ! function_exists( 'hotel_booking_print_pricing_json' ) ) {

	function hotel_booking_print_pricing_json( $room_id = null, $date = null ) {
		$start = date( 'm/01/Y', strtotime( $date ) );
		$end   = date( 'm/t/Y', strtotime( $date ) );

		$json = array();
		if ( ! $room_id || ! $date ) {
			return $json;
		}

		$month_day = date( 't', strtotime( $end ) );
		$room      = WPHB_Room::instance( $room_id );
		for ( $i = 0; $i < $month_day; $i++ ) {
			$day   = strtotime( $start ) + $i * 24 * HOUR_IN_SECONDS;
			$price = $room->get_price( $day, false );

			// $json[] = array(
			// 'title' => $price ? floatval( $price ) : '0',
			// 'start' => date( 'Y-m-d', $day ),
			// 'end'   => date( 'Y-m-d', strtotime( '+1 day', $day ) )
			// );
			$json[] = array(
				'price' => $price ? floatval( $price ) : '0',
				'd'     => date( 'Y-m-d\TH:i:s\Z', $day ),
				// 'end'   => date( 'Y-m-d', strtotime( '+1 day', $day ) )
			);
		}

		return json_encode( $json );
	}
}

if ( ! function_exists( 'hb_room_update_room_price_meta' ) ) {
	function hb_room_update_room_price_meta( $room_id = null ) {
		if ( $room_id === null ) {
			return;
		}

		$price = WPHB_Room::instance( $room_id )->get_price();

		$old_price = get_post_meta( $room_id, 'hb_price', true );

		if ( $price !== $old_price ) {
			update_post_meta( $room_id, 'hb_price', $price );
		}
	}
}

if ( ! function_exists( 'hb_room_update_room_average_rating' ) ) {
	function hb_room_update_room_average_rating( $room_id = null ) {
		if ( $room_id === null ) {
			return;
		}

		$room           = WPHB_Room::instance( $room_id );
		$average_rating = floatval( $room->average_rating() );
		$average_rating = number_format( $average_rating, 2 );

		$old_rating = get_post_meta( $room_id, 'hb_average_rating', true );

		if ( $old_rating !== $average_rating ) {
			update_post_meta( $room_id, 'hb_average_rating', $average_rating );
		}
	}
}

if ( ! function_exists( 'hb_get_room_query_args' ) ) {
	function hb_get_room_query_args( $atts = [] ) {
		$hb_settings = WPHB_Settings::instance();

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		// default posts_per_page
		$posts_per_page = $hb_settings->get( 'posts_per_page', 8 );

		// if $attrs has value passed in: set $attrs['number_room'] instead of posts_per_page
		if ( isset( $atts['number_room'] ) ) {
			$posts_per_page = $atts['number_room'];
		}

		$atts = shortcode_atts(
			array( // shortcode: default atts
				'room_type'   => '',
				'orderby'     => 'date',
				'order'       => 'DESC',
				'number_room' => -1,
				'room_in'     => '',
				'room_not_in' => '',
				'paged'       => $paged,
			),
			$atts // atts
		);

		// default args
		$args = array(
			'post_type'      => 'hb_room',
			'posts_per_page' => $posts_per_page,
			'orderby'        => $atts['orderby'],
			'order'          => $atts['order'],
			'post_status'    => 'publish',
			'paged'          => $paged,
		);

		// Start get data filter from $_GET
		// 1.Sort By
		$sort_by = hb_get_request( 'sort_by' );
		if ( $sort_by ) {
			$sort_options = [
				'date-desc'  => [ 'date', 'DESC' ],
				'date-asc'   => [ 'date', 'ASC' ],
				'title-asc'  => [ 'title', 'ASC' ],
				'title-desc' => [ 'title', 'DESC' ],
			];
			if ( isset( $sort_options[ $sort_by ] ) ) {
				$args['orderby'] = $sort_options[ $sort_by ][0];
				$args['order']   = $sort_options[ $sort_by ][1];
			}
		}

		// 2.Price Filter
		$min_price = sanitize_text_field( $_GET['min_price'] ?? 0 );
		$max_price = hb_get_request( 'max_price' );

		if ( $min_price >=0 && $max_price ) {
			$args['meta_query'][] = array(
				'key'     => 'hb_price',
				'value'   => array( $min_price, $max_price ),
				'type'    => 'DECIMAL',
				'compare' => 'BETWEEN',
			);
		}

		// 3.Rating Filter
		$rating = hb_get_request( 'rating' );
		if ( $rating ) {
			$rating = explode( ',', $rating );
			// remap data to query
			$rating = array_map( function( $value ) {
			    return ( $value === 'unrated' ) ? 0 : $value;
			}, $rating );
			if ( count( $rating ) > 1 ) {
				$rating_query             = array();
				$rating_query['relation'] = 'OR';
				foreach ( $rating as $rate ) {
					$rating_query[] = array(
						'relation' => 'AND',
						array(
							'key'   => 'hb_average_rating',
							'value'   => $rate + 0,
							'compare' => '>=',
							// 'type'    => 'DECIMAL'
						),
						array(
							'key'   => 'hb_average_rating',
							'value'   => $rate + 1,
							'compare' => '<',
							// 'type'    => 'DECIMAL'
						),
					);
				}
				$args['meta_query'][] = $rating_query;
			} else {
				$args['meta_query'][] = array(
					'relation' => 'AND',
					array(
						'key'   => 'hb_average_rating',
						'value'   => $rating[0] + 0,
						'compare' => '>=',
						// 'type'    => 'DECIMAL'
					),
					array(
						'key'   => 'hb_average_rating',
						'value'   => $rating[0] + 1,
						'compare' => '<',
						// 'type'    => 'DECIMAL'
					),
				);
			}
		}

		if ( isset( $args['meta_query'] ) ) {
			$args['meta_query']['relation'] = 'AND';
		}

		// 4.Room Type Filter
		$room_type = hb_get_request( 'room_type' ) ? $atts['room_type'] : $atts['room_type'];
		if ( is_tax( 'hb_room_type' ) ) { // is taxonomy page hb_room_type
			$term_slug = get_query_var( 'term' );
			$term      = get_term_by( 'slug', $term_slug, 'hb_room_type' );
			$room_type = $term->term_id;
		}
		if ( $room_type ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'hb_room_type',
				'field'    => is_numeric( $room_type ) ? 'id' : 'slug',
				'terms'    => explode( ',', $room_type ),
			);
		}

		if ( isset( $args['tax_query'] ) ) {
			$args['tax_query']['relation'] = 'AND';
		}

		// Include/Exclude Rooms ( for shortcode )
		if ( $atts['room_in'] ) {
			$args['post__in'] = explode( ',', $atts['room_in'] );
		}
		if ( $atts['room_not_in'] ) {
			$args['post__not_in'] = explode( ',', $atts['room_not_in'] );
		}
		return $args; // return $args after filter
	}
}
