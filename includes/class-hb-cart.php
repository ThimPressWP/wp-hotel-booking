<?php
/**
 * Class HB_Cart
 *
 * Simple Cart object for now. Maybe need to expand later
 */
class HB_Cart{
    /**
     * @var bool
     */
    private static $instance = false;

    /**
     * @var array
     */
    protected $_options = array();

    protected $_rooms = null;

    /**
     * Construction
     */
    function __construct(){
        if( !session_id() ) session_start();

        if( empty( $_SESSION['hb_cart'.HB_BLOG_ID] ) ){
            $_SESSION['hb_cart'.HB_BLOG_ID] = array(
                'cart_id'   => $this->generate_cart_id(),
                'options'   => array(),
                'products'  => array()
            );
        }
        if( HB_Settings::instance()->get( 'enable_coupon' ) ) {
            if ($coupon = get_transient('hb_user_coupon_' . session_id())) {
                HB_Coupon::instance($coupon);
            }
        }
        add_action( 'init', array($this, 'hotel_booking_cart_update') );
    }

    /**
     * Magic function for get a property
     *
     * @param string
     * @return mixed
     */
    function __get( $key ){
        $value = null;
        switch( $key ){
            case 'total_rooms':
                $value = $this->get_total_rooms();
                break;
            case 'total_nights':
                $value = $this->get_total_nights();
                break;
            case 'check_in_date':
            case 'check_out_date':
                $value = $this->get_option( $key );
                break;
            case 'sub_total':
                $value = $this->get_sub_total();
                break;
            case 'total':
                $value = $this->get_total();
                break;
            case 'advance_payment':
                $value = $this->get_advance_payment();
        }
        return $value;
    }

    public function needs_payment() {
        return apply_filters( 'hb_cart_needs_payment', $this->total > 0, $this );
    }

    /**
     * Set extra option for cart
     *
     * @param $name
     * @param null $value
     * @return $this
     */
    function set_option( $name, $value = null ){
        if( is_array( $name ) ){
            foreach( $name as $k => $v ){
                $this->set_option( $k, $v );
            }
        }else {
            $_SESSION['hb_cart'.HB_BLOG_ID]['options'][$name] = $value;
        }
        return $this;
    }

    /**
     * Get total rooms
     *
     * @return int
     */
    function get_total_rooms(){
        $total_rooms = 0;
        if( $rooms = $this->get_rooms() ){
            foreach( $rooms as $id => $room ){
                $total_rooms += (int)$room->get_data('quantity');
            }
        }
        return apply_filters( 'hb_cart_total_rooms', $total_rooms );
    }

    /**
     * Get total nights
     *
     * @return int
     */
    function get_total_nights(){
        $total_nights = 0;

        $rooms = $this->get_rooms();
        if( $rooms = $this->get_rooms() ){
            foreach( $rooms as $id => $room ){
                $total_nights += hb_count_nights_two_dates( $room->check_out_date, $room->check_in_date );
            }
        }
        return apply_filters( 'hb_cart_total_nights', $total_nights );
    }

    /**
     * Get extra option of cart
     *
     * @param $name
     * @return bool
     */
    function get_option( $name ){
        return ! empty(  $_SESSION['hb_cart'.HB_BLOG_ID]['options'][ $name ] ) ? $_SESSION['hb_cart'.HB_BLOG_ID]['options'][ $name ] : false;
    }

    /**
     * Get cart id
     *
     * @return mixed
     */
    function get_cart_id(){
        return $_SESSION['hb_cart'.HB_BLOG_ID]['cart_id'];
    }

    /**
     * Get rooms from cart
     *
     * @return mixed
     */
    function get_products(){
        if( isset($_SESSION['hb_cart'.HB_BLOG_ID]['products'], $_SESSION['hb_cart'.HB_BLOG_ID]['products']) )
            return $_SESSION['hb_cart'.HB_BLOG_ID]['products'];
        return null;
    }

