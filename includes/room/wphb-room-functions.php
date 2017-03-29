<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-04-12 13:08:14
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-13 14:55:29
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !function_exists( 'hb_room_get_pricing_plans' ) ) {

    // get pricing plans
    function hb_room_get_pricing_plans( $room_id = null ) {
        if ( !$room_id ) {
            // throw new Exception( __( 'Room id is not exists.', 'wp-hotel-booking' ), 503 );
        }

        global $wpdb;

        if ( !isset( $wpdb->hotel_booking_plans ) ) {
            hotel_booking_set_table_name();
        }

        $sql = $wpdb->prepare( "
				SELECT plans.* FROM $wpdb->hotel_booking_plans AS plans
					INNER JOIN $wpdb->posts AS room ON room.ID = plans.room_id
				WHERE
					plans.room_id = %d
					AND room.post_type = %s
					AND room.post_status = %s
			", $room_id, 'hb_room', 'publish' );

        $cols = $wpdb->get_results( $sql );
        $plans = array();

        foreach ( $cols as $k => $plan ) {
            $pl = new stdClass;

            $pl->ID = $plan->plan_id;
            $pl->start = $plan->start_time;
            $pl->end = $plan->end_time;
            $pl->prices = maybe_unserialize( $plan->pricing );

            $plans[$plan->plan_id] = $pl;
        }

        return apply_filters( 'hb_room_get_pricing_plans', $plans, $room_id );
    }

}

if ( !function_exists( 'hb_room_set_pricing_plan' ) ) {

    /**
     * hb_room_set_pricing_plan set new pricing plans
     * @param  array  $args
     * @start_time
     * @end_time
     * @pricing param
     * @plan id if update
     * @return plan id
     */
    function hb_room_set_pricing_plan( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'start_time' => null,
            'end_time' => null,
            'pricing' => null,
            'room_id' => null,
            'plan_id' => null
                ) );

        global $wpdb;

        if ( $args['plan_id'] && $args['plan_id'] > 0 ) {
            $wpdb->update(
                    $wpdb->hotel_booking_plans, array(
                'start_time' => $args['start_time'] ? date( 'Y-m-d H:i:s', $args['start_time'] ) : null,
                'end_time' => $args['end_time'] ? date( 'Y-m-d H:i:s', $args['end_time'] ) : null,
                'pricing' => maybe_serialize( $args['pricing'] )
                    ), array(
                'plan_id' => $args['plan_id']
                    ), array(
                '%s',
                '%s',
                '%s'
                    ), array( '%d' )
            );
            $plan_id = $args['plan_id'];
        } else {
            $wpdb->insert(
                    $wpdb->hotel_booking_plans, array(
                'start_time' => $args['start_time'] ? date( 'Y-m-d H:i:s', $args['start_time'] ) : null,
                'end_time' => $args['end_time'] ? date( 'Y-m-d H:i:s', $args['end_time'] ) : null,
                'pricing' => maybe_serialize( $args['pricing'] ),
                'room_id' => $args['room_id']
                    ), array(
                '%s',
                '%s',
                '%s',
                '%d'
                    )
            );

            $plan_id = absint( $wpdb->insert_id );
        }

        do_action( 'hotel_booking_created_pricing_plan', $plan_id, $args );

        return $plan_id;
    }

}

if ( !function_exists( 'hb_room_get_selected_plan' ) ) {

    function hb_room_get_selected_plan( $room_id = null, $date = null ) {
        if ( !$room_id ) {
            return;
        }

        if ( !$date ) {
            $date = time();
        }
        $regular_plan = null;
        $selected_plan = null;

        $plans = hb_room_get_pricing_plans( $room_id );
        if ( $plans ) {
            foreach ( $plans as $plan ) {
                if ( $plan->start && $plan->end ) {
                    $start = strtotime( $plan->start );
                    $end = strtotime( $plan->end );

                    if ( $date >= $start && $date <= $end ) {
                        $selected_plan = $plan;
                        break;
                    }
                } else if ( !$regular_plan ) {
                    $selected_plan = $regular_plan = $plan;
                }
            }
        }

        return apply_filters( 'hb_room_get_selected_plan', $selected_plan );
    }

}

if ( !function_exists( 'hb_room_get_regular_plan' ) ) {

    function hb_room_get_regular_plan( $room_id = null ) {
        if ( !$room_id ) {
            return null;
        }

        $plans = hb_room_get_pricing_plans( $room_id );
        $regular_plan = null;
        if ( $plans ) {
            foreach ( $plans as $plan ) {
                if ( !$plan->start && !$plan->end ) {
                    $regular_plan = $plan;
                }
            }
        }

        return apply_filters( 'hb_room_get_regular_plan', $regular_plan );
    }

}

if ( !function_exists( 'hb_room_remove_pricing' ) ) {

    /**
     * hb_room_remove_pricing
     * remove pricing plan by id of table $wpdb->hotel_booking_plans
     * @param  $plan_id integer
     * @return null if $plan_id invalid and plan id if valid
     */
    function hb_room_remove_pricing( $plan_id = null ) {

        if ( !$plan_id ) {
            return;
        }

        global $wpdb;
        $wpdb->delete( $wpdb->hotel_booking_plans, array( 'plan_id' => $plan_id ), array( '%d' ) );

        do_action( 'hb_room_remove_pricing', $plan_id );
        return $plan_id;
    }

}

if ( !function_exists( 'hotel_booking_print_pricing_json' ) ) {

    function hotel_booking_print_pricing_json( $room_id = null, $date = null ) {
        $start = date( 'm/01/Y', strtotime( $date ) );
        $end = date( 'm/t/Y', strtotime( $date ) );

        $json = array();
        if ( !$room_id || !$date ) {
            return $json;
        }

        $month_day = date( 't', strtotime( $end ) );
        $room = WPHB_Room::instance( $room_id );
        for ( $i = 0; $i < $month_day; $i++ ) {
            $day = strtotime( $start ) + $i * 24 * HOUR_IN_SECONDS;
            $price = $room->get_price( $day, false );

            $json[] = array(
                'title' => $price ? floatval( $price ) : '0',
                'start' => date( 'Y-m-d', $day ),
                'end' => date( 'Y-m-d', strtotime( '+1 day', $day ) )
            );
        }

        return json_encode( $json );
    }

}
