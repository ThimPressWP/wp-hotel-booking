<?php

class HB_WC_Product_Room extends WC_Product_Simple{

	public $data = null;
	public $total;

	function __construct( $the_product, $args = null ){
		parent::__construct( $the_product, $args );
	}

	function get_price(){
		$room = HB_Room::instance( $this->post, $this->data );

		return $room->get_total( $room->check_in_date, $room->check_out_date, 1, false );
	}

	/**
	 * Check if a product is purchasable
	 */
	function is_purchasable(){
		return true;
	}
}