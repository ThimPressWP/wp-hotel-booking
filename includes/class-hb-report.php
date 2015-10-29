<?php

/**
 * Report Class
 */
class HB_Report
{
	public $_title;

	public $_chart_type = 'price';

	public $_start_in;

	public $_end_in;

	public $chart_groupby;

	public $_axis_x = array();

	public $_axis_y = array();

	public $_day_start;
	public $_day_end;

	static $_instance = array();

	public function __construct( $range = null )
	{
		if( ! $range ) return;

		if( isset( $_GET['tab'] ) && $_GET['tab'] )
			$this->_chart_type = sanitize_text_field( $_GET['tab'] );

		$this->calculate_current_range( $range );

		$this->_title = sprintf( 'Chart in %s to %s', $this->_start_in, $this->_end_in );
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
				$this->_start_in    = date( 'Y-01-01', current_time('timestamp') );
				$this->_end_in      = date( 'Y-m-d', current_time( 'timestamp' ) );
				$this->chart_groupby = 'month';
			break;

			case 'last_month' :
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->_start_in        = date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) );
				$this->_end_in          = date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) );
				$this->chart_groupby     = 'day';
			break;

			case 'current_month' :
				$this->_start_in    = date( 'Y-m-01', current_time('timestamp') );
				$this->_end_in      = date( 'Y-m-d', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
			break;

			case '7day' :
				$this->_start_in    = date( 'Y-m-d', strtotime( '-6 days', current_time( 'timestamp' ) ) );
				$this->_end_in      = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );
				$this->chart_groupby = 'day';
			break;
		}

		$this->_day_start = date( 'z', strtotime($this->_start_in) );
		$this->_day_end = date( 'z', strtotime($this->_end_in) );
	}

	/**
	 * get all post have post_type = hb_booking
	 * completed > start and < end
	 * @return object
	 */
	public function getOrdersItems()
	{
		global $wpdb;

		if( $this->_chart_type === 'price' )
		{
			/**
			 * pll is completed date
			 * ptt is total of booking
			 */
			$query = $wpdb->prepare("
					(
						SELECT SUM(ptt.meta_value) AS total FROM `$wpdb->posts` pb
						INNER JOIN `$wpdb->postmeta` AS pbl ON pb.ID = pbl.post_id AND pbl.meta_key = %s
						INNER JOIN `$wpdb->postmeta` AS ptt ON pb.ID = ptt.post_id AND ptt.meta_key = %s
						WHERE pb.post_type = %s
						AND pbl.meta_value >= %s AND pbl.meta_value <= %s
						AND DATE(pbl.meta_value) = DATE(completed_time)
					)
					", '_hb_booking_payment_completed', '_hb_total', 'hb_booking', $this->_start_in, $this->_end_in
				);

			$query = $wpdb->prepare("
					(
						SELECT p.ID as ID, pm.meta_value AS completed_time,
						{$query} AS total
						FROM `$wpdb->posts` AS p
						INNER JOIN `$wpdb->postmeta` AS pm ON p.ID = pm.post_id AND pm.meta_key = %s
						WHERE p.post_type = %s
						AND p.post_status = %s
						AND pm.meta_value >= %s AND pm.meta_value <= %s
						GROUP BY completed_time
					)
					", '_hb_booking_payment_completed', 'hb_booking', 'hb-completed', $this->_start_in, $this->_end_in
				);
		}
		else if( $this->_chart_type === 'room' )
		{

		}

		return $this->parseData( $wpdb->get_results( $query ) );
	}

	public function series()
	{
		$default = new stdClass;
		$default->name = '';
		$default->type = 'area';
		$default->data = $this->getOrdersItems();
		$default->pointInterval = 24 * 3600 * 1000;

		return apply_filters( 'tp_hotel_booking_charts', array(
				$default
			));
	}

	public function get_room_availability()
	{

	}

	public function parseData( $results )
	{

		$data = array();
		// $totals = false;
		$excerpts = array();
		$year = false;
		// $month = false;

		foreach ( $results as $key => $item) {

			if( $year === false )
			{
				// $day = (int)date( 'd', strtotime($item->completed_time) );
				// $month = (int)date( 'm', strtotime($item->completed_time) );
				$year = (int)date( 'Y', strtotime($item->completed_time) );
				// $totals = cal_days_in_month( CAL_GREGORIAN, $month, $year );
			}

			$excerpts[ (int)date("z", strtotime($item->completed_time)) ] = $item->completed_time;

			$data[strtotime($item->completed_time)] = array(
					strtotime( date('Y-m-d', strtotime($item->completed_time)) ) * 1000,
					(float)$item->total
				);
		}

		$range = $this->_day_end - $this->_day_start;

		for( $i = 1; $i < $range; $i++ )
		{
			$day = $this->_day_start + $i - 1;
			if( ! array_key_exists( $day, $excerpts) )
			{
				$data[] = array(
					(float)strtotime( date('Y-m-d', strtotime( $this->_start_in ) + 24 * 60 * 60 * $i ) ) * 1000,
					0
				);
			}
		}

		sort($data);
		$results = array();
		foreach ($data as $key => $da) {
			$results[] = $da;
		}
		return $results;
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