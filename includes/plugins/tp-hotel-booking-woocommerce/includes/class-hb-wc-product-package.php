<?php

class HB_WC_Product_Package extends WC_Product_Simple{

	public $data = null;
	public $total;

	public $package = null;

	function __construct( $the_product, $args ){
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
		global $woocommerce;
		$room = $woocommerce->cart->get_cart_item( $this->data['cart_room_id'] );
		$this->package = HB_Extra_Package::instance( $this->post, $this->data['check_in_date'], $this->data['check_out_date'], $room['quantity'], 1 );
		return $this->package->price;
	}

	/**
	 * Check if a product is purchasable
	 */
	function is_purchasable(){
		return true;
	}
}