    /**
     * Calculate sub total (without tax) and return
     *
     * @return mixed
     */
    function get_sub_total(){
        $sub_total = 0;

        if( $rooms = $this->get_rooms() )
        {
            foreach( $rooms as $room_id => $room ) {
                $sub_total += $room->get_total( $room->check_in_date, $room->check_out_date, $room->num_of_rooms, false );
            }
        }
        return apply_filters( 'hb_cart_sub_total', $sub_total );
    }

    /**
     * Calculate cart total (with tax) and return
     *
     * @return mixed
     */
    function get_total(){
        $sub_total  = $this->get_sub_total();

        $tax = hb_get_tax_settings();
        if( $tax > 0 ) {
            $grand_total = $sub_total + $sub_total * $tax;
        }else{
            $grand_total = $sub_total;
        }

        return apply_filters( 'learn_press_get_cart_total', $grand_total, $this->get_cart_id() );
    }

    /**
     * Get advance payment based on cart total
     *
     * @return float|int
     */
    function get_advance_payment(){
        $total = $this->get_total();
        if( $advance_payment = hb_get_advance_payment() ) {
            $total = $this->get_total() * $advance_payment / 100;
        }
        return $total;
    }

    /**
     * Get all HB_Room instance from cart
     *
     * @return array
     */
    function get_rooms( ){
        if( $this->_rooms )
            return $this->_rooms;

        if( $selected = $this->get_products() ){
            foreach( $selected as $in_to_out => $rooms ){
                foreach ($rooms as $id => $room) {
                    $this->_rooms[] = HB_Room::instance( $id, $room );
                }
            }
        }

        return $this->_rooms;
    }

    /**
     * Generate an unique cart id
     *
     * @return string
     */
    function generate_cart_id(){
        return md5( time() );
    }

    /**
     * Add a room to cart
     *
     * @param $room_id
     * @param int $quantity
     * @return $this
     */
    function add_to_cart( $room_id, $quantity = 1, $check_in_date,  $check_out_date ){
        $room = HB_Room::instance( $room_id );

        $date = strtotime($check_in_date) . '_' . strtotime($check_out_date);

        if ( ! isset( $_SESSION['hb_cart'.HB_BLOG_ID]['products'][$date] ) )
            $_SESSION['hb_cart'.HB_BLOG_ID]['products'][$date] = array();

        if ( ! isset( $_SESSION['hb_cart'.HB_BLOG_ID]['products'][$date][$room_id] ) )
        {
            $_SESSION['hb_cart'.HB_BLOG_ID]['products'][$date][$room_id] = array(
                    'id'            => $room_id,
                    'search_key'    => $date,
                    'quantity'      => $quantity,
                    'check_in_date' => $check_in_date,
                    'check_out_date'=> $check_out_date
                );
        }
        else
        {
            $_SESSION['hb_cart'.HB_BLOG_ID]['products'][$date][$room_id]['quantity'] = $quantity;
        }

        return $this;
    }

    /**
     * Clear all rooms from cart
     *
     * @return $this
     */
    function empty_cart(){
        unset( $_SESSION['hb_cart'.HB_BLOG_ID]['products'] );
        return $this;
    }

    /**
     * Destroy cart session
     */
    function destroy(){
        unset( $_SESSION['hb_cart'.HB_BLOG_ID] );
    }

    function update_order_status( $status ){

    }

    function is_empty(){
        return ! $this->get_rooms();
    }

    function hotel_booking_cart_update()
    {
        if( ! isset( $_POST ) )
            return;

        if( ! isset( $_POST['hotel_booking_cart'] ) )
            return;

        if( ! isset($_POST['hb_cart_field']) || ! wp_verify_nonce( $_POST['hb_cart_field'], 'hb_cart_field' ) )
            return;

        $cart_number = $_POST['hotel_booking_cart'];

        if( ! isset( $_SESSION['hb_cart'.HB_BLOG_ID]['products'] ) )
            return;

        $products = $_SESSION['hb_cart'.HB_BLOG_ID]['products'];

        foreach ($cart_number as $search_key => $rooms) {
            if( ! array_key_exists( $search_key, $products) )
                continue;

            foreach ($rooms as $id => $quantity) {
                if( ! isset( $products[$search_key][ $id ] )  )
                    continue;

                $quantity = (int) $quantity;

                if( $quantity === 0 )
                {
                    unset($_SESSION['hb_cart'.HB_BLOG_ID]['products'][$search_key][ $id ]);
                }
                else
                {
                    $_SESSION['hb_cart'.HB_BLOG_ID]['products'][$search_key][ $id ]['quantity'] = $quantity;
                }
            }
        }
        return;
        // var_dump($_SESSION['hb_cart'.HB_BLOG_ID]['products']); die();
    }

