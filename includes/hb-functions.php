<?php
/**
 * List room capacities into dropdown select
 *
 * @param array
 * @return string
 */
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

/**
 * List room types into dropdown select
 *
 * @param array $args
 * @return string
 */
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
                'orderby'       => 'term_group',
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

/**
 * Get room types taxonomy
 *
 * @param array $args
 * @return array
 */
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

/**
 * Get room capacities taxonomy
 *
 * @param array $args
 * @return array
 */
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

/**
 * Get list of child per each room with all available rooms
 *
 * @return mixed
 */
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

/**
 * List child of room into dropdown select
 *
 * @param array $args
 */
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

/**
 * Get capacity of a room type
 *
 * @param $type_id
 * @return int
 */
function hb_get_room_type_capacities( $type_id ){
    return intval( get_option( "hb_taxonomy_capacity_{$type_id}" ) );
}

/**
 * Parse a param from request has encoded
 */
function hb_parse_request(){
    $params = hb_get_request( 'hotel-booking-params' );
    if( $params ){
        $params = maybe_unserialize( base64_decode( $params ) );
        if( $params ){
            foreach( $params as $k => $v ){
                $_GET[ $k ] = $v;
                $_POST[ $k ] = $v;
                $_REQUEST[ $k ] = $v;
            }
        }
        if( isset( $_GET['hotel-booking-params'] ) ) unset( $_GET['hotel-booking-params'] );
        if( isset( $_POST['hotel-booking-params'] ) ) unset( $_POST['hotel-booking-params'] );
        if( isset( $_REQUEST['hotel-booking-params'] ) ) unset( $_REQUEST['hotel-booking-params'] );
    }
}
add_action( 'init', 'hb_parse_request' );

/**
 * Get the list of common currencies
 *
 * @return mixed
 */
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

/**
 * Get a variable from request
 *
 * @param string
 * @param mixed
 * @param mixed
 * @return mixed
 */
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

/**
 * Calculate the nights between to dates
 *
 * @param null $end
 * @param $start
 * @return float
 */
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

function hb_date_names(){
    $date_names = array(
        __( 'Sun', 'tp-hotel-booking' ),
        __( 'Mon', 'tp-hotel-booking' ),
        __( 'Tue', 'tp-hotel-booking' ),
        __( 'Web', 'tp-hotel-booking' ),
        __( 'Thu', 'tp-hotel-booking' ),
        __( 'Fri', 'tp-hotel-booking' ),
        __( 'Sat', 'tp-hotel-booking' )
    );
    return apply_filters( 'hb_date_names', $date_names );
}

function hb_date_to_name( $date ){
    $date_names = hb_date_names();
    return $date_names[ $date ];
}

function hb_get_common_titles(){
    return apply_filters( 'hb_customer_titles', array(
            'mr'    => __( 'Mr.', 'tp-hotel-booking' ),
            'ms'    => __( 'Ms.', 'tp-hotel-booking' ),
            'mrs'   => __( 'Mrs.', 'tp-hotel-booking' ),
            'miss'  => __( 'Miss.', 'tp-hotel-booking' ),
            'dr'    => __( 'Dr.', 'tp-hotel-booking' ),
            'prof'  => __( 'Prof.', 'tp-hotel-booking' )
        )
    );
}

