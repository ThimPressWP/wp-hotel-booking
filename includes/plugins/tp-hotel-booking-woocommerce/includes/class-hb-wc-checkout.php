<?php

class HB_WC_Checkout extends HB_Checkout
{

	function __construct()
	{
		parent::__construct();

		/**
		 * woo add new order hook
		 */
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'woo_add_order' ) );

		/**
		 * rooms transacton object
		 */
		add_filter( 'hb_transaction_rooms', array( $this, 'woo_transaction_rooms' ), 50, 1 );

		/**
		 * transaction object
		 */
		add_filter( 'hb_generate_transaction_object', array( $this, 'woo_transaction_object' ), 50, 2 );

		/**
		 * tax for woocommerce in TP Hotel Booking
		 */
		add_filter( 'hotel_booking_tax_metabox', array( $this, 'tax_order' ), 10, 1 );
		add_filter( 'hotel_booking_label_details', array( $this, 'booking_tax_price' ), 10, 1 );
		add_filter( 'hotel_booking_admin_book_details', array( $this, 'booking_details_tax_price' ), 10, 2 );
	}

	/**
	 * create customer for order
	 * @param  integer $order_id
	 * @return customer id
	 */
	function create_customer(  $order = null  )
	{
		if( ! $order )
			return;
		global $woocommerce;

		$customer_info = array(
            'ID'            => $order->get_user_id(),
            'first_name'    => $order->billing_first_name,
            'last_name'     => $order->billing_last_name,
            'address'       => $order->billing_address_1,
            'city'          => $order->billing_city,
            'state'         => $order->billing_state,
            'postal_code'   => $order->billing_postcode,
            'country'       => $woocommerce->countries->countries[ $order->billing_country ],
            'phone'         => $order->billing_phone,
            'email'         => $order->billing_email
        );

		$customer_id = hb_update_customer_info( $customer_info );

        // set transient for current customer in one hour
        set_transient( 'hb_current_customer_' . session_id(), $customer_id, HOUR_IN_SECONDS );
        return $this->_customer = $customer_id;
	}

	/**
	 * woo_add_order WooCoommerce hook create new order
	 * @param  [type] $order_id [description]
	 * @return [type]           [description]
	 */
	public function woo_add_order( $order_id )
	{

		$order = wc_get_order( $order_id );

		$cart_contents = wc()->cart->cart_contents;

		$create = true;
		foreach ( $cart_contents as $cart_key => $cart_content ) {
			if( get_post_type( $cart_content['product_id'] ) !== 'hb_room' )
			{
				$create = false;
				break;
			}
		}

		if( $create === true && $customer_id = $this->create_customer( $order ) )
		{
			if( $booking = $this->create_booking( $order ) )
			{
				$cart = HB_Cart::instance();
				$cart->empty_cart();
				return true;
			}
		}
	}

	/**
	 * create booking
	 * @param  integer $order_id
	 * @return [type]
	 */
	public function create_booking( $order = null )
	{
		global $hb_settings;
        $customer_id = get_transient( 'hb_current_customer_' . session_id() );

        $transaction_object = hb_generate_transaction_object( $order );

        // Insert or update the post data
        $booking_id = hb_create_booking();
        $booking = HB_Booking::instance( $booking_id );

        $tax                    = $transaction_object->tax;
        $price_including_tax    = $transaction_object->price_including_tax;
        $rooms                  = $transaction_object->rooms;

        // booking meta data
        $booking_info = array(
            '_hb_total_nights'              => $transaction_object->total_nights,
            '_hb_tax'                       => $tax,
            '_hb_price_including_tax'       => $price_including_tax ? 1 : 0,
            '_hb_sub_total'                 => $transaction_object->sub_total,
            '_hb_total'                     => $transaction_object->total,
            '_hb_advance_payment'           => $transaction_object->advance_payment,
            '_hb_advance_payment_setting'   => $hb_settings->get( 'advance_payment', 50 ),
            '_hb_currency'                  => $transaction_object->currency,
            '_hb_customer_id'               => $customer_id,
            '_hb_method'                    => $order->payment_method,
            '_hb_method_title'              => $order->payment_method_title,
            '_hb_method_id'                 => get_post_meta( $order->id, '_payment_method', true ),
            '_hb_woo_order_id'				=> $transaction_object->woo_order_id
        );

		/**
		 * add post meta for order woocommerce
		 */
		add_post_meta( $transaction_object->woo_order_id, 'hb_wc_booking_id', $booking_id );

        if( ! empty( $transaction_object->coupon ) ){
            $booking_info['_hb_coupon'] = $transaction_object->coupon;
        }

        $booking_info = apply_filters( 'tp_hotel_booking_checkout_booking_info', $booking_info, $transaction_object );
        $booking->set_booking_info(
            $booking_info
        );

        $booking_id = $booking->update();
        if( $booking_id ){
            $prices = array();
            delete_post_meta( $booking_id, '_hb_room_id' );
            // $tax = $hb_settings->get('tax');
            if( $rooms )
            {
                foreach( $rooms as $room_options ){
                    $num_of_rooms = $room_options['quantity'];
                    // insert multiple meta value
                    for( $i = 0; $i < $num_of_rooms; $i ++ ) {
                        add_post_meta( $booking_id, '_hb_room_id', $room_options['id'] );
                        // create post save item of order
                        $booking->save_room( $room_options, $booking_id );
                    }
                    // add_post_meta( $booking_id, '_hb_room_total', $room_options['sub_total'] );
                    $room = HB_Room::instance( $room_options['id'], $room_options);
                    $prices[ $room_options['id'] ] = $room_options['sub_total'];

                }
            }

            $booking_params = apply_filters( 'hotel_booking_booking_params', $_SESSION['hb_cart'.HB_BLOG_ID]['products'] );
            add_post_meta( $booking_id, '_hb_booking_params', $booking_params );
        }
        do_action( 'hb_new_booking', $booking_id );
        return $booking_id;
	}

	/**
	 * transaction rooms
	 */
	public function woo_transaction_rooms( $rooms )
	{

		global $woocommerce;

	    if( $woocommerce->cart->is_empty() ) return false;

	    // parse cart item
	    $rooms = array();
	    if( $_rooms = $woocommerce->cart->get_cart() )
	    {
		    foreach( $_rooms as $key => $room ) {
	            $rooms[ $key ] = apply_filters( 'hb_generate_transaction_object_room', array(
	                'id'                => $room['product_id'],
	                'base_price'        => $room['line_total'],
	                'quantity'          => $room['quantity'],
	                'name'              => $room['data']->post->post_title,
	                'check_in_date'     => $room['check_in_date'],
	                'check_out_date'    => $room['check_out_date'],
	                'sub_total'         => $room['line_subtotal'],
	                'tax_subtotal'		=> $room['line_subtotal_tax']
	            ), $room);

	        }
	    }
		return $rooms;
	}

	public function woo_transaction_object( $transaction, $order )
	{
		global $woocommerce;
		$cart = HB_Cart::instance();

		if( ! $order )
			return $transaction;

	    $transaction->cart_id                = $cart->get_cart_id();
	    $transaction->total                  = round( $woocommerce->cart->total, 2 );
	    $transaction->sub_total              = $woocommerce->cart->subtotal_ex_tax;
	    $transaction->advance_payment        = $woocommerce->cart->total;
	    // currency of default
	    $transaction->currency               = get_woocommerce_currency();
	    $transaction->description            = $order->customer_message ? $order->customer_message : __( 'Empty Booking Notes', 'tp-hotel-booking-woocommerce' );
	    $transaction->coupons                = '';
	    $transaction->coupons_total_discount = '';
	    $transaction->tax    				 = $woocommerce->cart->tax_total;
	    $transaction->woo_order_id			 = $order->id;
	    $transaction->price_including_tax    = wc_prices_include_tax() ? 1 : 0;
	    $transaction->addition_information   = $order->customer_message ? $order->customer_message : __( 'Empty Booking Notes', 'tp-hotel-booking-woocommerce' );
	    $transaction->total_nights           = $cart->total_nights;

	    if( WC()->cart->coupons_enabled() ){
	        $transaction->coupon = WC()->cart->get_coupons();
	    }

		return $transaction;
	}

	/**
	 * tax_order
	 * @param  [type] $tax [description]
	 * @return [type]      [description]
	 */
	public function tax_order( $tax )
	{
		global $post;

		if( ! $order_ID = get_post_meta( $post->ID, '_hb_woo_order_id', true ) )
			return $tax;

		return get_post_meta( $post->ID, '_hb_woo_tax_total', true );
	}

	/**
	 * booking_tax_price metabox admin
	 * @param  [type] $html [description]
	 * @param  [type] $atts [description]
	 * @param  [type] $val  [description]
	 * @return [type]       [description]
	 */
	public function booking_tax_price( $val )
	{
		global $post;

		if( ! $order_woo = get_post_meta( $post->ID, '_hb_woo_order_id', true ) )
			return $val;

		if( ! $currency = get_post_meta( $post->ID, '_hb_currency', true ) )
			return $val;

		return wc_price( get_post_meta( $post->ID, '_hb_tax', true ), array( 'currency' => $currency ) );
	}

	/**
	 * admin booking details tax
	 * @param  [type] $html       [description]
	 * @param  [type] $booking_id [description]
	 * @return [type]             [description]
	 */
	function booking_details_tax_price( $html, $booking_id )
	{
		if( ! $order_woo = get_post_meta( $booking_id, '_hb_woo_order_id', true ) )
			return $html;

		/**
		 * get woocommerce tax
		 * @var [type]
		 */
		if( ! $woo_tax = get_post_meta( $booking_id, '_hb_tax', true ) )
			return $html;

		return wc_price( $woo_tax, array( 'currency' => get_post_meta( $booking_id, '_hb_currency', true ) ) );;
	}

}

new HB_WC_Checkout();
