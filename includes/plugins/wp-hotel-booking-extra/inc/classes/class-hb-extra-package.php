<?php

class HB_Extra_Package
{
	/**
	 * instance
	 * @var null
	 */
	static $_instance = null;

	/**
	 * package
	 * @var null
	 */
	public $_package = null;

	/**
	 * post_type = hb_extra_room
	 * @var null
	 */
	protected $_post = null;

	/**
	 * checkin room
	 * @var null
	 */
	protected $check_in_date = null;

	/**
	 * checkout room
	 * @var null
	 */
	protected $check_out_date = null;

	/**
	 * room quantity
	 * @var null
	 */
	public $parent_quantity = null;

	/**
	 * package quantity
	 * @var null
	 */
	public $quantity = null;

	function __construct( $post, $params = array() )
	{
		$params = wp_parse_args( $params, array(
 				'check_in_date' 	=> '',
 				'check_out_date' 	=> '',
 				'room_quantity'		=> 1,
 				'quantity' 			=> 1
			) );
		$this->check_in_date = $params['check_in_date'];

		if( ! $this->check_in_date )
			$this->check_in_date = time();

		$this->check_out_date = $params['check_out_date'];

		if( ! $this->check_out_date )
			$this->check_out_date = time();

		$this->parent_quantity = $params['room_quantity'];

		$this->quantity = $params['quantity'];

		if( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_extra_room') {
            $this->_post = get_post( $post );
        }elseif( $post instanceof WP_Post || is_object( $post ) ){
            $this->_post = $post;
        }

        if( ! $this->_post ) return;
	}

	public function __get( $key )
	{
		switch ( $key ) {
			case 'ID':
				# code...
				$return = $this->_post->ID;
				break;
			case 'title':
				# code...
				$return = $this->_post->post_title;
				break;
			case 'description':
				# code...
				$return = $this->_post->post_content;
				break;
			case 'regular_price':
				# code...
				$return = $this->get_regular_price();
				break;
			case 'regular_price_tax':
				# code...
				$return = $this->get_regular_price( true );
				break;
			case 'quantity':
				# code...
				$return = $this->quantity;
				break;
			case 'price':
				# code...
				$return = $this->get_price_package( false );
				break;
			case 'price_tax':
				# code...
				$return = $this->get_price_package();
				break;
			case 'respondent':
				# code...
				$return = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_respondent', true );
				break;
			case 'respondent_name':
				# code...
				$return = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_respondent_name', true );
				break;
			case 'night':
				$return = hb_count_nights_two_dates( $this->check_out_date, $this->check_in_date );
				break;
			case 'amount_singular':
                $return = $this->amount_singular();
                break;
			case 'amount_singular_exclude_tax':
				# code...
				$return = $this->amount_singular_exclude_tax();
				break;
            case 'amount_singular_include_tax':
                $return = $this->amount_singular_include_tax();
                break;
			default:
				$return = null;
				break;
		}
		return $return;
	}

	function get_data( $key = null ) {
		if ( ! $key ) {
			return;
		}

		if ( isset( $this->{$key} ) ) {
			return $this->{$key};
		}
	}

	/**
	 * get price of package
	 * @return float price of package
	 */
	function get_price_package( $tax = true )
	{
		if( $tax )
		{
			$regular_price = (float)$this->regular_price_tax;// * (int)$this->parent_quantity;
		}
		else
		{
			$regular_price = (float)$this->regular_price;// * (int)$this->parent_quantity;
		}

		$price = $regular_price;
		if( $this->respondent === 'number' )
		{
			$price = $price * $this->quantity * $this->night;
		}

		$price = apply_filters( 'hotel_booking_regular_extra_price', $price, $regular_price, $this, $tax );

		return (float)$price;
	}

	function get_regular_price( $tax = false )
	{
		if( ! $this->_post ) return;
		$price = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_price', true );

		if( $tax )
		{
			$tax_price = apply_filters( 'hotel_booking_extra_package_regular_price_incl_tax', $price * hb_get_tax_settings(), $price, $this );
			$price = $price + $tax_price;
		}

		return (float)$price;
	}

	function amount_include_tax() {
        return $this->price_tax;
    }

	function amount_exclude_tax() {
        return $this->price;
    }

	function amount( $cart = false ) {
        return hb_price_including_tax() ? $this->get_price_package() : $this->get_price_package( false );
    }

    function amount_singular_exclude_tax()
    {
        return apply_filters( 'hotel_booking_package_singular_total_exclude_tax', $this->get_regular_price( false ), $this );
    }

    function amount_singular_include_tax()
    {
        return apply_filters( 'hotel_booking_package_singular_total_include_tax', $this->get_regular_price( true ), $this );
    }

    function amount_singular()
    {
        $amount = hb_price_including_tax() ? $this->amount_singular_include_tax() : $this->amount_singular_exclude_tax();
        return apply_filters( 'hotel_booking_package_amount_singular', $amount, $this );
    }

	function is_taxable( $content = 'view' ) {
		return false;
	}

	function get_tax_class( $content = 'view' ) {
		return '';
	}

	public function is_in_stock(){

	}

	/**
	 * return instance variable instead of new class
	 * @param  integer $id               	post id
	 * @param  datetime $checkIn          	checkin date
	 * @param  datetime $checkOut         	checkout date
	 * @param  integer $room_quantity    	number of room
	 * @param  integer $package_quantity 	number of package
	 * @return object                   	object
	 */
	static function instance( $id, $params = array() )
	{
		$params = wp_parse_args( $params, array(
 				'check_in_date' 	=> '',
 				'check_out_date' 	=> '',
 				'room_quantity'		=> 1,
 				'quantity' 			=> 1
			) );

		if( ! empty( self::$_instance[ $id ] ) )
		{
			$package = self::$_instance[ $id ];

			if( $package->check_in_date === $params['check_in_date'] &&
				$package->check_out_date === $params['check_out_date'] &&
				$package->parent_quantity == $params['room_quantity'] &&
				$package->quantity == $params['quantity']
			)
			{
				return $package;
			}
		}

		return new self( $id, $params );
	}

}