function hb_get_title_by_slug( $slug ){
    $titles = hb_get_common_titles();
    return ! empty( $titles[ $slug ] ) ? $titles[ $slug ] : '';
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
    $titles = hb_get_common_titles();
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

/**
 * Create an empty object with all fields as a WP_Post object
 *
 * @return stdClass
 */
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

/**
 * Localize script for front-end
 *
 * @return mixed
 */
function hb_l18n(){
    $translation = array(
        'invalid_email'                 => __( 'Your email address is invalid', 'tp-hotel-booking' ),
        'no_payment_method_selected'    => __( 'Please select your payment method ', 'tp-hotel-booking' ),
        'confirm_tos'                   => __( 'Please accept our Terms and Conditions ', 'tp-hotel-booking' ),
        'no_rooms_selected'             => __( 'Please select at least one the room', 'tp-hotel-booking' ),
        'empty_customer_title'          => __( 'Please select your title', 'tp-hotel-booking' ),
        'empty_customer_first_name'     => __( 'Please enter your first name', 'tp-hotel-booking'),
        'empty_customer_last_name'      => __( 'Please enter your last name', 'tp-hotel-booking' ),

        'empty_customer_address'        => __( 'Please enter your address', 'tp-hotel-booking' ),
        'empty_customer_city'           => __( 'Please enter your city name', 'tp-hotel-booking' ),
        'empty_customer_state'          => __( 'Please enter your state', 'tp-hotel-booking' ),
        'empty_customer_postal_code'    => __( 'Please enter your postal code', 'tp-hotel-booking' ),
        'empty_customer_country'        => __( 'Please select your country', 'tp-hotel-booking' ),
        'empty_customer_phone'          => __( 'Please enter your phone number', 'tp-hotel-booking' ),
        'customer_email_invalid'        => __( 'Your email is invalid', 'tp-hotel-booking' ),
        'customer_email_not_match'      => __( 'Your email does not match with existing email! Ok to create a new customer information', 'tp-hotel-booking' ),

        'empty_check_in_date'           => __( 'Please select check in date', 'tp-hotel-booking' ),
        'empty_check_out_date'          => __( 'Please select check out date', 'tp-hotel-booking' ),
        'check_in_date_must_be_greater' => __( 'Check in date must be greater than the current', 'tp-hotel-booking' ),
        'check_out_date_must_be_greater'    => __( 'Check out date must be greater than the check in', 'tp-hotel-booking' ),
    );
    return apply_filters( 'hb_l18n', $translation );
}

/**
 * Get tax setting
 *
 * @return float|mixed
 */
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

/**
 * @param $data
 */
function hb_send_json( $data ){
    echo '<!-- HB_AJAX_START -->';
    @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
    echo wp_json_encode( $data );
    echo '<!-- HB_AJAX_END -->';
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        wp_die();
    else
        die;
}

function hb_is_ajax(){
    return defined( 'DOING_AJAX' ) && DOING_AJAX;
}

function hb_update_customer_info( $data ){
    $is_new = false;
    if( ! empty( $data['ID'] ) ){
        $customer_id = $data['ID'];
        $customer = get_post( $customer_id );
        if( ! $customer || get_post_meta( $customer_id, '_hb_email', true ) != $data['email'] ){
            $customer_id = 0;
        }
    }
    if( ! $customer_id ){
        $is_new = true;
        $customer_id = wp_insert_post(
            array(
                'post_type'     => 'hb_customer',
                'post_status'   => 'publish',
                'post_title'    => __( 'Hotel Booking Customer', 'tp-hotel-booking' )
            )
        );
    }
    if( $customer_id ) {
        foreach ($data as $k => $v) {
            if( $k == 'ID' ) continue;
            if( ! $is_new && $k == 'email' ) continue;
            update_post_meta($customer_id, "_hb_{$k}", $v);
        }
    }
    return $customer_id;
}

function hb_get_customer( $customer_id ){
    if( is_string( $customer_id ) ){
        global $wpdb;
        $query = $wpdb->prepare("
            SELECT post_id
            FROM {$wpdb->postmeta}
            WHERE meta_key = %s
            AND meta_value = %s
        ", '_hb_email', $customer_id );
        $customer_id = $wpdb->get_var( $query );
    }
    $customer = get_post( $customer_id );
    if( $customer && $customer->post_type == 'hb_customer' ){
        $customer->data = array();
        $data = get_post_meta( $customer->ID );
        foreach( $data as $k => $v ) {
            $key_name = preg_replace( '!^_hb_!', '', $k );
            $customer->data[ $key_name ] = $v[0];
        }
    }else{
        $customer = null;
    }
    return $customer;
}

/**
 * Place order for a booking
 *
 * @throws Exception
 */
function hb_customer_place_order(){

    if( strtolower( $_SERVER['REQUEST_METHOD'] ) != 'post' ){
        return;
    }

    if ( ! isset( $_POST['hb_customer_place_order_field'] ) || ! wp_verify_nonce( $_POST['hb_customer_place_order_field'], 'hb_customer_place_order' ) ){
        return;
    }

    $payment_method = hb_get_user_payment_method( hb_get_request( 'hb-payment-method' ) );

    if( ! $payment_method ){
        throw new Exception( __( 'The payment method is not available', 'tp-hotel-booking' ) );
    }

    $customer_info = array(
        'ID'            => hb_get_request( 'existing-customer-id' ),
        'title'         => hb_get_request( 'title' ),
        'first_name'    => hb_get_request( 'first_name' ),
        'last_name'     => hb_get_request( 'last_name' ),
        'address'       => hb_get_request( 'address' ),
        'city'          => hb_get_request( 'city' ),
        'state'         => hb_get_request( 'state' ),
        'postal_code'   => hb_get_request( 'postal_code' ),
        'country'       => hb_get_request( 'country' ),
        'phone'         => hb_get_request( 'phone' ),
        'email'         => hb_get_request( 'email' ),
        'fax'           => hb_get_request( 'fax' ),
    );
    $customer_id = hb_update_customer_info( $customer_info );
    if( $customer_id ) {
        $result = $payment_method->process_checkout( $customer_id );
    }

    if ( ! empty( $result['result'] ) && $result['result'] == 'success' ) {

        $result = apply_filters( 'hb_payment_successful_result', $result );

        if ( hb_is_ajax() ) {
            hb_send_json( $result );
            exit;
        } else {
            wp_redirect( $result['redirect'] );
            exit;
        }

    }
    exit();
}

function hb_get_current_user(){
    return wp_get_current_user();
}

//add_action( 'init', 'hb_customer_place_order' );

function hb_get_currency() {
    $currencies     = hb_payment_currencies();
    $currency_codes = array_keys( $currencies );
    $currency       = reset( $currency_codes );

    return apply_filters( 'hb_currency', HB_Settings::instance()->get( 'currency', $currency ) );
}

function hb_get_currency_symbol( $currency = '' ) {
    if ( ! $currency ) {
        $currency = hb_get_currency();
    }

    switch ( $currency ) {
        case 'AED' :
            $currency_symbol = 'د.إ';
            break;
        case 'AUD' :
        case 'CAD' :
        case 'CLP' :
        case 'COP' :
        case 'HKD' :
        case 'MXN' :
        case 'NZD' :
        case 'SGD' :
        case 'USD' :
            $currency_symbol = '&#36;';
            break;
        case 'BDT':
            $currency_symbol = '&#2547;&nbsp;';
            break;
        case 'BGN' :
            $currency_symbol = '&#1083;&#1074;.';
            break;
        case 'BRL' :
            $currency_symbol = '&#82;&#36;';
            break;
        case 'CHF' :
            $currency_symbol = '&#67;&#72;&#70;';
            break;
        case 'CNY' :
        case 'JPY' :
        case 'RMB' :
            $currency_symbol = '&yen;';
            break;
        case 'CZK' :
            $currency_symbol = '&#75;&#269;';
            break;
        case 'DKK' :
            $currency_symbol = 'kr.';
            break;
        case 'DOP' :
            $currency_symbol = 'RD&#36;';
            break;
        case 'EGP' :
            $currency_symbol = 'EGP';
            break;
        case 'EUR' :
            $currency_symbol = '&euro;';
            break;
        case 'GBP' :
            $currency_symbol = '&pound;';
            break;
        case 'HRK' :
            $currency_symbol = 'Kn';
            break;
        case 'HUF' :
            $currency_symbol = '&#70;&#116;';
            break;
        case 'IDR' :
            $currency_symbol = 'Rp';
            break;
        case 'ILS' :
            $currency_symbol = '&#8362;';
            break;
        case 'INR' :
            $currency_symbol = 'Rs.';
            break;
        case 'ISK' :
            $currency_symbol = 'Kr.';
            break;
        case 'KIP' :
            $currency_symbol = '&#8365;';
            break;
        case 'KRW' :
            $currency_symbol = '&#8361;';
            break;
        case 'MYR' :
            $currency_symbol = '&#82;&#77;';
            break;
        case 'NGN' :
            $currency_symbol = '&#8358;';
            break;
        case 'NOK' :
            $currency_symbol = '&#107;&#114;';
            break;
        case 'NPR' :
            $currency_symbol = 'Rs.';
            break;
        case 'PHP' :
            $currency_symbol = '&#8369;';
            break;
        case 'PLN' :
            $currency_symbol = '&#122;&#322;';
            break;
        case 'PYG' :
            $currency_symbol = '&#8370;';
            break;
        case 'RON' :
            $currency_symbol = 'lei';
            break;
        case 'RUB' :
            $currency_symbol = '&#1088;&#1091;&#1073;.';
            break;
        case 'SEK' :
            $currency_symbol = '&#107;&#114;';
            break;
        case 'THB' :
            $currency_symbol = '&#3647;';
            break;
        case 'TRY' :
            $currency_symbol = '&#8378;';
            break;
        case 'TWD' :
            $currency_symbol = '&#78;&#84;&#36;';
            break;
        case 'UAH' :
            $currency_symbol = '&#8372;';
            break;
        case 'VND' :
            $currency_symbol = '&#8363;';
            break;
        case 'ZAR' :
            $currency_symbol = '&#82;';
            break;
        default :
            $currency_symbol = $currency;
            break;
    }

    return apply_filters( 'hb_currency_symbol', $currency_symbol, $currency );
}
function hb_format_price( $price, $with_currency = true ){
    $settings = HB_Settings::instance();
    $position = $settings->get( 'price_currency_position' );
    $price_thousands_separator = $settings->get( 'price_thousands_separator' );
    $price_decimals_separator = $settings->get( 'price_decimals_separator' );
    $price_number_of_decimal = $settings->get( 'price_number_of_decimal' );
    if ( ! is_numeric( $price ) )
        $price = 0;
    $before   = $after = '';
    if ( $with_currency ) {
        if ( gettype( $with_currency ) != 'string' ) {
            $currency = hb_get_currency_symbol();
        } else {
            $currency = $with_currency;
        }

        switch ( $position ) {
            default:
                $before = $currency;
                break;
            case 'left_with_space':
                $before = $currency . ' ';
                break;
            case 'right':
                $after = $currency;
                break;
            case 'right_with_space':
                $after = ' ' . $currency;
        }
    }

    $price =
        $before
        . number_format(
            $price,
            $price_number_of_decimal,
            $price_decimals_separator,
            $price_thousands_separator
        ) . $after;

    return $price;
}

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
            $room = HB_Room::instance( $p );
            $room->set_data(
                array(
                    'check_in_date' => $args['check_in_date'],
                    'check_out_date' => $args['check_out_date']
                )
            )->get_booking_room_details();
            $results[ $k ] = $room;
        }
    }
    //print_r($search);
    /**echo '</pre>';*/
    return $results;
}

