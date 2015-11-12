<?php

require_once __DIR__ . '/class-hb-currencies-settings.php';
require_once __DIR__ . '/class-hb-currencies-storage.php';

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
	 * allow multi - currency
	 * @var boolean
	 */
	public $_is_multi = false;

	/**
	 * __constructor
	 */
	public function __construct( )
	{
		// include file
		$this->includes();
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		/**
		 * if is multi currency is true
		 * do all action in frontend
		 */
		add_filter( 'hb_currency', array( $this, 'switch_currencies' ) );
		add_filter( 'tp_hotel_booking_price_switcher', array( $this, 'switch_price' ) );
		add_filter( 'tp_hotel_booking_currency_aggregator', array( $this, 'aggregator' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * add action, filter
	 * @return null
	 */
	public function init()
	{
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

	/**
	 * switch currency
	 * @param  string key $currency
	 * @return string key
	 */
	public function switch_currencies( $currency )
	{
		$settings = HB_SW_Curreny_Setting::instance();
		$storage = HB_SW_Curreny_Storage::instance();
		if( $this->_is_multi = $settings->get('is_multi_currency', false) )
		{
			do_action( 'tp_hb_before_currencies_switcher' );

			$currency = apply_filters( 'tp_hb_currencies_switcher', $storage->get( 'currency' ) );

			do_action( 'tp_hb_after_currencies_switcher' );
		}
		return $currency;
	}

	/**
	 * switch price
	 * @param  numberic $price
	 * @return numberic
	 */
	public function switch_price ( $price )
	{
		$settings = HB_SW_Curreny_Setting::instance();
		$storage = HB_SW_Curreny_Storage::instance();

		$default_currency = $settings->_detault_currency;

		$current_currency = $storage->get( 'currency' );

		$rate = $storage->get_rate( $default_currency, $current_currency );

		return (float)$price * $rate;
	}

	/**
	 * generate aggregator
	 * @param  array $aggregators
	 * @return array
	 */
	public function aggregator( $aggregators )
	{
		$aggregators[ 'yahoo' ] = 'http://finance.yahoo.com';
		$aggregators[ 'google' ] = 'http://google.com/finance';

		return $aggregators;
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

	/**
	 * admin setting
	 * @return null
	 */
	function admin_settings()
	{
		// TP_Hotel_Booking::instance()->_include( 'includes/currencies/views/settings.php' );
		require_once __DIR__ . '/settings/settings.php' ;
	}

}
new HB_SW_Curreny();