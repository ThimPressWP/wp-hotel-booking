<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! function_exists( 'hb_settings' ) )
	return;

require_once __DIR__ . '/class-hb-currencies-settings.php';
require_once __DIR__ . '/class-hb-currencies-storage.php';

/**
 * class switch currency.
 */
class HB_SW_Curreny
{

	/**
	 * process action, cookie, sesssion, transient
	 * @var null
	 */
	protected $_storage = null;

	/**
	 * protected method option
	 * @var null
	 */
	protected $_options = null;

	/**
	 * default setting currency $hb_settings->get( 'currency', 'USD' );
	 * @var null
	 */
	public $_default_currency = null;

	/**
	 * current selected currency
	 * @var null
	 */
	public $_current_currency = null;

	/**
	 * allow multi - currency
	 * @var boolean
	 */
	public $_is_multi = false;

	/**
	 * instance instead new HB_SW_Curreny();
	 * @var null
	 */
	static $_instance = null;

	/**
	 * __constructor
	 */
	public function __construct( HB_SW_Curreny_Storage $storage, HB_SW_Curreny_Setting $settings )
	{
		global $hb_settings;

		$this->_storage = $storage;

		$this->_options = $settings;

		$this->_is_multi = (boolean)$this->_options->get( 'is_multi_currency', 1 );
		$this->_default_currency = $this->_options->_detault_currency;
		$this->_current_currency = $this->_storage->get();

		$this->init();
	}

	/**
	 * add action, filter
	 * @return null
	 */
	public function init()
	{
		// include file
		$this->includes();

		/**
		 * if is multi currency is true
		 * do all action in frontend
		 */
		if( $this->_is_multi )
		{
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_action( 'tp_hotel_booking_sw_currencies', array( $this, 'switch_currencies' ) );
		}

		/**
		 * generate settings in admin panel
		 */
		if( is_admin() )
		{
			add_filter( 'hb_admin_settings_tabs', array( $this, 'setting_tab' ) );
			add_action( 'hb_admin_settings_tab_currencies', array( $this, 'admin_settings' ) );

		}
	}

	public function includes()
	{
		require_once __DIR__ . '/widgets/class-hb-widget-currency-switch.php' ;
	}

	/**
	 * register widget
	 * @return null
	 */
	public function register_widgets()
	{
		// register_widget( 'HB_Widget_Currency_Switch' );
	}

	public function switch_currencies()
	{

	}

	/**
	 * admin setting tab hook
	 * @param  array $tabs
	 * @return array
	 */
	function setting_tab( $tabs )
	{
		$tabs['currencies'] = __( 'Currency', 'tp-hotel-booking' );
		return $tabs;
	}

	function admin_settings()
	{
		// TP_Hotel_Booking::instance()->_include( 'includes/currencies/views/settings.php' );
		require_once __DIR__ . '/settings/settings.php' ;
	}

	/**
	 * get intance instead of new HB_SW_Curreny();
	 * @param  text $currency
	 * @return object
	 */
	static function instance( $currency = null )
	{
		global $hb_settings;
		if( ! $currency )
			$currency = $hb_settings->get( 'currency', 'USD' );

		if( empty( self::$_instance[$currency] ) )
			return self::$_instance[$currency] = new HB_SW_Curreny( new HB_SW_Curreny_Storage, new HB_SW_Curreny_Setting );

		return self::$_instance[$currency];
	}

}

$GLOBALS['hb_multi_currency'] = HB_SW_Curreny::instance();