<?php

/**
* process setting
*/
class HB_SW_Curreny_Setting
{
	/**
	 * setting options
	 * @var null
	 */
	public $_options = null;

	/**
	 * default currency in settings
	 * @var null
	 */
	public $_detault_currency = null;

	/**
	 * option name in admin settings
	 * @var string
	 */
	public $_option_name = 'currencies';

	static $_instance = null;

	function __construct( $name = 'currencies' )
	{
		if( $name )
			$this->_option_name = $name;

		if( isset( self::$_instance[ $this->_option_name ] ) )
			return self::$_instance[ $this->_option_name ];

		$hb_settings = hb_settings();

		$this->_options = $hb_settings->get( $this->_option_name );

		$this->_detault_currency = $hb_settings->get( 'currency', 'USD' );
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

		if( isset( $this->_options[ $name ] ) )
			return $this->_options[ $name ];

		return $value;
	}

	/**
	 * [get_field_name description]
	 * @param  string or null $name
	 * @param  boolean $multi
	 * @return string
	 */
	public function get_field_name( $name = null, $multi = false )
	{
		global $hb_settings;
		$field_name =  $hb_settings->get_prefix() . $this->_option_name . '['.$name.']';

		if( $multi )
			$field_name =  $field_name . '[]';

		return $field_name;
	}

	/**
	 * instance instead new class
	 * @param  string $name [description]
	 * @return [type]       [description]
	 */
	static function instance( $name = 'currencies' )
	{

		if( ! $name )
			$name = 'currencies';

		if( ! empty( self::$_instance[ $name ] ) )
			return self::$_instance[ $name ];

		return self::$_instance[ $name ] = new self( $name );

	}
}