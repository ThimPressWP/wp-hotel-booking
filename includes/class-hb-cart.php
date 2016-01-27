<?php

class HB_Cart
{

	/**
     * @var bool
     */
    private static $instance = null;

    /**
     * $sessions object
     * @var null
     */
    private $sessions = null;

    /**
     * $customer_sessions object
     * @var null
     */
    private $customer_sessions = null;

    /**
     * $customer_sessions object
     * @var null
     */
    private $booking_sessions = null;

    // load cart contents
    public $cart_contents = array();
	public $cart_total_include_tax = 0;
	public $cart_total 			= 0;
	public $cart_total_exclude_tax = 0;
	public $cart_items_count 	= 0;

    // customer
    public $customer_id = null;

    // customer
    public $customer_email = null;

    // coupon
    public $coupon = null;

    // booking id
    public $booking_id = null;

    function __construct()
    {
    	// session class
    	$this->sessions = HB_Sessions::instance( 'thimpress_hotel_booking_' . HB_BLOG_ID , true );

        // session customer object
        $this->customer_sessions = HB_Sessions::instance( 'thimpress_hotel_booking_customer_' . HB_BLOG_ID , true );

        // session booking object
        $this->booking_sessions = HB_Sessions::instance( 'thimpress_hotel_booking_info_' . HB_BLOG_ID , true );

        // refresh cart session
    	$this->refresh();

        // update init hook
        add_action( 'init', array($this, 'hotel_booking_cart_update') );
    }

    function __get( $key )
    {
    	switch ( $key ) {
    		case 'cart_contents':
    			$return = $this->get_cart_contents();
    			break;

    		case 'cart_total_include_tax':
    			$return = $this->cart_total_include_tax();
    			break;

    		case 'cart_total_exclude_tax':
    			$return = $this->cart_total_exclude_tax();
    			break;

    		case 'cart_items_count':
    			$return = count( $this->get_cart_contents() );
    			break;

            // old
            case 'total_rooms':
                $return = $this->get_total_rooms();
                break;
            case 'total_nights':
                $return = $this->get_total_nights();
                break;
            // case 'check_in_date':
            // case 'check_out_date':
            //     $return = $this->get_option( $key );
            //     break;
            case 'sub_total':
                $return = $this->get_sub_total();
                break;
            case 'total':
                $return = $this->get_total();
                break;
            case 'advance_payment':
                $return = $this->get_advance_payment();
                break;
            // end old
    		default:
    			$return = '';
    			break;
    	}

    	return $return;
    }

    // load cart contents
    function get_cart_contents()
    {
        // load cart session object
    	if( $this->sessions && $this->sessions->session )
    	{
    		foreach ( $this->sessions->session as $cart_id => $param ) {
    			$cart_item = new stdClass;
                if( is_array( $param ) || is_object( $param ) )
                {
                    foreach( $param as $k => $v ) {
                        $cart_item->{$k} = $v;
                    }

                    if ( $cart_item->product_id ) {
                        $post_type = get_post_type( $cart_item->product_id );

                        $product = 'HB_Product_' . implode( '_', array_map( 'ucfirst', explode( '_', $post_type ) ) );
                        if( ! class_exists( $product ) ) {
                            $product = 'HB_Room';
                        }
                        $product = apply_filters( 'hotel_booking_cart_product_class_name', $product, $cart_item );
                        $product = new $product( $cart_item->product_id, $param ) ;
                        $product = apply_filters( 'hotel_booking_cart_product_class', $product, $cart_item, $this );
                        // set product data
                        $cart_item->product_data = $product;
                        // amount item include tax
                        $cart_item->amount_include_tax = $product->amount_include_tax();

                        // amount item exclude tax
                        $cart_item->amount_exclude_tax = $product->amount_exclude_tax();

                        // amount item exclude tax
                        $cart_item->amount = $product->amount();

                        // amount tax
                        $cart_item->amount_tax = $cart_item->amount_include_tax - $cart_item->amount_exclude_tax;

                        // singular include tax
                        $cart_item->amount_singular_include_tax = $product->amount_singular_include_tax();

                        // singular exclude tax
                        $cart_item->amount_singular_exclude_tax = $product->amount_singular_exclude_tax();

                        // singular
                        $cart_item->amount_singular = $product->amount_singular();
                    }

                    $this->cart_contents[ $cart_id ] = $cart_item;
                }
    		}
    	}

    	return apply_filters( 'hotel_booking_load_cart_from_session', $this->cart_contents );
    }

