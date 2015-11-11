<?php

/**
* Storage class process changed action
*/
class HB_SW_Curreny_Storage
{

	/**
	 * all of storage
	 * @var null
	 */
	public $_strorage = null;

	/**
	 * name prefix of storage
	 * @var null
	 */
	static $_storage_name = null;

	/**
	 * self::$_instance instead of new HB_SW_Curreny_Storage();
	 * @var null
	 */
	static $_instance = null;

	function __construct( )
	{
		add_filter( 'tp_hotel_booking_currency_aggregator', array( $this, 'aggregator' ) );
	}

	public function aggregator( $aggregators )
	{
		$aggregators[ 'yahoo' ] = 'http://finance.yahoo.com';
		$aggregators[ 'google' ] = 'http://google.com/finance';

		return $aggregators;
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

	static function instance ( $name = 'tp_hotel_booking_storage' )
	{
		if( ! $name )
			$name = 'currencies';

		self::$_storage_name = $name;

		if( ! empty( self::$_instance[$name] ) )
			return self::$_instance[$name];

		return self::$_instance[$name] = new self();
	}

}
