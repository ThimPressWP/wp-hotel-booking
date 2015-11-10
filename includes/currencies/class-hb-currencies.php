<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! function_exists( 'hb_settings' ) )
	return;

/**
 * class switch currency.
 */
class HB_SW_Curreny
{

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
	 * __construct
	 */
	public function __construct()
	{
		global $hb_settings;

		$this->_is_multi = $hb_settings->get( 'is_multi_currency', 1 );
		$this->_default_currency = $hb_settings->get( 'currency', 'USD' );
		$this->_current_currency = $this->get();

		$this->init();
	}

	/**
	 * add action, filter
	 * @return [type] [description]
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
	 * get value of option name
	 * @param  string $name  name of the option
	 * @param  [string, int, boolean, null] $value default val when option name == false
	 * @return string, boolean, integer or null
	 */
	public function get( $name = null, $value = null )
	{
		if( ! $name )
			return $value;

		return $value;
	}

	/**
	 * set option name COOKIE, SESSION, hb_setting, transient
	 * @param [type] $name  name of option
	 * @param [type] $value value of oftion name
	 */
	public function set( $name = null, $value = null )
	{
		if( ! $name  )
			return;

		return $value;
	}

	/**
	 * register widget
	 * @return null
	 */
	public function register_widgets()
	{
		// register_widget( 'HB_Widget_Currency_Switch' );
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
			return self::$_instance[$currency] = new HB_SW_Curreny( $currency );

		return self::$_instance[$currency];
	}

}

$GLOBALS['hb_multi_currency'] = HB_SW_Curreny::instance();