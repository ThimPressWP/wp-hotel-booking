<?php
/*
 * Plugin Name: WP Hotel Booking Extra
 * Plugin URI: http://thimpress.com/
 * Description: Support extra room for WP Hotel Booking
 * Author: ThimPress
 * Version: 1.9.7.4
 * Author URI: http://thimpress.com
 * Text Domain: wp-hotel-booking-extra
 * Domain Path: /languages/
 * Requires PHP: 7.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WPHB_PLUGIN_PATH' ) ) {
	return;
}

define( 'WPHB_EXTRA_FILE', dirname( __FILE__ ) );
define( 'WPHB_EXTRA_URI', WPHB_PLUGIN_URL . '/includes/plugins/wp-hotel-booking-extra' );
define( 'WPHB_EXTRA_INC', WPHB_EXTRA_FILE . '/inc' );
define( 'WPHB_EXTRA_TEMPLATES', WPHB_EXTRA_FILE . '/templates/' );
define( 'WPHB_EXTRA_OPTION_NAME', 'tp_hb_extra_room' );

if ( ! class_exists( 'WPHB_Extra_Factory' ) ) {
	/**
	 * Class WPHB_Extra_Factory
	 */
	class WPHB_Extra_Factory {

		/**
		 * @var null
		 */
		static $_self = null;

		/**
		 * WPHB_Extra_Factory constructor.
		 */
		public function __construct() {
			$this->init();
			// enqueue
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_filter( 'hotel_booking_get_product_class', array( $this, 'product_class' ), 10, 3 );
			add_filter( 'hb_admin_i18n', array( $this, 'language_js' ) );
			add_filter( 'hb_plugins_templates_path', array( $this, 'plugin_override_templates' ) );
		}

		/**
		 * @param $l10n
		 *
		 * @return mixed
		 */
		public function language_js( $l10n ) {
			$l10n['remove_confirm'] = __( 'Remove package. Are you sure?', 'wp-hotel-booking' );

			return $l10n;
		}

		/**
		 * Include files.
		 */
		protected function init() {
			$this->_include( WPHB_EXTRA_INC . '/wphb-extra-functions.php' );
			if ( is_admin() ) {
				$this->_include( WPHB_EXTRA_INC . '/admin/class-wphb-extra-admin.php' );
			}

			$this->_include( WPHB_EXTRA_INC . '/class-wphb-extra.php' );
			$this->_include( WPHB_EXTRA_INC . '/class-wphb-extra-settings.php' );
			$this->_include( WPHB_EXTRA_INC . '/class-wphb-extra-post-type.php' );
			$this->_include( WPHB_EXTRA_INC . '/class-wphb-extra-room.php' );
			$this->_include( WPHB_EXTRA_INC . '/class-wphb-extra-cart.php' );
			$this->_include( WPHB_EXTRA_INC . '/class-wphb-extra-package.php' );
		}

		/**
		 * @param $file
		 */
		public function _include( $file ) {
			if ( is_array( $file ) ) {
				foreach ( $file as $key => $f ) {
					if ( file_exists( $f ) ) {
						require_once $f;
					} elseif ( file_exists( untrailingslashit( WPHB_EXTRA_FILE ) . '/' . $f ) ) {
						require_once untrailingslashit( WPHB_EXTRA_FILE ) . '/' . $f;
					}
				}
			} else {
				if ( file_exists( $file ) ) {
					require_once $file;
				} elseif ( file_exists( untrailingslashit( WPHB_EXTRA_FILE ) . '/' . $file ) ) {
					require_once untrailingslashit( WPHB_EXTRA_FILE ) . '/' . $file;
				}
			}
		}

		/**
		 * @param null  $product
		 * @param null  $product_id
		 * @param array $params
		 *
		 * @return HB_Extra_Package|null
		 */
		public function product_class( $product = null, $product_id = null, $params = array() ) {
			if ( ! $product_id || get_post_type( $product_id ) !== 'hb_extra_room' ) {
				return $product;
			}
			$parent_quantity = 1;
			if ( isset( $params['order_item_id'] ) ) {
				$parent_quantity = hb_get_order_item_meta( hb_get_parent_order_item( $params['order_item_id'] ), 'quantity', true );
			} elseif ( ! is_admin() && isset( $params['parent_id'] ) && WP_Hotel_Booking::instance()->cart ) {
				$parent = WP_Hotel_Booking::instance()->cart->get_cart_item( $params['parent_id'] );
				if ( $parent ) {
					$parent_quantity = $parent->quantity;
				}
			}

			return new HB_Extra_Package(
				$product_id,
				array(
					'check_in_date'  => isset( $params['check_in_date'] ) ? $params['check_in_date'] : '',
					'check_out_date' => isset( $params['check_out_date'] ) ? $params['check_out_date'] : '',
					'room_quantity'  => $parent_quantity,
					'quantity'       => isset( $params['quantity'] ) ? $params['quantity'] : 1,
				)
			);
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue() {
			if ( is_admin() ) {
				wp_register_script( 'wphb-extra-js', WPHB_EXTRA_URI . '/assets/js/admin.js', array(), WPHB_VERSION, true );
				wp_enqueue_style( 'wphb-extra-css', WPHB_EXTRA_URI . '/assets/css/admin.min.css', array(), WPHB_VERSION );
			} else {
				wp_register_script( 'wphb-extra-js', WPHB_EXTRA_URI . '/assets/js/site.min.js', array(), WPHB_VERSION, true );
				wp_enqueue_style( 'wphb-extra-css', WPHB_EXTRA_URI . '/assets/css/site.css', array(), WPHB_VERSION );
			}

			wp_localize_script( 'wphb-extra-js', 'TPHB_Extra_Lang', apply_filters( 'hb_extra_l10n', array() ) );
			wp_enqueue_script( 'wphb-extra-js' );
		}

		/**
		 * @param $plugins
		 *
		 * @return array
		 */
		public function plugin_override_templates( $plugins ) {
			if ( ! is_array( $plugins ) ) {
				return $plugins;
			}

			$plugins['wphb-extra'] = array(
				'folder' => 'wp-hotel-booking-extra',
				'path'   => WPHB_EXTRA_TEMPLATES,
			);

			return $plugins;
		}

		/**
		 * @return null|WPHB_Extra_Factory
		 */
		static function instance() {
			if ( self::$_self ) {
				return self::$_self;
			}

			return new self();
		}
	}
}

new WPHB_Extra_Factory();