    /**
     * Get an instance of HB_Cart
     *
     * @param bool $prop
     * @param bool $args
     * @return bool|HB_Cart|mixed
     */
    static function instance( $prop = false, $args = false ){
        if( !self::$instance ){
            self::$instance = new self();
        }
        return self::$instance;
    }
}

if( !is_admin() ) {
    $GLOBALS['hb_cart'] = HB_Cart::instance();
}

/**
 * Get HB_Cart instance
 *
 * @param null $prop
 * @return bool|HB_Cart|mixed
 */
function hb_get_cart( $prop = null ){
    return HB_Cart::instance( $prop );
}

/**
 * Get cart total
 *
 * @param bool $pre_paid
 * @return float|int|mixed
 */
function hb_get_cart_total( $pre_paid = false ){
    $cart = HB_Cart::instance();
    if( $pre_paid ){
        $total = $cart->get_advance_payment();
    }else{
        $total = $cart->get_total();
    }
    return $total;
}

/**
 * Generate an unique string
 *
 * @return mixed
 */
function hb_uniqid(){
    $hash = str_replace( '.', '', microtime( true ) . uniqid() );
    return apply_filters( 'hb_generate_unique_hash', $hash );
}

/**
 * Get cart description
 *
 * @return string
 */
function hb_get_cart_description(){
    $cart = HB_Cart::instance();
    $description = array();
    foreach( $cart->get_rooms() as $room ){
        $description[] = sprintf( '%s (x %d)', $room->name, $room->quantity );
    }
    return join( ', ', $description );
}

/**
 * Get check out return URL
 *
 * @return mixed
 */
function hb_get_return_url(){
    $url = get_site_url();
    return apply_filters( 'hb_return_url', $url );
}

/**
 * Generate transaction object to store after the order placed
 *
 * @param $customer_id
 * @return stdClass
 */
// function hb_generate_transaction_object( $customer_id ){
function hb_generate_transaction_object( ){
    $cart = HB_Cart::instance();
    if( $cart->is_empty() ) return false;
    $rooms = array();
    if( $_rooms = $cart->get_rooms() ){
        foreach( $_rooms as $key => $room ) {
            $rooms[ $key ] = array(
                'id'                => $room->post->ID,
                'base_price'        => $room->get_price(),
                'quantity'          => $room->quantity,
                'name'              => $room->name,
                'check_in_date'     => $room->check_in_date,
                'check_out_date'    => $room->check_out_date,
                'sub_total'         => $room->get_total( $room->check_in_date, $room->check_out_date, $room->num_of_rooms, false )
            );
        }
    }

    if( ! $rooms )
        return;

    $transaction_object = new stdClass();
    $transaction_object->cart_id                = $cart->get_cart_id();
    $transaction_object->total                  = round( $cart->get_total(), 2 );
    $transaction_object->sub_total              = $cart->get_sub_total();
    $transaction_object->advance_payment        = hb_get_cart_total( ! hb_get_request( 'pay_all' ) );
    $transaction_object->currency               = hb_get_currency();
    $transaction_object->description            = hb_get_cart_description();
    $transaction_object->rooms                  = $rooms;
    $transaction_object->coupons                = '';
    $transaction_object->coupons_total_discount = '';
    $transaction_object->tax                    = hb_get_tax_settings();
    $transaction_object->price_including_tax    = hb_price_including_tax();
    $transaction_object->addition_information   = hb_get_request( 'addition_information' );
    $transaction_object->total_nights           = $cart->total_nights;

    if( HB_Settings::instance()->get( 'enable_coupon' ) && $coupon = get_transient( 'hb_user_coupon_' . session_id() ) ){
        $coupon = HB_Coupon::instance( $coupon );
        $transaction_object->coupon = array(
            'id'        => $coupon->ID,
            'code'      => $coupon->coupon_code,
            'value'     => $coupon->discount_value
        );
    }

    $transaction_object = apply_filters( 'hb_generate_transaction_object', $transaction_object );

    return $transaction_object;
}

