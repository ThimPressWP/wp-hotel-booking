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
	 * rate
	 * @var null
	 */
	public $_rate = null;

	/**
	 * name prefix of storage
	 * @var null
	 */
	public $_storage_name = null;

	/**
	 * self::$_instance instead of new HB_SW_Curreny_Storage();
	 * @var null
	 */
	static $_instance = null;

	function __construct( )
	{
		$this->_storage_name = TP_HB_STORAGE_NAME;

		add_action( 'hb_currencies_switcher', array( $this, 'switcher' ) );
		add_filter( 'hotel_booking_payment_currency_rate', array( $this, 'get_rate' ), 10, 3 );
	}

	/**
	 * filter
	 * @param  string $currency
	 * @return string
	 */
	public function switcher( $currency )
	{
		return $currency;
	}

	public function curl_get( $url )
	{
		$ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
	}

	/**
	 * rate
	 * @param  string $from
	 * @param  string $to
	 * @return (float)
	 */
	public function get_rate( $from = 'USD', $to = 'USD' )
	{
		if( $from === $to )
			return 1;

		$name = $this->_rate . '_' . $from . '_' . $to;
		$rate = get_transient( $name );

		if( ! $rate )
		{
			$settings = HB_SW_Curreny_Setting::instance();
			$type = $settings->get( 'aggregator', 'yahoo' );

			switch ( $type ) {
				case 'yahoo':
	                $yql_query = 'select * from yahoo.finance.xchange where pair in ("' . $from . $to. '")';

	                $url = 'http://query.yahooapis.com/v1/public/yql?q='. urlencode($yql_query);
	                $url .= "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";

	                if ( function_exists('curl_init') ) {
	                    $res = $this->curl_get($url);
	                } else {
	                    $res = file_get_contents($url);
	                }

	                //***
	                $results = json_decode($res, true);
	                $rate = (float) $results['query']['results']['rate']['Rate'];

					break;

				case 'google':
					# code...
					$amount = urlencode(1);
	                $from_Currency = urlencode( $from );
	                $to_Currency = urlencode( $to );
	                $url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

	                if ( function_exists('curl_init') ) {
	                    $html = $this->curl_get($url);
	                } else {
	                    $html = file_get_contents($url);
	                }

	                preg_match_all('/<span class=bld>(.*?)<\/span>/s', $html, $matches);

	                if ( isset($matches[1][0]) ) {
	                    $rate = floatval($matches[1][0]);
	                } else {
	                    $rate = sprintf( __("no data for %s", 'wp-hotel-booking'), $to );
	                }
					break;

				default:
					break;
			}

			set_transient( $name, $rate, 24 * HOUR_IN_SECONDS );
		}

		return (float)$rate;
	}

	/**
	 * get value of option name
	 * @param  string $name  name of the option
	 * @param  [string, int, boolean, null] $value default val when option name == false
	 * @return string, boolean, integer or null
	 */
	public function get ( $name = null, $value = null )
	{
		if( ! $name )
			return $value;

		$settings = HB_SW_Curreny_Setting::instance();
		if( $type = $settings->get( 'storage' ) )
		{
			switch ( $type ) {
				case 'session':
					# code...
					if ( isset( $_SESSION[ $this->_storage_name ], $_SESSION[ $this->_storage_name ][ $name ] ) )
						$value = $_SESSION[ $this->_storage_name ][ $name ];
					break;

				case 'transient':
					# code...
					$storage = get_transient( $this->_storage_name );

					if( $storage && isset( $storage[$name] ) )
						$value = $storage[$name];

					break;

				default:
					# code...
					if ( isset( $_COOKIE[ $this->_storage_name ], $_COOKIE[ $this->_storage_name ][$name] ) )
						$value = $_COOKIE[ $this->_storage_name ][$name];
					break;
			}
		}

		if( ! $value )
			$value = $settings->_detault_currency;

		return $value;
	}

	/**
	 * set option name COOKIE, SESSION, hb_setting, transient
	 * @param [type] $name  name of option
	 * @param [type] $value value of oftion name
	 */
	public function set ( $name = null, $value = null )
	{
		if( ! $name  )
			return;

		$settings = HB_SW_Curreny_Setting::instance();
		if( $type = $settings->get( 'storage' ) )
		{
			switch ( $type ) {
				case 'session':
					# code...
					if ( ! isset( $_SESSION[ $this->_storage_name ] ) )
						$_SESSION[ $this->_storage_name ] = array();

					$_SESSION[ $this->_storage_name ][ $name ] = $value;
					break;

				case 'transient':
					# code...
					$storage = get_transient( $this->_storage_name );
					if ( FALSE === $storage )
						$storage = array();

					$storage[ $name ] = $value;

					set_transient( $this->_storage_name, $storage, 24 * HOUR_IN_SECONDS );

					break;

				default:
					# code...
					setcookie( $this->_storage_name . '[' . $name . ']', $value, time() + 60 * 60 * 24, '/' );
					break;
			}
		}
	}

	static function instance ( $name = 'tp_hotel_booking_storage' )
	{
		if( ! $name )
			$name = 'currencies';

		if( ! empty( self::$_instance[$name] ) )
			return self::$_instance[$name];

		return self::$_instance[$name] = new self();
	}

}