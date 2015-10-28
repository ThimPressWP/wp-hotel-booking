<?php

/**
 * Report Class
 */
class HB_Report
{

	public $_start_in;

	public $_end_in;

	public $chart_groupby;

	static $_instance = array();

	public function __construct( $range = null )
	{
		if( ! $range ) return;

		$this->calculate_current_range( $range );
	}

	public function calculate_current_range( $current_range )
	{
		switch ( $current_range ) {

			case 'custom' :
				$this->_start_in = strtotime( sanitize_text_field( $_GET['_start_in'] ) );
				$this->_end_in   = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET['_end_in'] ) ) );

				if ( ! $this->_end_in ) {
					$this->_end_in = current_time('timestamp');
				}

				$interval = 0;
				$min_date = $this->_start_in;

				while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->_end_in ) {
					$interval ++;
				}

				// 3 months max for day view
				if ( $interval > 3 ) {
					$this->chart_groupby = 'month';
				} else {
					$this->chart_groupby = 'day';
				}
			break;

			case 'year' :
				$this->_start_in    = strtotime( date( 'Y-01-01', current_time('timestamp') ) );
				$this->_end_in      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'month';
			break;

			case 'last_month' :
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->_start_in        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$this->_end_in          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$this->chart_groupby     = 'day';
			break;

			case 'current_month' :
				$this->_start_in    = strtotime( date( 'Y-m-01', current_time('timestamp') ) );
				$this->_end_in      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
			break;

			case '7day' :
				$this->_start_in    = strtotime( '-6 days', current_time( 'timestamp' ) );
				$this->_end_in      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
			break;
		}
	}

	/**
	 * get all post have post_type = hb_booking
	 * completed > start and < end
	 * @return object
	 */
	public function getOrdersItems()
	{
		global $wpdb;

		$query = $wpdb->prepare("
				(
					SELECT p.ID,
					pm.meta_value AS completed_time
					FROM `$wpdb->posts` AS p
					INNER JOIN `$wpdb->postmeta` AS pm ON p.ID = pm.post_id AND pm.meta_key = %s
					WHERE p.post_type = %s
					AND p.post_status = %s
					HAVING ( completed_time >= %d AND completed_time <= %d )
				)
				", '_hb_booking_payment_completed', 'hb_booking', 'hb-completed',
				$this->_start_in, $this->_end_in
			);

		return $results = $wpdb->get_results( $query );
	}

	public function get_room_availability()
	{

	}

	static function instance( $range = null )
	{
		if( ! $range && ! isset( $_GET['range'] ) )
			$range = '7day';

		if( ! $range && isset( $_GET['range'] ) )
			$range = $_GET['range'];

		if( ! empty( self::$_instance[ $range ] ) )
			return self::$_instance[ $range ];

		return new self( $range );
	}

}

// $GLOBAL['hb_report'] = HB_Report::instance();