/**
 * Update booking status
 *
 * @param int
 * @param string
 */
function hb_update_booking_status( $booking_id, $status ){
    $old_status = get_post_meta( $booking_id, '_hb_booking_status', true );

    if( strcasecmp( $old_status, $status ) != 0 ) {
        update_post_meta($booking_id, '_hb_booking_status', $status);
        if ($coupon = get_post_meta($booking_id, '_hb_coupon', true)) {
            $usage_count = get_post_meta($coupon['id'], '_hb_usage_count', true);
            if (strcasecmp($status, 'complete') == 0) {
                $usage_count++;
            } else {
                if ($usage_count > 0) {
                    $usage_count--;
                }else{
                    $usage_count = 0;
                }
            }
            update_post_meta( $coupon['id'], '_hb_usage_count', $usage_count );
        }
        do_action( 'hb_update_booking_status', $status, $old_status, $booking_id );
    }
}

/**
 * Set booking data to cache
 *
 * @param $method
 * @param $temp_id
 * @param $customer_id
 * @param $transaction
 */
function hb_set_transient_transaction( $method, $temp_id, $customer_id, $transaction ){
    // store booking info in a day
    set_transient( $method . '-' . $temp_id, array( 'customer_id' => $customer_id, 'transaction_object' => $transaction ), 60 * 60 * 24 );
}

/**
 * Get booking data from cache
 *
 * @param $method
 * @param $temp_id
 * @return mixed
 */
function hb_get_transient_transaction( $method, $temp_id ){
    return get_transient( $method . '-' . $temp_id );
}

/**
 * Delete booking data from cache
 *
 * @param $method
 * @param $temp_id
 * @return mixed
 */
function hb_delete_transient_transaction( $method, $temp_id ) {
    return delete_transient( $method . '-' . $temp_id );
}

/**
 * Creates new booking
 *
 * @param array $args
 * @return mixed|WP_Error
 */
function hb_create_booking( $args = array() ){
    $default_args = array(
        'status'        => '',
        'customer_id'   => null,
        'customer_note' => null,
        'booking_id'      => 0,
        'parent'        => 0
    );
    $args       = wp_parse_args( $args, $default_args );
    if( is_null( $args['customer_id'] && isset( $_SESSION['hb_cart'.HB_BLOG_ID]['customer_id'] ) ) ){
        $args['customer_id'] = absint( $_SESSION['hb_cart'.HB_BLOG_ID]['customer_id'] );
    }

    TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );

    $transaction_object = hb_generate_transaction_object(); // hb_generate_transaction_object( $args['customer_id'] );

    $tax                    = $transaction_object->tax;
    $price_including_tax    = $transaction_object->price_including_tax;
    $rooms                  = $transaction_object->rooms;

    $booking = HB_Booking::instance( $args['booking_id'] );
    $booking->post->post_title      = sprintf( __( 'Booking ', 'tp-hotel-booking' ) );
    $booking->post->post_content    = $transaction_object->addition_information;
    $booking->post->post_status     = 'hb-' . apply_filters( 'hb_default_order_status', 'pending' );

    if ( $args['status'] ) {
        if ( ! in_array( 'hb-' . $args['status'], array_keys( hb_get_booking_statuses() ) ) ) {
            return new WP_Error( 'hb_invalid_booking_status', __( 'Invalid booking status', 'tp-hotel-booking' ) );
        }
        $booking->post->post_status  = 'hb-' . $args['status'];
    }

    $booking_info = array(
        '_hb_booking_key'              => apply_filters( 'hb_generate_booking_key', uniqid( 'booking' ) )
    );
    if( ! empty( $transaction_object->coupon ) ){
        $booking_info['_hb_coupon'] = $transaction_object->coupon;
    }
    $booking->set_booking_info(
        $booking_info
    );

    $booking_id = $booking->update();

    return $booking_id;
}

