<?php

if ( !class_exists( 'WC_Product_Simple' ) )
    return;

class HB_WC_Product_Room extends WC_Product_Simple {

    public $data = null;
    public $total;

    function __construct( $the_product, $args = null ) {
        parent::__construct( $the_product, $args );
    }

    function get_price() {
        $room = HB_Room::instance( $this->post, $this->data );
        return $room->amount_singular_exclude_tax;
    }

    /**
     * Check if a product is purchasable
     */
    function is_purchasable() {
        return true;
    }

}
