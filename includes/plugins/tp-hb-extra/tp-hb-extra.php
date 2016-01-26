<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

if( ! defined( 'HB_PLUGIN_PATH' ) )
	return;

if( ! defined( 'TP_HB_EXTRA' ) )
	define( 'TP_HB_EXTRA', dirname( __FILE__ ) );

if( ! defined( 'TP_HB_EXTRA_URI' ) )
	define( 'TP_HB_EXTRA_URI', HB_PLUGIN_URL . '/includes/plugins/tp-hb-extra' );

if( ! defined( 'TP_HB_EXTRA_INC' ) )
	define( 'TP_HB_EXTRA_INC', TP_HB_EXTRA . '/inc' );

if( ! defined( 'TP_HB_EXTRA_TPL' ) )
	define( 'TP_HB_EXTRA_TPL', TP_HB_EXTRA . '/templates' );

if( ! defined( 'TP_HB_OPTION_NAME' ) )
	define( 'TP_HB_OPTION_NAME', 'tp_hb_extra_room' );

class HB_Extra_Factory
{
	static $_self = null;

	function __construct()
	{

		$this->init();
		// enqueue
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'hotel_booking_cart_product_class', array( $this, 'product_class' ), 10, 3 );
	}

	/**
	 * initialize addon plugin
	 * @return null
	 */
	protected function init()
	{
		$this->_include( TP_HB_EXTRA_INC . '/functions.php' );
		if( is_admin() )
		{
			$this->_include( TP_HB_EXTRA_INC . '/admin/admin-functions.php' );
			$this->_include( TP_HB_EXTRA_INC . '/admin/class-hb-admin.php' );
		}

		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-settings.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-post-type.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-room.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-cart.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-room.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-package.php' );
	}

	/**
	 * _include function
	 * @param  $file as @string or @array
	 * @return null
	 */
	public function _include( $file )
	{
		if( is_array( $file ) )
		{
			foreach ( $file as $key => $f ) {
				if( file_exists( $f ) )
					require_once $f;
				else if( file_exists( untrailingslashit( TP_HB_EXTRA ) . '/' . $f ) )
					require_once untrailingslashit( TP_HB_EXTRA ) . '/' . $f;
			}
		}
		else
		{
			if( file_exists( $file ) )
				require_once $file;
			else if( file_exists( untrailingslashit( TP_HB_EXTRA ) . '/' . $file ) )
				require_once untrailingslashit( TP_HB_EXTRA ) . '/' . $file;
		}
	}

	function product_class( $product, $cart_item, $cart )
	{
		if( get_post_type( $cart_item->product_id ) === 'hb_extra_room' ) {
			if( isset( $cart_item->parent_id  ) )
			{
				$parent = $cart->get_cart_item( $cart_item->parent_id );
				if( $parent ) {
					$product = new HB_Extra_Package( $cart_item->product_id, $cart_item->check_in_date, $cart_item->check_out_date, $parent->quantity, $cart_item->quantity );
				}
			}
		}
		return $product;
	}

	/**
	 * enqueue script, style
	 * @return null
	 */
	public function enqueue()
	{
		if( is_admin() )
		{
			wp_register_script( 'tp-hb-extra-js', TP_HB_EXTRA_URI . '/inc/assets/js/admin.min.js', array(), HB_VERSION, true );
			wp_enqueue_style( 'tp-hb-extra-css', TP_HB_EXTRA_URI . '/inc/assets/css/admin.min.css', array(), HB_VERSION );
		}
		else
		{
			wp_register_script( 'tp-hb-extra-js', TP_HB_EXTRA_URI . '/inc/assets/js/site.js', array(), HB_VERSION, true );
			wp_enqueue_style( 'tp-hb-extra-css', TP_HB_EXTRA_URI . '/inc/assets/css/site.min.css', array(), HB_VERSION );
		}

		wp_localize_script( 'tp-hb-extra-js', 'TPHB_Extra_Lang', apply_filters( 'tp_hb_extra_l10n', array() ) );
		wp_enqueue_script( 'tp-hb-extra-js' );
	}

	static function instance()
	{
		if( self::$_self )
			return self::$_self;

		return new self();
	}

}

new HB_Extra_Factory();
