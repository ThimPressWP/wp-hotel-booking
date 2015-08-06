<?php

function hb_dropdown_room_capacities( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'echo'  => true
        )
    );
    ob_start();
    wp_dropdown_categories(
        array_merge( $args,
            array(
                'taxonomy'      => 'hb_room_capacity',
                'hide_empty'    => false,
                'name'          => 'hb-room-capacities'
            )
        )
    );

    $output = ob_get_clean();
    if( $args['echo'] ){
        echo $output;
    }
    return $output;
}

function hb_dropdown_room_types( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'echo'  => true
        )
    );
    ob_start();
    wp_dropdown_categories(
        array_merge( $args,
            array(
                'taxonomy'      => 'hb_room_type',
                'hide_empty'    => false,
                'name'          => 'hb-room-types',
                'echo'          => true
            )
        )
    );
    $output = ob_get_clean();

    if( $args['echo'] ){
        echo $output;
    }
    return $output;
}

function hb_get_room_types( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'taxonomy'      => 'hb_room_type',
            'hide_empty'    => 0,
            'orderby'       => 'term_group',
            'map_fields'    => null
        )
    );
    $terms = (array) get_terms( "hb_room_type", $args );
    if( is_array( $args['map_fields' ] ) ){
        $types = array();
        foreach( $terms as $term ){
            $type = new stdClass();
            foreach( $args['map_fields'] as $from => $to ){
                if( ! empty( $term->{$from} ) ){
                    $type->{$to} = $term->{$from};
                }else{
                    $type->{$to} = null;
                }
            }
            $types[] = $type;
        }
    }else{
        $types = $terms;
    }
    return $types;
}

function hb_get_room_capacities( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'taxonomy'      => 'hb_room_capacity',
            'hide_empty'    => 0,
            'orderby'       => 'term_group',
            'map_fields'    => null
        )
    );
    $terms = (array) get_terms( "hb_room_capacity", $args );
    if( is_array( $args['map_fields' ] ) ){
        $types = array();
        foreach( $terms as $term ){
            $type = new stdClass();
            foreach( $args['map_fields'] as $from => $to ){
                if( ! empty( $term->{$from} ) ){
                    $type->{$to} = $term->{$from};
                }else{
                    $type->{$to} = null;
                }
            }
            $types[] = $type;
        }
    }else{
        $types = $terms;
    }
    return $types;
}

function hb_get_child_per_room(){
    global $wpdb;
    $query = $wpdb->prepare("
        SELECT DISTINCT meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type=%s
          AND meta_key=%s
          AND meta_value <> 0
        ORDER BY meta_value ASC
    ", 'hb_room', '_hb_max_child_per_room' );
    return $wpdb->get_col( $query );
}

function hb_dropdown_child_per_room( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'name'      => '',
            'selected'  => ''
        )
    );
    $rows = hb_get_child_per_room();
    $output = '<select name="' . $args['name'] . '">';
    if( $rows ){
        foreach( $rows as $num ){
            $output .= sprintf( '<option value="%1$d"%2$s>%1$d</option>', $num, $args['selected'] == $num ? ' selected="selected"' : '' );
        }
    }
    $output .= '</select>';
    echo $output;
}
function hb_payment_currencies() {
    $currencies = array(
        'AED' => 'United Arab Emirates Dirham (د.إ)',
        'AUD' => 'Australian Dollars ($)',
        'BDT' => 'Bangladeshi Taka (৳&nbsp;)',
        'BRL' => 'Brazilian Real (R$)',
        'BGN' => 'Bulgarian Lev (лв.)',
        'CAD' => 'Canadian Dollars ($)',
        'CLP' => 'Chilean Peso ($)',
        'CNY' => 'Chinese Yuan (¥)',
        'COP' => 'Colombian Peso ($)',
        'CZK' => 'Czech Koruna (Kč)',
        'DKK' => 'Danish Krone (kr.)',
        'DOP' => 'Dominican Peso (RD$)',
        'EUR' => 'Euros (€)',
        'HKD' => 'Hong Kong Dollar ($)',
        'HRK' => 'Croatia kuna (Kn)',
        'HUF' => 'Hungarian Forint (Ft)',
        'ISK' => 'Icelandic krona (Kr.)',
        'IDR' => 'Indonesia Rupiah (Rp)',
        'INR' => 'Indian Rupee (Rs.)',
        'NPR' => 'Nepali Rupee (Rs.)',
        'ILS' => 'Israeli Shekel (₪)',
        'JPY' => 'Japanese Yen (¥)',
        'KIP' => 'Lao Kip (₭)',
        'KRW' => 'South Korean Won (₩)',
        'MYR' => 'Malaysian Ringgits (RM)',
        'MXN' => 'Mexican Peso ($)',
        'NGN' => 'Nigerian Naira (₦)',
        'NOK' => 'Norwegian Krone (kr)',
        'NZD' => 'New Zealand Dollar ($)',
        'PYG' => 'Paraguayan Guaraní (₲)',
        'PHP' => 'Philippine Pesos (₱)',
        'PLN' => 'Polish Zloty (zł)',
        'GBP' => 'Pounds Sterling (£)',
        'RON' => 'Romanian Leu (lei)',
        'RUB' => 'Russian Ruble (руб.)',
        'SGD' => 'Singapore Dollar ($)',
        'ZAR' => 'South African rand (R)',
        'SEK' => 'Swedish Krona (kr)',
        'CHF' => 'Swiss Franc (CHF)',
        'TWD' => 'Taiwan New Dollars (NT$)',
        'THB' => 'Thai Baht (฿)',
        'TRY' => 'Turkish Lira (₺)',
        'USD' => 'US Dollars ($)',
        'VND' => 'Vietnamese Dong (₫)',
        'EGP' => 'Egyptian Pound (EGP)'
    );

    return apply_filters( 'hb_payment_currencies', $currencies );
}

