<?php

/**
 * Report Class
 */
class HB_Report_Room extends HB_Report {

    public $_title;
    public $_chart_type = 'room';
    public $_rooms = array();
    public $_start_in;
    public $_end_in;
    public $chart_groupby;
    public $_axis_x = array();
    public $_axis_y = array();
    public $_range_start;
    public $_range_end;
    public $_query_results = null;
    public $_range;
    static $_instance = array();

    public function __construct( $range = null ) {
        if ( !$range )
            return;

        $this->_range = $range;

        if ( isset( $_GET['tab'] ) && $_GET['tab'] )
            $this->_chart_type = sanitize_text_field( $_GET['tab'] );

        if ( isset( $_GET['room_id'] ) && $_GET['room_id'] )
            $this->_rooms = (array) $_GET['room_id'];

        if ( !$this->_rooms )
            return;

        $this->calculate_current_range( $this->_range );

        $this->_title = sprintf( __( 'Chart in %s to %s', 'tp-hotel-booking-report' ), $this->_start_in, $this->_end_in );

        add_action( 'admin_init', array( $this, 'export_csv' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    function enqueue() {
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }

    public function get_rooms() {
        global $wpdb;
        $query = $wpdb->prepare( "
				(
					SELECT ID, post_title FROM {$wpdb->posts}
					WHERE
						`post_type` = %s
						AND `post_status` = %s
				)
			", 'hb_room', 'publish' );

        return $wpdb->get_results( $query );
    }

    /**
     * get all post have post_type = hb_booking
     * completed > start and < end
     * @return object
     */
    public function getOrdersItems() {
        global $wpdb;

        if ( $this->chart_groupby === 'day' ) {

            $query = $wpdb->prepare( "
					SELECT DATE( from_unixtime( check_in.meta_value ) ) AS checkindate, DATE( from_unixtime( check_out.meta_value ) ) as checkoutdate, product.meta_value AS room_ID, max_room.meta_key AS total
						FROM $wpdb->hotel_booking_order_items AS order_items
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_in ON check_in.hotel_booking_order_item_id = order_items.order_item_id AND check_in.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_out ON order_items.order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS product ON order_items.order_item_id = product.hotel_booking_order_item_id AND product.meta_key = %s
						LEFT JOIN $wpdb->posts AS booking ON booking.ID = order_items.order_id
						LEFT JOIN $wpdb->posts AS room ON room.ID = product.meta_value
						LEFT JOIN $wpdb->postmeta AS max_room ON max_room.post_id = room.ID AND max_room.meta_key = %s
					WHERE
						booking.post_status = %s
						AND room.post_status = %s
						AND room.post_type = %s
						AND room.ID IN ( %s )
						HAVING ( checkindate <= %s AND checkoutdate >= %s )
							OR ( checkindate >= %s AND checkindate <= %s )
							OR ( checkoutdate > %s AND checkoutdate <= %s )
				", 'check_in_date', 'check_out_date', 'product_id', '_hb_num_of_rooms', 'hb-completed', 'publish', 'hb_room', implode( ',', $this->_rooms ), $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in
            );
        } else {

            $query = $wpdb->prepare( "
					SELECT from_unixtime( check_in.meta_value ) AS checkindate, from_unixtime( check_out.meta_value ) as checkoutdate, product.meta_value AS room_ID, max_room.meta_key AS total
						FROM $wpdb->hotel_booking_order_items AS order_items
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_in ON check_in.hotel_booking_order_item_id = order_items.order_item_id AND check_in.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_out ON order_items.order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS product ON order_items.order_item_id = product.hotel_booking_order_item_id AND product.meta_key = %s
						LEFT JOIN $wpdb->posts AS booking ON booking.ID = order_items.order_id
						LEFT JOIN $wpdb->posts AS room ON room.ID = product.meta_value
						LEFT JOIN $wpdb->postmeta AS max_room ON max_room.post_id = room.ID AND max_room.meta_key = %s
					WHERE
						booking.post_status = %s
						AND room.post_status = %s
						AND room.post_type = %s
						AND room.ID IN ( %s )
						AND ( check_in.meta_value <= %s AND check_out.meta_value <= %s )
							OR ( check_in.meta_value >= %s AND check_in.meta_value <= %s )
							OR ( check_out.meta_value > %s AND check_out.meta_value <= %s )
				", 'check_in_date', 'check_out_date', 'product_id', '_hb_num_of_rooms', 'hb-completed', 'publish', 'hb_room', implode( ',', $this->_rooms ), $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in
            );
        }

        $results = $wpdb->get_results( $query );
        return $results;
    }

    public function series() {
        if ( !$this->_rooms ) {
            return;
        }

        $transient_name = 'tp_hotel_booking_charts_' . $this->_chart_type . '_' . $this->chart_groupby . '_' . $this->_range . '_' . $this->_start_in . '_' . $this->_end_in;
        delete_transient( $transient_name );
        if ( false === ( $chart_results = get_transient( $transient_name ) ) ) {
            // $chart_results = $this->parseData( $this->_query_results );
            $chart_results = $this->js_data();
            set_transient( $transient_name, $chart_results, 12 * HOUR_IN_SECONDS );
        }
        return apply_filters( 'hotel_booking_charts', $chart_results );
    }

    // new chartjs
    function js_data() {
        $results = $this->getOrdersItems();
        $series = array();
        $series['labels'] = array();
        $series['datasets'] = array();

        $ids = array();
        if ( !$results )
            return $series;

        foreach ( $results as $key => $value ) {
            if ( !isset( $ids[$value->room_ID] ) )
                $ids[$value->room_ID] = $value->total;
        }

        $label = array();
        foreach ( $ids as $id => $total ) {
            $serie = new stdClass();
            if ( !isset( $series[$id] ) ) {
                $data = new stdCLass();
            }

            $range = $this->_range_end - $this->_range_start;
            $cache = $this->_start_in;
            $data_recode = array();
            for ( $i = 0; $i <= $range; $i++ ) {
                $unavaiable = 0;
                if ( $this->chart_groupby === 'day' ) {
                    $current_time = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
                    $label[$current_time] = date( 'M.d', $current_time );
                } else {
                    $reg = $this->_range_start + $i;
                    $cache = date( "Y-$reg-01", strtotime( $cache ) );
                    $current_time = strtotime( date( "Y-$reg-01", strtotime( $cache ) ) );
                    $label[$current_time] = date( 'M.Y', $current_time );
                }

                foreach ( $results as $k => $v ) {

                    if ( (int) $v->room_ID !== (int) $id )
                        continue;

                    if ( $this->chart_groupby === 'day' ) {
                        $_in = strtotime( date( 'Y-m-d', strtotime( $v->checkindate ) ) );
                        $_out = strtotime( date( 'Y-m-d', strtotime( $v->checkoutdate ) ) );

                        if ( $current_time >= $_in && $current_time < $_out ) {
                            $unavaiable++;
                        }
                    } else {
                        $_in = strtotime( date( 'Y-m-1', strtotime( $v->checkindate ) ) );
                        $_out = strtotime( date( 'Y-m-1', strtotime( $v->checkoutdate ) ) );

                        if ( $current_time >= $_in && $current_time <= $_out ) {
                            $unavaiable++;
                        }
                    }
                }

                $data_recode[$current_time] = $unavaiable;
            }

            ksort( $data_recode );
            // random color
            $color = hb_random_color();
            $data->fillColor = $color;
            $data->strokeColor = $color;
            $data->pointColor = $color;
            $data->pointStrokeColor = "#fff";
            $data->pointHighlightFill = "#fff";
            $data->pointHighlightStroke = $color;

            $data->data = array_values( $data_recode );
            $series['datasets'][] = $data;
        }
        ksort( $label );
        $series['labels'] = array_values( $label );

        return $series;
    }

    // old
    public function parseData() {
        $results = $this->_query_results;
        $series = array();
        $ids = array();
        foreach ( $results as $key => $value ) {
            if ( !isset( $ids[$value->room_ID] ) )
                $ids[$value->room_ID] = $value->total;
        }

        foreach ( $ids as $id => $total ) {
            if ( !isset( $series[$id] ) ) {
                $prepare = array(
                    'name' => sprintf( __( '%s unavaiable', 'tp-hotel-booking-report' ), get_the_title( $id ) ),
                    'data' => array(),
                    'stack' => $id
                );

                if ( $this->chart_groupby === 'day' ) {
                    $unavaiable = array(
                        'name' => sprintf( __( '%s avaiable', 'tp-hotel-booking-report' ), get_the_title( $id ) ),
                        'data' => array(),
                        'stack' => $id
                    );
                } else {
                    $unavaiable = array(
                        'name' => sprintf( __( '%s quantity of room', 'tp-hotel-booking-report' ), get_the_title( $id ) ),
                        'data' => array(),
                        'stack' => $id
                    );
                }
            }

            $range = $this->_range_end - $this->_range_start;
            $cache = $this->_start_in;
            for ( $i = 0; $i <= $range; $i++ ) {
                $avaiable = 0;
                if ( $this->chart_groupby === 'day' ) {
                    $current_time = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
                } else {
                    $reg = $this->_range_start + $i;
                    $cache = date( "Y-$reg-01", strtotime( $cache ) );
                    $current_time = strtotime( date( "Y-$reg-01", strtotime( $cache ) ) );
                }

                foreach ( $results as $k => $v ) {

                    if ( (int) $v->room_ID !== (int) $id )
                        continue;

                    if ( $this->chart_groupby === 'day' ) {
                        $_in = strtotime( date( 'Y-m-d', $v->checkindate ) );
                        $_out = strtotime( date( 'Y-m-d', $v->checkoutdate ) );

                        if ( $current_time >= $_in && $current_time < $_out )
                            $avaiable++;
                    }
                    else {
                        $_in = strtotime( date( 'Y-m-1', $v->checkindate ) );
                        $_out = strtotime( date( 'Y-m-1', $v->checkoutdate ) );

                        if ( $current_time >= $_in && $current_time <= $_out )
                            $avaiable++;
                    }
                }

                $prepare['data'][] = array(
                    $current_time * 1000,
                    $avaiable
                );

                if ( $this->chart_groupby === 'day' ) {
                    $unavaiable['data'][] = array(
                        $current_time * 1000,
                        $total - $avaiable
                    );
                } else {
                    $unavaiable['data'][] = array(
                        $current_time * 1000,
                        (int) $total
                    );
                }
            }

            $series[] = $prepare;
            $series[] = $unavaiable;
        }

        return $series;
    }

    public function export_csv() {
        $this->_query_results = $this->getOrdersItems();
        if ( !isset( $_POST ) )
            return;

        if ( !isset( $_POST['tp-hotel-booking-report-export'] ) ||
                !wp_verify_nonce( sanitize_text_field( $_POST['tp-hotel-booking-report-export'] ), 'tp-hotel-booking-report-export' ) )
            return;

        if ( !isset( $_POST['tab'] ) || sanitize_file_name( $_POST['tab'] ) !== $this->_chart_type )
            return;

        $inputs = $this->parseData( $this->_query_results );

        if ( !$inputs )
            return;

        $rooms = array();
        foreach ( $inputs as $key => $input ) {
            if ( !isset( $rooms[$input['stack']] ) )
                $rooms[$input['stack']] = array();

            $rooms[$input['stack']][] = $input;
        }

        $filename = 'tp_hotel_export_' . $this->_chart_type . '_' . $this->_start_in . '_to_' . $this->_end_in . '.csv';
        header( 'Content-Type: application/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        // create a file pointer connected to the output stream
        $output = fopen( 'php://output', 'w' );

        foreach ( $rooms as $id => $params ) {

            // output the column headings
            fputcsv( $output, array( sprintf( '%s', get_the_title( $id ) ) ) );

            $column = array(
                __( 'Date/Time', 'tp-hotel-booking-report' )
            );

            $avaiable_data = false;
            $excerpt = array();
            if ( isset( $params[0] ) ) {
                $avaiables = $params[0];

                $avaiable_data = array(
                    __( 'Unavaiable', 'tp-hotel-booking-report' )
                );
                foreach ( $avaiables['data'] as $key => $avai ) {
                    if ( (int) $avai[1] === 0 ) {
                        $excerpt[] = $key;
                        continue;
                    }
                    if ( $this->chart_groupby === 'day' ) {
                        if ( isset( $avai[0], $avai[1] ) )
                            $time = $avai[0] / 1000;

                        $column[] = date( 'Y-m-d', $time );
                        $avaiable_data[] = $avai[1];
                    }
                    else {
                        if ( isset( $avai[0], $avai[1] ) )
                            $time = $avai[0] / 1000;

                        $column[] = date( 'F. Y', $time );
                        $avaiable_data[] = $avai[1];
                    }
                }
            }

            if ( $avaiable_data ) {
                // heading and avaiable
                fputcsv( $output, $column );
                fputcsv( $output, $avaiable_data );
            }

            if ( isset( $params[1] ) ) {
                $unavaiable = $params[1];

                if ( $this->chart_groupby === 'day' ) {
                    $unavaiable_data = array(
                        __( 'Avaiable', 'tp-hotel-booking-report' )
                    );
                } else {
                    $unavaiable_data = array(
                        __( 'Room Quantity', 'tp-hotel-booking-report' )
                    );
                }
                foreach ( $unavaiable['data'] as $key => $avai ) {
                    if ( in_array( $key, $excerpt ) )
                        continue;

                    if ( $this->chart_groupby === 'day' ) {
                        if ( isset( $avai[0], $avai[1] ) )
                            $time = $avai[0] / 1000;

                        $column[] = date( 'Y-m-d', $time );
                        $unavaiable_data[] = $avai[1];
                    }
                    else {
                        if ( isset( $avai[0], $avai[1] ) )
                            $time = $avai[0] / 1000;

                        $column[] = date( 'F. Y', $time );
                        $unavaiable_data[] = $avai[1];
                    }
                }
                fputcsv( $output, $unavaiable_data );
                fputcsv( $output, array() );
            }
        }

        fpassthru( $output );
        die();
    }

    static function instance( $range = null ) {
        if ( !$range && !isset( $_GET['range'] ) )
            $range = '7day';

        if ( !$range && isset( $_GET['range'] ) )
            $range = sanitize_text_field( $_GET['range'] );

        if ( !empty( self::$_instance[$range] ) )
            return self::$_instance[$range];

        return self::$_instance[$range] = new self( $range );
    }

}

if ( isset( $_REQUEST['tab'] ) && sanitize_text_field( $_REQUEST['tab'] ) === 'room' ) {
    $GLOBALS['hb_report'] = HB_Report_Room::instance();
}