function hb_get_payment_gateways( $args = array() ){
    static $payment_gateways = array();
    if( ! $payment_gateways ) {
        $defaults = array(
            'paypal' => new HB_Payment_Gateway_Paypal(),
            'offline-payment' => new HB_Payment_Gateway_Offline_Payment()
        );
        $payment_gateways = apply_filters('hb_payment_gateways', $defaults);
    }
    $args = wp_parse_args(
        $args,
        array(
            'enable' => false
        )
    );
    if( $args['enable'] ){
        $gateways = array();
        foreach( $payment_gateways as $k => $gateway ){
            if( $gateway->is_enable() ){
                $gateways[ $k ] = $gateway;
            }
        }
    }else{
        $gateways = $payment_gateways;
    }
    return $gateways;
}

function hb_get_user_payment_method( $slug ){
    $methods = hb_get_payment_gateways( array( 'enable' => true ) );
    $method = false;
    if( $methods && ! empty( $methods[ $slug ] ) ){
        $method = $methods[ $slug ];
    }
    return $method;
}

function hb_get_page_id( $name ){
    $settings = hb_settings();
    return $settings->get( "{$name}_page_id" );
}

function hb_get_page_permalink( $name ){
    return get_the_permalink( hb_get_page_id( $name ) );
}

function hb_get_advance_payment(){
    $advance_payment = HB_Settings::instance()->get( 'advance_payment' );
    return apply_filters( 'hb_advance_payment', $advance_payment );
}