    // load customer
    function load_customer()
    {
        // load customer session object
        if( $this->customer_sessions && $this->customer_sessions->session ) {
            if( isset( $this->customer_sessions->session['customer_id'] ) ) {
                $this->customer_id = $this->customer_sessions->session['customer_id'];
            }

            if( isset( $this->customer_sessions->session['customer_email'] ) ) {
                $this->customer_email = $this->customer_sessions->session['customer_email'];
            }

            if( isset( $this->customer_sessions->session['coupon'] ) ) {
                $this->coupon = $this->customer_sessions->session['coupon'];
            }
            $this->customer_id = apply_filters( 'hotel_booking_load_customer_from_session', $this->customer_id );
            $this->coupon = apply_filters( 'hotel_booking_load_customer_from_session', $this->coupon );
        }
    }

    // load booking
    function load_booking()
    {
        // load customer session object
        if( $this->booking_sessions && $this->booking_sessions->session ) {
            if( isset( $this->booking_sessions->session['booking_id'] ) ) {
                $this->booking_id = $this->booking_sessions->session['booking_id'];
            }
            $this->booking_id = apply_filters( 'hotel_booking_load_booking_from_session', $this->booking_id );
        }
    }

    /**
     * add_to_cart
     * @param $post_id
     * @param $params product
     * @param $qty product
     * @param $group_post_id use with extra packages
     * @param $asc if set true $qty++
     */
    function add_to_cart( $post_id = null, $params = array(), $qty = 1, $group_post_id = null, $asc = false )
    {
    	if( ! $post_id ) {
    		return new WP_Error( 'hotel_booking_add_to_cart_error', __( 'Can not add to cart, product is not exist.', 'tp-hotel-booking' ) );
    	}

    	$cart_item_id = $this->generate_cart_id( $params );
    	if ( $qty == 0 ) {
    		return $this->remove_cart_item( $cart_item_id );
    	}

    	// cart item is exist
    	if ( isset( $this->cart_contents[ $cart_item_id ] ) )
    	{
    		if( $asc === true ) {
    			$cart_item = $this->get_cart_item( $cart_item_id );
    			$qty = $cart_item->quantity + $qty;
    		}
    		else {
    			$this->remove_cart_item( $cart_item_id );
    		}
    	}

    	// set params product_id
    	$params[ 'product_id' ] = $post_id;

    	// set params quantity
    	$params[ 'quantity' ] = $qty;

        $params = apply_filters( 'tp_hotel_booking_add_to_cart_params', $params );
    	// set session cart
    	$this->sessions->set( $cart_item_id, $params );

        // do action
        do_action( 'hotel_booking_added_cart', $cart_item_id, $params );

    	// refresh cart
    	$this->refresh();

        return $cart_item_id;
    }

    // remove cart item by id
    function remove_cart_item( $cart_item_id = null )
    {
		do_action( 'hotel_booking_remove_cart_item', $cart_item_id );

		if( isset( $this->cart_contents[ $cart_item_id ] ) ){
			unset( $this->cart_contents[ $cart_item_id ] );
		}
        // set null
		$this->sessions->set( $cart_item_id, null );

        if ( ! empty( $this->cart_contents ) )
        {
            foreach ( $this->cart_contents as $cart_id => $cart_item ) {
                if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_item_id )
                {
                    unset( $this->cart_contents[ $cart_id ] );
                    $this->sessions->set( $cart_id, null );
                }
            }
        }
        // hook
		do_action( 'hotel_booking_removed_cart_item', $cart_item_id );

