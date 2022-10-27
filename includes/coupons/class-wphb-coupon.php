<?php
/**
 * HB_Coupon
 *
 * @author   ThimPress
 * @package  WP-Hotel-Booking/Coupon/Classes
 * @version  1.7.2
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'HB_Coupon' ) ) {
	/**
	 * Class HB_Coupon
	 */
	class HB_Coupon {

		/**
		 * @var array
		 */
		static $_instance = null;

		/**
		 * @var bool
		 */
		public $post = false;

		/**
		 * @var bool
		 */
		protected $_settings = array();

		/**
		 * @param $post
		 */
		public function __construct( $post ) {
			if ( is_numeric( $post ) ) {
				$this->post = get_post( $post );
			} elseif ( $post instanceof WP_Post || ( is_object( $post ) && ! ( $post instanceof HB_Coupon ) ) ) {
				$this->post = $post;
			} elseif ( $post instanceof HB_Coupon ) {
				$this->post = $post->post;
			}
			$this->_load_settings();

			add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
		}

		/**
		 * Load settings.
		 */
		private function _load_settings() {
			if ( ! empty( $this->post->ID ) ) {
				if ( $metas = get_post_meta( $this->post->ID ) ) {
					foreach ( $metas as $k => $v ) {
						$k                     = str_replace( '_hb_', '', $k );
						$this->_settings[ $k ] = $v[0];
					}
				}
			}
		}

		/**
		 * @param $prop
		 *
		 * @return bool|float|int
		 */
		public function __get( $prop ) {
			$return = false;
			switch ( $prop ) {
				case 'discount_value':
					$return = $this->get_discount_value();
					break;
				case 'coupon_code':
					$return = $this->post->post_title;
					break;
				default:
					if ( ! empty( $this->post->{$prop} ) ) {
						$return = $this->post->{$prop};
					}
			}

			return $return;
		}

		/**
		 * @param int $subtotal
		 *
		 * @return float|int
		 */
		public function get_discount_value( $subtotal = 0 ) {
			remove_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

			$discount = 0;
			switch ( $this->_settings['coupon_discount_type'] ) {
				case 'percent_cart':
					$cart     = WPHB_Cart::instance();
					$subtotal = $cart->get_sub_total();
					$discount = $subtotal * $this->_settings['coupon_discount_value'] / 100;
					break;
				case 'fixed_cart':
					$discount = $this->_settings['coupon_discount_value'];
					break;
			}
			add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

			return $discount;
		}

		/**
		 * @param $sub_total
		 *
		 * @return float|int
		 */
		public function apply_sub_total_discount( $sub_total ) {
			$discount = $this->get_discount_value( $sub_total );

			return $discount < $sub_total ? $sub_total - $discount : 0;
		}

		/**
		 * @return mixed
		 */
		public function get_cart_sub_total() {
			remove_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
			$cart           = WPHB_Cart::instance();
			$cart_sub_total = $cart->get_sub_total();
			add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

			return $cart_sub_total;
		}

		/**
		 * @return array
		 */
		public function validate() {
			$return = array(
				'is_valid' => true,
			);
			if ( ! empty( $this->_settings['minimum_spend'] ) && ( ( $minimum_spend = intval( $this->_settings['minimum_spend'] ) ) > 0 ) ) {
				$return['is_valid'] = $this->get_cart_sub_total() >= $minimum_spend;
				if ( ! $return['is_valid'] ) {
					$return['message'] = sprintf( __( 'The minimum spend for this coupon is %s.', 'wp-hotel-booking-coupon' ), $minimum_spend );
				}
			}

			if ( $return['is_valid'] && ! empty( $this->_settings['maximum_spend'] ) && ( ( $maximum_spend = intval( $this->_settings['maximum_spend'] ) ) > 0 ) ) {
				$return['is_valid'] = $this->get_cart_sub_total() <= $maximum_spend;
				if ( ! $return['is_valid'] ) {
					$return['message'] = sprintf( __( 'The maximum spend for this coupon is %s.', 'wp-hotel-booking-coupon' ), $maximum_spend );
				}
			}

			if ( $return['is_valid'] && ! empty( $this->_settings['limit_per_coupon'] ) && ( $limit_per_coupon = intval( $this->_settings['limit_per_coupon'] ) ) > 0 ) {
				$usage_count        = ! empty( $this->_settings['usage_count'] ) ? intval( $this->_settings['usage_count'] ) : 0;
				$return['is_valid'] = $limit_per_coupon > $usage_count;
				if ( ! $return['is_valid'] ) {
					$return['message'] = __( 'Coupon usage limit has been reached.', 'wp-hotel-booking-coupon' );
				}
			}

			return $return;
		}

		/**
		 * @param null $coupon
		 *
		 * @return HB_Coupon|mixed
		 */
		public static function instance( $coupon = null ) {
			$post = $coupon;

			$id = null;
			if ( $coupon instanceof WP_Post ) {
				$id = $coupon->ID;
			} elseif ( is_object( $coupon ) && isset( $coupon->ID ) ) {
				$id = $coupon->ID;
			} elseif ( $coupon instanceof HB_Coupon ) {
				$id = $coupon->post->ID;
			} else {
				$id = $coupon;
			}

			if ( isset( self::$_instance[ $id ] ) ) {
				return self::$_instance[ $id ];
			}

			return self::$_instance[ $id ] = new self( $post );
		}
	}
}