function hb_do_transaction( $method, $transaction = false ){
    do_action( 'hb_do_transaction_' . $method, $transaction );
}

/**
 * Process purchase request
 */
function hb_handle_purchase_request(){
    hb_get_payment_gateways();
    $method_var = 'hb-transaction-method';
    $requested_transaction_method = empty( $_REQUEST[$method_var] ) ? false : $_REQUEST[$method_var];
    hb_do_transaction( $requested_transaction_method );
}

function hb_get_bookings( $args = array() ){
    $defaults = array(
        'post_type' => 'hb_booking',
    );
    $args = wp_parse_args( $args, $defaults );
    $bookings = get_posts( $args );
    return apply_filters( 'hb_get_bookings', $bookings, $args );
}

/**
 * Update booking status
 *
 * @param int
 * @param string
 */
function hb_update_booking_status( $booking_id, $status ){
    update_post_meta( $booking_id, '_hb_booking_status', $status );
}

/**
 *
 */
function hb_maybe_modify_page_content(){
    global $post;
    if( is_page() && $post->ID == hb_get_page_id( 'search' ) ){
        $post->post_content = '[hotel_booking]';
    }
}
add_action( 'template_redirect', 'hb_maybe_modify_page_content' );

/**
 * Init some task when wp init
 */
function hb_init(){
    hb_get_payment_gateways();
}
add_action( 'init', 'hb_init' );

function hb_format_order_number( $order_number ) {
    return '#' . sprintf( "%'.010d", $order_number );
}