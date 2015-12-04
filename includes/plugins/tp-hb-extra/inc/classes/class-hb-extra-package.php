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
	protected $_check_in = null;

	/**
	 * checkout room
	 * @var null
	 */
	protected $_check_out = null;

	/**
	 * room quantity
	 * @var null
	 */
	protected $_room_quantity = null;

	/**
	 * package quantity
	 * @var null
	 */
	protected $_package_quantity = null;

	function __construct( $post, $checkIn = null, $checkOut = null, $room_quantity, $package_quantity )
	{
		$this->_check_in = $checkIn;

		if( ! $this->_check_in )
			$this->_check_in = time();

		$this->_check_out = $checkOut;

		if( ! $this->_check_out )
			$this->_check_out = time();

		if( ! $room_quantity )
			$room_quantity = 1;

		$this->_room_quantity = $room_quantity;

		if( ! $package_quantity )
			$package_quantity = 1;

		$this->_package_quantity = $package_quantity;

		if( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_extra_room') {
            $this->_post = get_post( $post );
        }elseif( $post instanceof WP_Post || is_object( $post ) ){
            $this->_post = $post;
        }
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
				$return = $this->_package_quantity;
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
				$return = hb_count_nights_two_dates( $this->_check_out, $this->_check_in );;
				break;
			default:
				$return = null;
				break;
		}
		return $return;
	}

	/**
	 * get price of package
	 * @return float price of package
	 */
	function get_price_package( $tax = true )
	{
		$price = (float)$this->regular_price;// * (int)$this->_room_quantity;

		return apply_filters( 'tp_hb_extra_package_price', $price, $this->respondent, $this->_package_quantity, $this->night, $tax );

	}

	function get_regular_price( $tax = false )
	{

		$price = get_post_meta( $this->_post->ID, 'tp_hb_extra_room_price', true );

		if( $tax && hb_price_including_tax() )
		{
			return $price + $price * hb_get_tax_settings();
		}
		return $price;

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
	static function instance( $id, $checkIn = null, $checkOut = null, $room_quantity = null, $package_quantity = null )
	{
		$in_out = strtotime( $checkIn ) . '_' . strtotime( $checkOut );
		if( ! empty( self::$_instance[ $id ] ) )
		{
			$package = self::$_instance[ $id ];

			if( $package->_check_in === $checkIn &&
				$package->_check_out === $checkOut &&
				$package->_room_quantity == $room_quantity &&
				$package->_package_quantity == $package_quantity
			)
			{
				return $package;
			}
		}

		return new self( $id, $checkIn, $checkOut, $room_quantity, $package_quantity );
	}

}