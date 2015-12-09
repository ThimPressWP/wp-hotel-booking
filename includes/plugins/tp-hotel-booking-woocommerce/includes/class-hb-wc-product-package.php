<?php

class HB_WC_Product_Package extends WC_Product_Simple{

	public $data = null;
	public $total;

	public $package = null;

	function __construct( $the_product, $args = null ){
		parent::__construct( $the_product, $args );

		if( ! class_exists( 'HB_Extra_Package' ) )
			return;
	}

	/**
	 * regular price
	 * @return float price
	 */
	function get_price()
	{
		if( ! isset( $this->data['cart_room_id'] ) )
			return;

		global $woocommerce;
		$room = $woocommerce->cart->get_cart_item( $this->data['cart_room_id'] );
		$this->package = HB_Extra_Package::instance( $this->post, $this->data['check_in_date'], $this->data['check_out_date'], $room['quantity'], 1 );
		return $this->package->get_price_package( false );
	}

	/**
	 * is_sold_individually input type
	 * @return boolean
	 */
	function is_sold_individually()
	{
		if( ! class_exists( 'HB_Extra_Package' ) )
			return parent::is_sold_individually();

		$package = HB_Extra_Package::instance( $this->post );

		if( ! $package->respondent )
			return parent::is_sold_individually();

		if( $package->respondent === 'trip' )
			return true;

		return false;
	}

	/**
	 * Check if a product is purchasable
	 */
	function is_purchasable(){
		return true;
	}
}