/**
 * Gets all statuses that room supported
 *
 * @return array
 */
function hb_get_booking_statuses() {
    $booking_statuses = array(
        'hb-pending'    => _x( 'Pending Payment', 'Booking status', 'tp-hotel-booking' ),
        'hb-processing' => _x( 'Processing', 'Booking status', 'tp-hotel-booking' ),
        'hb-completed'  => _x( 'Completed', 'Booking status', 'tp-hotel-booking' ),
    );
    return apply_filters( 'hb_booking_statuses', $booking_statuses );
}

function hb_create_booking_2( $args = array() ){
    $default_args = array(
        'status'        => '',
        'customer_id'   => null,
        'customer_note' => null,
        'booking_id'      => 0,
        'created_via'   => '',
        'parent'        => 0
    );

    $args       = wp_parse_args( $args, $default_args );
    $booking_data = array();
    if ( $args['booking_id'] > 0 ) {
        $updating         = true;
        $booking_data['ID'] = $args['booking_id'];
    } else {
        $updating                    = false;
        $booking_data['post_type']     = 'hb_booking';
        $booking_data['post_status']   = 'hb-' . apply_filters( 'hb_default_order_status', 'pending' );
        $booking_data['ping_status']   = 'closed';
        $booking_data['post_author']   = 1;
        $booking_data['post_password'] = uniqid( 'booking' );
        $booking_data['post_title']    = sprintf( __( 'Booking &ndash; %s', 'tp-hotel-booking' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Booking date parsed by strftime', 'tp-hotel-booking' ) ) );
        $booking_data['post_parent']   = absint( $args['parent'] );
    }

    if ( $args['status'] ) {
        if ( ! in_array( 'hb-' . $args['status'], array_keys( hb_get_booking_statuses() ) ) ) {
            return new WP_Error( 'hb_invalid_booking_status', __( 'Invalid booking status', 'tp-hotel-booking' ) );
        }
        $booking_data['post_status']  = 'hb-' . $args['status'];
    }

    if ( ! is_null( $args['customer_note'] ) ) {
        $booking_data['post_excerpt'] = $args['customer_note'];
    }

    if ( $updating ) {
        $booking_id = wp_update_post( $booking_data );
    } else {
        $booking_id = wp_insert_post( apply_filters( 'hb_new_booking_data', $booking_data ), true );
    }
    if ( is_wp_error( $booking_id ) ) {
        return $booking_id;
    }

    if ( is_numeric( $args['customer_id'] ) ) {
        update_post_meta( $booking_id, '_customer_user', $args['customer_id'] );
    }

    return HB_Booking::instance( $booking_id );
}

/**
 * Get payment method title by slug
 *
 * @param $method_slug
 * @return mixed
 */
function hb_get_payment_method_title( $method_slug ){
    return apply_filters( 'hb_payment_method_title_' . $method_slug, __( 'N/A' ) );
}

/**
 * @param $date
 * @param bool $code
 * @return bool
 */
function hb_get_coupons_active( $date, $code = false ){
    $coupons = false;
    $enable = HB_Settings::instance()->get( 'enable_coupon' );
    if( $enable ) {
        $args = array(
            'post_type' => 'hb_coupon',
            'posts_per_page' => 999,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'value' => $date,
                    'key'   => '_hb_coupon_date_from',
                    'compare' => '<='
                ),
                array(
                    'value' => $date,
                    'key'   => '_hb_coupon_date_to',
                    'compare' => '>='
                )
            )
        );
        if( ( $coupons = get_posts( $args ) ) && $code ){
            $found = false;
            foreach( $coupons as $coupon ){
                if( strcmp( $coupon->post_title, $code ) == 0 ){
                    $coupons = $coupon;
                    $found = true;
                    break;
                }
            }
            if( ! $found ){
                $coupons = false;
            }
        }
    }
    return $coupons;
}