        // refresh cart
        $this->refresh();
		// return cart item removed
		return $cart_item_id;
    }

    // get rooms of cart_contents
    function get_rooms()
    {
        if( ! $this->cart_contents )
        {
            return null;
        }

        $rooms = array();
        foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
            if( ! isset( $cart_item->parent_id ) ) {
                $rooms[ $cart_item_id ] = $cart_item->product_data;
            }
        }

        return $rooms;
    }

    // get extra packages
    function get_extra_packages( $parent_cart_id = null )
    {
        $packages = array();
        if( $this->cart_contents ) {
            foreach ( $this->cart_contents as $cart_id => $cart_item ) {
                if( isset( $cart_item->parent_id ) && $cart_item->parent_id === $parent_cart_id )
                {
                    $packages[ $cart_id ] = $cart_item;
                }
            }
        }
        return $packages;
    }

    // set empty cart
    function empty_cart()
    {
		// remove
		$this->cart_contents = array();

        // reset all sessions
		$this->sessions->remove();

        $this->booking_sessions->remove();

        $this->set_customer( 'coupon', null );

		do_action( 'hotel_booking_empty_cart' );
		// refresh cart contents
		$this->refresh();

    }

    // generate cart item id
    function generate_cart_id( $params = array() )
    {
        ksort( $params );
    	return hb_generate_cart_item_id( $params );
    }

    // get cart item
    function get_cart_item( $cart_item_id = null )
    {
    	if ( ! $cart_item_id ) {
    		return null;
    	}

    	if ( isset( $this->cart_contents[ $cart_item_id ] ) ) {
    		return $this->cart_contents[ $cart_item_id ];
    	}

    	return null;
    }

    // set customer object
    function set_customer( $name = null, $val = null )
    {
        if( ! $name || ! $val )
            return;
        // set session cart
        $this->customer_sessions->set( $name, $val );

        // refresh
        $this->load_customer();
    }

    // set customer object
    function set_booking( $name = null, $val = null )
    {
        if( ! $name || ! $val )
            return;
        // set session cart
        $this->booking_sessions->set( $name, $val );

        // refresh
        $this->load_booking();
    }

    // get cart item by parent_id
    function get_cart_item_by_parent( $parent_id = null )
    {
        if( ! $parent_id || empty( $this->cart_contents ) ) return;

        $results = array();
        foreach ( $this->cart_contents as $cart_id => $cart_item ) {
            if( isset( $cart_item->parent_id ) === $parent_id )
            {
                $results[ $cart_id ] = $cart_item;
            }
        }

        return $results;
    }

    // refresh carts
    function refresh()
    {
    	// refresh cart_contents
		$this->cart_contents = $this->get_cart_contents();

		// refresh cart_totals
		$this->cart_total_include_tax = $this->cart_total = $this->cart_total_include_tax();

		// refresh cart_totals_exclude_tax
		$this->cart_totals_exclude_tax = $this->cart_total_exclude_tax();

		// refresh cart_items_count
		$this->cart_items_count = count( $this->cart_contents );

        // refresh customer
        $this->load_customer();

        // refresh booking
        $this->load_booking();
    }

    // update cart
    function hotel_booking_cart_update()
    {

        if( ! isset( $_POST ) || empty( $_POST['hotel_booking_cart'] ) )
            return;

        if( ! isset( $_POST['hotel_booking_cart'] ) )
            return;

        if( ! isset($_POST['hb_cart_field']) || ! wp_verify_nonce( $_POST['hb_cart_field'], 'hb_cart_field' ) )
            return;

        $cart_number = $_POST['hotel_booking_cart'];
        $cart_contents = $this->cart_contents;
        foreach ( $cart_number as $cart_id => $qty ) {
            // if not in array keys $cart_contents
            if( ! array_key_exists( $cart_id, $cart_contents ) ) {
                continue;
            }

            $cart_item = $cart_contents[ $cart_id ];

            if ( ! $cart_item ) {
                continue;
            }

            if( $qty == 0 )
            {
                $this->remove_cart_item( $cart_id );
            }
            else
            {
                $param = array();
                $param[ 'product_id' ] = $cart_item->product_id;
                $param[ 'check_in_date' ] = $cart_item->check_in_date;
                $param[ 'check_out_date' ] = $cart_item->check_out_date;
                if( isset( $cart_item->parent_id ) ) {
                    $param[ 'parent_id' ] = $cart_item->parent_id;
                }
                // update
                $param = apply_filters( 'tp_hotel_booking_ajax_update_cart_item_cart_params', $param, $cart_item );
                $this->add_to_cart( $cart_item->product_id, $param, (int)$qty );
            }
        }

        do_action( 'hotel_booking_cart_update', $_POST );
        //refresh
        $this->refresh();
        return;
    }

    // cart total include tax
    function cart_total_include_tax()
    {
    	$total = 0;
    	if( ! empty( $this->cart_contents ) )
    	{
	    	foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
	    		$total = $total + $cart_item->amount_include_tax;
	    	}
    	}
    	return apply_filters( 'hotel_booking_cart_total_include_tax', $total );
    }

    // cart total exclude tax
    function cart_total_exclude_tax()
    {
    	$total = 0;
    	if( ! empty( $this->cart_contents ) )
    	{
	    	foreach ( $this->cart_contents as $cart_item_id => $cart_item ) {
	    		$total = $total + $cart_item->amount_exclude_tax;
	    	}
    	}
    	return apply_filters( 'hotel_booking_cart_total_exclude_tax', $total );
    }

    /**
     * Calculate sub total (without tax) and return
     *
     * @return mixed
     */
    function get_sub_total(){
        return apply_filters( 'hb_cart_sub_total', $this->cart_total_exclude_tax() );
    }

    /**
     * Calculate cart total (with tax) and return
     *
     * @return mixed
     */
    function get_total(){
        return apply_filters( 'hotel_booking_get_cart_total', $this->cart_total_include_tax() );
    }

    /**
     * Get advance payment based on cart total
     *
     * @return float|int
     */
    function get_advance_payment(){
        $total = $this->cart_total_include_tax();
        if( $advance_payment = hb_get_advance_payment() ) {
            $total = $total * $advance_payment / 100;
        }
        return $total;
    }

    // total > 0
    public function needs_payment() {
        return apply_filters( 'hb_cart_needs_payment', $this->total > 0, $this );
    }

    function is_empty() {
        return apply_filters( 'hotel_booking_cart_is_empty', $this->cart_items_count ? true : false );
    }

    /**
     * generate transaction object payment
     * @return object
     */
    function generate_transaction( $customer_id = null, $payment_method = null )
    {
        if ( $this->is_empty ) {
            return new WP_Error( 'hotel_booking_transaction_error', __( 'Your cart is empty.', 'tp-hotel-booking' ) );
        }

        // initialize object
        $trasnsaction = new stdClass();
        // booking info array param
        $booking_info = array(
                '_hb_tax'                       => hb_get_tax_settings(),
                '_hb_sub_total'                 => $this->sub_total,
                '_hb_total'                     => round( $this->get_total(), 2 ),
                '_hb_advance_payment'           => $this->hb_get_cart_total( ! hb_get_request( 'pay_all' ) ),
                '_hb_advance_payment_setting'   => hb_settings()->get( 'advance_payment', 50 ),
                '_hb_currency'                  => apply_filters( 'tp_hotel_booking_payment_currency', hb_get_currency() ),
                '_hb_customer_id'               => $customer_id,
                '_hb_method'                    => $payment_method->slug,
                '_hb_method_title'              => $payment_method->title,
                '_hb_method_id'                 => $payment_method->method_id
            );

        // use coupon
        if( HB_Settings::instance()->get( 'enable_coupon' ) && $coupon = TP_Hotel_Booking::instance()->cart->coupon ){
            $coupon = HB_Coupon::instance( $coupon );
            $booking_info[ '_hb_coupon' ] = array(
                'id'        => $coupon->ID,
                'code'      => $coupon->coupon_code,
                'value'     => $coupon->discount_value
            );
        }

        // set booking info
        $trasnsaction->booking_info = $booking_info;

        // get rooms
        $rooms = $this->get_rooms();
        $_rooms = array();
        foreach ( $rooms as $k => $room ) {
            $_rooms[] = apply_filters( 'hb_generate_transaction_object_room', array(
                    '_hb_id'                => $room->ID,
                    '_hb_quantity'          => $room->quantity,
                    '_hb_check_in_date'     => strtotime( $room->check_in_date ),
                    '_hb_check_out_date'    => strtotime( $room->check_out_date ),
                    '_hb_sub_total'         => $room->amount_exclude_tax
                ), $room );
        }

        $trasnsaction->rooms = $_rooms;
        return apply_filters( 'hb_generate_transaction_object', $trasnsaction );
    }

    /**
     * Get cart total
     *
     * @param bool $pre_paid
     * @return float|int|mixed
     */
    function hb_get_cart_total( $pre_paid = false ){
        if( $pre_paid ){
            $total = $this->get_advance_payment();
        }else{
            $total = $this->total;
        }
        return $total;
    }

    // instance instead of new Class
    static function instance()
    {
    	if( empty( self::$instance ) )
    	{
    		return self::$instance = new self();
    	}

    	return self::$instance;
    }


}

