<?php

if ( !class_exists( 'WC_Product_Simple' ) )
	return;

global $woocommerce;

if ( $woocommerce && version_compare( $woocommerce->version, '3.0.0', '<' ) ) {

	require_once 'class-hb-wc-2x-product-package.php';
	return;

} else {

	class HB_WC_Product_Package extends WC_Product_Simple {

		//public $data = null;
		public $total;
		public $package = null;

		public function __construct( $product = 0 ) {
			// Should not call constructor of parent
			//parent::__construct( $product );

			if ( !class_exists( 'HB_Extra_Package' ) )
				return;

			if ( is_numeric( $product ) && $product > 0 ) {
				$this->set_id( $product );
			} elseif ( $product instanceof self ) {
				$this->set_id( absint( $product->get_id() ) );
			} elseif ( !empty( $product->ID ) ) {
				$this->set_id( absint( $product->ID ) );
			}
		}

		/**
		 * regular price
		 * @return float price
		 */
		function get_price( $context = 'view' ) {
			$qty = 1;

//			$parent_id   = $this->get_data( 'parent_id' );
//			$woo_cart_id = $this->get_id('woo_cart_id');
//			if ( !isset( $parent_id ) ) {
//				$parent = WPHB_Cart::instance()->get_cart_item( $parent_id );
//				$qty    = $parent->quantity;
//			} else if ( isset( $woo_cart_id ) ) {
//				$parent = WC()->cart->get_cart_item( $woo_cart_id );
//				$qty    = $parent->get_data( 'quantity' );
//			}

			$this->package = HB_Extra_Package::instance( $this->get_id(), array(
//				'check_in_date'  => $this->get_data( 'check_in_date' ),
//				'check_out_date' => $this->get_data( 'check_out_date' ),
				'room_quantity'  => $qty,
				'quantity'       => 1
			) );

//			if ( !isset( $this->data['parent_id'] ) ) {
//				$parent = WPHB_Cart::instance()->get_cart_item( $this->data['parent_id'] );
//				$qty    = $parent->quantity;
//			} else if ( isset( $this->data['woo_cart_id'] ) ) {
//				$parent = WC()->cart->get_cart_item( $this->data['woo_cart_id'] );
//				$qty    = $parent['quantity'];
//			}
//			$this->package = HB_Extra_Package::instance( $this->post, array(
//				'check_in_date'  => $this->data['check_in_date'],
//				'check_out_date' => $this->data['check_out_date'],
//				'room_quantity'  => $qty,
//				'quantity'       => 1
//			) );

			return $this->package->amount_singular_exclude_tax();
		}

		/**
		 * is_sold_individually input type
		 * @return boolean
		 */
		function is_sold_individually() {
			if ( !class_exists( 'HB_Extra_Package' ) )
				return parent::is_sold_individually();

			$package = HB_Extra_Package::instance( $this->get_id() );

			if ( !$package->respondent )
				return parent::is_sold_individually();

			if ( $package->respondent === 'trip' )
				return true;

			return false;
		}

		/**
		 * Check if a product is purchasable
		 */
		function is_purchasable( $context = 'view' ) {
			return true;
		}

		function is_in_stock() {
			return true;
		}

		/**
		 * @param string $context
		 *
		 * @return bool
		 */
		public function exists( $context = 'view' ) {
			return $this->get_id() && ( get_post_type( $this->get_id() ) == 'hb_extra_room' ) && ( !in_array( get_post_status( $this->get_id() ), array( 'draft', 'auto-draft' ) ) );
		}

		public function is_virtual() {
			return true;
		}

		/**
		 * @param string $context
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return get_the_title( $this->get_id() );
		}

	}

}
