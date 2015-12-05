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
		$this->package = HB_Extra_Package::instance( $this->post, $this->data['check_in_date'], $this->data['check_out_date'], $this->data['room_quantity'], 1 );
		return $this->package->price;
	}

	/**
	 * Check if a product is purchasable
	 */
	function is_purchasable(){
		return true;
	}
}