/**
 * Checks to see if is enable overwrite templates from theme
 *
 * @return bool
 */
function hb_enable_overwrite_template(){
    return HB_Settings::instance()->get( 'overwrite_templates' ) == 'on';
}

function hb_get_request( $name, $default = null, $var = '' ){
    $return = $default;
    switch( strtolower( $var ) ){
        case 'post': $var = $_POST; break;
        case 'get': $var = $_GET; break;
        default: $var = $_REQUEST;
    }
    if( ! empty( $var[ $name ] ) ){
        $return = $var[ $name ];
    }
    return $return;
}

function hb_count_nights_two_dates( $end = null, $start ){
    if( ! $end ) $end = time();
    else if( is_string( $end ) ){
        $end = @strtotime( $end );
    }
    if( is_string( $start ) ){
        $start = strtotime( $start );
    }
    $datediff = $end - $start;
    return floor( $datediff / ( 60 * 60 * 24 ) );
}

function hb_date_to_name( $date ){
    $date_names = array(
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
    );
    return $date_names[ $date ];
}

function hb_dropdown_titles( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'name'              => 'title',
            'selected'          => '',
            'show_option_none'  => __( '--Select--', 'tp-hotel-booking' ),
            'option_none_value' => -1,
            'echo'              => true
        )
    );
    $name = '';
    $selected = '';
    $echo = false;
    $show_option_none = false;
    $option_none_value = -1;
    extract( $args );
    $titles = apply_filters( 'hb_customer_titles', array(
            'mr'    => __( 'Mr.', 'tp-hotel-booking' ),
            'ms'    => __( 'Ms.', 'tp-hotel-booking' ),
            'mrs'   => __( 'Mrs.', 'tp-hotel-booking' ),
            'miss'  => __( 'Miss.', 'tp-hotel-booking' ),
            'dr'    => __( 'Dr.', 'tp-hotel-booking' ),
            'Prof'  => __( 'Prof.', 'tp-hotel-booking' )
        )
    );
    $output = '<select name="' . $name . '">';
    if( $show_option_none ){
        $output .= sprintf( '<option value="%s">%s</option>', $option_none_value, $show_option_none );
    }
    if( $titles ) foreach( $titles as $slug => $title ){
        $output .= sprintf( '<option value="%s"%s>%s</option>', $slug, $slug == $selected ? ' selected="selected"' : '', $title );
    }
    $output .= '</select>';
    if( $echo ){
        echo $output;
    }
    return $output;
}

function hb_create_empty_post(){
    $posts = get_posts(
        array(
            'post_type'         => 'any',
            'posts_per_page'    => 1
        )
    );

    if( $posts ){
        foreach( get_object_vars( $posts[0] ) as $key => $value ){
            $posts[0]->{$key} = null;
        }
        return $posts[0];
    }
    return new stdClass();
}