// if( ! is_admin() ) {
//     $GLOBALS['hb_cart'] = HB_Cart::instance();
// }

// generate cart item id
function hb_generate_cart_item_id( $params = array() )
{
	$cart_id = array();
	foreach ( $params as $key => $param ) {
		if( is_array( $param ) )
		{
			$cart_id[] = $key . hb_generate_cart_item_id( $param );
		}
		else
		{
			$cart_id[] = $key . $param;
		}
	}

	return md5( implode( '', $cart_id ) );
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
function hb_generate_transaction_object( $order = null ){
    $cart = HB_Cart::instance();
    if( $cart->is_empty() ) return false;
    $rooms = array();
    if( $_rooms = $cart->get_rooms() ){
        foreach( $_rooms as $key => $room ) {
            $rooms[ $key ] = apply_filters( 'hb_generate_transaction_object_room', array(
                'id'                => $room->post->ID,
                'base_price'        => $room->get_price(),
                'quantity'          => $room->quantity,
                'name'              => $room->name,
                'check_in_date'     => $room->check_in_date,
                'check_out_date'    => $room->check_out_date,
                'sub_total'         => $room->get_total( $room->check_in_date, $room->check_out_date, $room->num_of_rooms, false )
            ), $room);
        }
    }

    if( ! $rooms )
        return;

    $rooms = apply_filters( 'hb_transaction_rooms', $rooms );

    global $hb_settings;
    $default_curreny = $hb_settings->get( 'currency', 'USD' );

    $transaction_object = new stdClass();
    $transaction_object->cart_id                = $cart->get_cart_id();
    $transaction_object->total                  = round( $cart->get_total(), 2 );
    $transaction_object->sub_total              = $cart->get_sub_total();
    $transaction_object->advance_payment        = hb_get_cart_total( ! hb_get_request( 'pay_all' ) );
    // currency of default
    $transaction_object->currency               = apply_filters( 'tp_hotel_booking_payment_currency', $default_curreny );
    $transaction_object->description            = hb_get_cart_description();
    $transaction_object->rooms                  = $rooms;
    $transaction_object->coupons                = '';
    $transaction_object->coupons_total_discount = '';
    $transaction_object->tax                    = hb_get_tax_settings();
    $transaction_object->price_including_tax    = hb_price_including_tax();
    $transaction_object->addition_information   = hb_get_request( 'addition_information' );
    $transaction_object->total_nights           = $cart->total_nights;

    // if( HB_Settings::instance()->get( 'enable_coupon' ) && $coupon = get_transient( 'hb_user_coupon_' . session_id() ) ){
    if( HB_Settings::instance()->get( 'enable_coupon' ) && $coupon = TP_Hotel_Booking::instance()->cart->coupon ){
        $coupon = HB_Coupon::instance( $coupon );
        $transaction_object->coupon = array(
            'id'        => $coupon->ID,
            'code'      => $coupon->coupon_code,
            'value'     => $coupon->discount_value
        );
    }

    $transaction_object = apply_filters( 'hb_generate_transaction_object', $transaction_object, $order );

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
function hb_create_booking() {

    // return WP_Error if cart is empty
    if( TP_Hotel_Booking::instance()->cart->cart_items_count === 0 ){
        return new WP_Error( 'hotel_booking_cart_empty', __( 'Your cart is empty.', 'tp-hotel-booking' ) );
    }

    $args = array(
        'status'        => '',
        'customer_id'   => null,
        'customer_note' => null,
        'booking_id'    => 0,
        'parent'        => 0
    );

    if( TP_Hotel_Booking::instance()->cart->customer_id ){
        $args['customer_id'] = absint( TP_Hotel_Booking::instance()->cart->customer_id );
    }

    TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );

    $booking = HB_Booking::instance( $args['booking_id'] );
    $booking->post->post_title      = sprintf( __( 'Booking ', 'tp-hotel-booking' ) );
    $booking->post->post_content    = hb_get_request( 'addition_information' ) ? hb_get_request( 'addition_information' ) : __( 'Empty Booking Notes', 'tp-hotel-booking' ) ;
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

    if( TP_Hotel_Booking::instance()->cart->coupon ){
        $booking_info['_hb_coupon'] = TP_Hotel_Booking::instance()->cart->coupon;
    }

    $booking->set_booking_info(
        $booking_info
    );

    $booking_id = $booking->update();

    // set session booking id
    TP_Hotel_Booking::instance()->cart->set_booking( 'booking_id', $booking_id );
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