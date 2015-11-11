<?php

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
	public function __construct( )
	{
		// include file
		$this->includes();
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * add action, filter
	 * @return null
	 */
	public function init()
	{
		$this->_storage = HB_SW_Curreny_Storage::instance();

		/**
		 * if is multi currency is true
		 * do all action in frontend
		 */

		if( $this->_is_multi )
		{
			add_filter( 'hb_get_currency', array( $this, 'switch_currencies' ) );
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
		require_once __DIR__ . '/functions.php' ;
		require_once __DIR__ . '/class-hb-abstract-shortcode.php' ;
		require_once __DIR__ . '/shortcodes/class-hb-shortcode-currency-switcher.php' ;
		require_once __DIR__ . '/widgets/class-hb-widget-currency-switch.php' ;
	}

	/**
	 * register widget
	 * @return null
	 */
	public function register_widgets()
	{
		register_widget( 'HB_Widget_Currency_Switch' );
	}

	public function switch_currencies( $currency )
	{

		return $currency;
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
			return self::$_instance[$currency] = new HB_SW_Curreny();

		return self::$_instance[$currency];
	}

}

$GLOBALS['hb_multi_currency'] = HB_SW_Curreny::instance();