function hb_l18n(){
    $translation = array(
        'invalid_email' => __( 'Your email address is invalid', 'tp-hotel-booking' )
    );
    return apply_filters( 'hb_l18n', $translation );
}

function hb_get_tax_settings(){
    $settings = HB_Settings::instance();
    if( $tax = $settings->get('tax') ){
        $tax = $tax / 100;
    }
    if( hb_price_including_tax() ){
        $tax = -$tax;
    }
    return $tax;
}

function hb_price_including_tax(){
    $settings = HB_Settings::instance();
    return $settings->get('price_including_tax');
}

function hb_dropdown_numbers( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'min'       => 0,
            'max'       => 100,
            'selected'  => 0,
            'name'      => '',
            'id'        => '',
            'class'     => '',
            'echo'      => true,
            'show_option_none'  => '',
            'option_none_value' => ''
        )
    );
    $min = 0;
    $max = 100;
    $selected = 0;
    $name = '';
    $id = '';
    $class = '';
    $echo = true;
    $show_option_none = false;
    $option_none_value = '';

    extract( $args );

    $id = empty( $id ) ? sanitize_title( $name ) : $id;
    $output = '<select name="' . $name . '" id="' . $id . '"' . ( $class ? "" : ' class="' . $class . '"' ) . '>';
    if( $show_option_none ) {
        $output .= '<option value="' . $option_none_value . '">' . $show_option_none . '</option>';
    }
    for( $i = $min; $i <= $max; $i++ ){
        $output .= sprintf( '<option value="%1$d"%2$s>%1$d</option>', $i, $selected == $i ? ' selected="selected"' : '' );
    }
    $output .= '</select>';
    if( $echo ){
        echo $output;
    }
    return $output;
}
function hb_customer_place_order(){

    if( strtolower( $_SERVER['REQUEST_METHOD'] ) != 'post' ){
        return;
    }

    if ( ! isset( $_POST['hb_customer_place_order_field'] ) || ! wp_verify_nonce( $_POST['hb_customer_place_order_field'], 'hb_customer_place_order' ) ){
        return;
    }


    TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );
    $check_in = hb_get_request('check_in_date');
    $check_out = hb_get_request('check_out_date');
    $settings = HB_Settings::instance();
    $tax = hb_get_tax_settings();
    $price_including_tax = hb_price_including_tax();
    $rooms = hb_get_request( 'num_of_rooms' );
    $total = 0;
    foreach( $rooms as $id => $num_of_rooms ){
        $room = HB_Room::instance( $id );
        $total += $room->get_total( $check_in, $check_out, $num_of_rooms, false );
    }
    if( ! $price_including_tax ){
        $grand_total = $total + $total * $tax;
    }else{
        $grand_total = $total;
    }
    $request = maybe_unserialize( base64_decode( $_POST['sig'] ) );

    $booking = HB_Booking::instance( 0 );
    $booking->post->post_title = sprintf( __( 'Booking from %s to %s', 'tp-hotel-booking' ), $check_in, $check_out );
    $booking->post->post_content = hb_get_request( 'addition_information' );
    $booking->post->post_status = 'pending';

    $booking->set_customer(
        'data',
        array(
            '_hb_title'         => hb_get_request( 'title' ),
            '_hb_first_name'    => hb_get_request( 'first_name' ),
            '_hb_last_name'     => hb_get_request( 'last_name' ),
            '_hb_address'       => hb_get_request( 'address' ),
            '_hb_city'          => hb_get_request( 'city' ),
            '_hb_state'         => hb_get_request( 'state' ),
            '_hb_postal_code'   => hb_get_request( 'postal_code' ),
            '_hb_country'       => hb_get_request( 'country' ),
            '_hb_phone'         => hb_get_request( 'phone' ),
            '_hb_email'         => hb_get_request( 'email' ),
            '_hb_fax'           => hb_get_request( 'fax' )
        )
    );
    $booking_info = array(
        '_hb_check_in_date'     => strtotime( $request['check_in_date'] ),
        '_hb_check_out_date'    => strtotime( $request['check_out_date'] ),
        '_hb_total_nights'      => $request['total_nights'],
        '_hb_tax'               => $settings->get('tax'),
        '_hb_price_including_tax' => $price_including_tax ? 1 : 0,
        '_sub_total'                => $total,
        '_total'                    => $grand_total
    );

    $booking->set_booking_info(
        $booking_info
    );

    $booking_id = $booking->update();
    if( $booking_id ){
        $booking_rooms = hb_get_request( 'num_of_rooms' );
        foreach( $booking_rooms as $room_id => $num_of_rooms ){
            // insert multiple meta value
            for( $i = 0; $i < $num_of_rooms; $i ++ ) {
                add_post_meta($booking_id, '_hb_room_id', $room_id);
            }
        }
        //update_post_meta( $booking_id, '_hb_rooms', $booking_rooms );
    }

    return $booking_id;
}
add_action( 'init', 'hb_customer_place_order' );

function hb_search_rooms( $args = array() ){
    global $wpdb;

    $tax_id = hb_get_request( 'hb-room-capacities' );
    $tax = get_term( $tax_id, 'hb_room_capacity' );
    if( ! is_wp_error( $tax ) ){
        $adults = get_option( 'hb_taxonomy_capacity_' . $tax_id );
    }else{
        $adults = -1;
    }

    $args = wp_parse_args(
        $args,
        array(
            'check_in_date'     => date( 'm/d/Y' ),
            'check_out_date'    => date( 'm/d/Y' ),
            'adults'            => $adults,
            'max_child'         => 0
        )
    );

    $check_in_time = strtotime( $args['check_in_date'] );
    $check_out_time = strtotime( $args['check_out_date'] );
    $check_in_date_to_time = mktime( 0, 0, 0, date( 'm', $check_in_time ), date( 'd', $check_in_time ), date( 'Y', $check_in_time ) );
    $check_out_date_to_time = mktime( 0, 0, 0, date( 'm', $check_out_time ), date( 'd', $check_out_time ), date( 'Y', $check_out_time ) );

    $results = array();

    /**
     * Count available rooms
     */
    $query_count_available = $wpdb->prepare("
        (
            SELECT ra.meta_value
            FROM {$wpdb->postmeta} ra
            INNER JOIN {$wpdb->posts} r ON ra.post_id = r.ID AND ra.meta_key = %s
                WHERE r.ID=rooms.ID
        )
    ", '_hb_num_of_rooms');

    /**
     * Count booked rooms
     */
    $query_count_not_available = $wpdb->prepare("
        (
            SELECT count(booking.ID)
            FROM {$wpdb->posts} booking
            INNER JOIN {$wpdb->postmeta} bm ON bm.post_id = booking.ID AND bm.meta_key = %s
            INNER JOIN {$wpdb->postmeta} bi ON bi.post_id = booking.ID AND bi.meta_key = %s
            INNER JOIN {$wpdb->postmeta} bo ON bo.post_id = booking.ID AND bo.meta_key = %s
            WHERE
                bm.meta_value=rooms.ID
                AND bi.meta_value >= %d
                AND bo.meta_value <= %d
        )
    ", '_hb_room_id', '_hb_check_in_date', '_hb_check_out_date', $check_in_date_to_time, $check_out_date_to_time );

    /**
     *
     */
    $query = $wpdb->prepare("
        SELECT rooms.*, {$query_count_available} - {$query_count_not_available} as available_rooms
        FROM {$wpdb->posts} rooms
        INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = rooms.ID AND pm.meta_key = %s
        INNER JOIN {$wpdb->postmeta} pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
        WHERE
          rooms.post_type = %s
          AND rooms.post_status = %s
          AND pm.meta_value >= %d
          AND pm2.meta_value >= %d
        HAVING available_rooms > 0
    ", '_hb_max_child_per_room', '_hb_max_adults_per_room', 'hb_room', 'publish', hb_get_request('max_child'), $adults );

    /*
    echo '<pre>';
    echo date('d.m.Y h:i:s', 1438992000);
    print_r($_REQUEST);
    echo $query;
*/
    if( $search = $wpdb->get_results( $query ) ){
        foreach( $search as $k => $p ){
            $results[ $k ] = HB_Room::instance( $p );
        }
    }
    //print_r($search);
    /**echo '</pre>';*/
    return $results;
}