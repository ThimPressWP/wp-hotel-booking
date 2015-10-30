<?php

/**
 * Report Class
 */
class HB_Report_Price extends HB_Report
{
	public $_title;

	public $_chart_type = 'price';

	public $_start_in;

	public $_end_in;

	public $chart_groupby;

	public $_axis_x = array();

	public $_axis_y = array();

	public $_range_start;
	public $_range_end;

	public $_range;

	static $_instance = array();

	public function __construct( $range = null )
	{
		if( ! $range ) return;

		$this->_range = $range;

		if( isset( $_GET['tab'] ) && $_GET['tab'] )
			$this->_chart_type = sanitize_text_field( $_GET['tab'] );

	}

	/**
	 * get all post have post_type = hb_booking
	 * completed > start and < end
	 * @return object
	 */
	public function getOrdersItems()
	{
		$this->calculate_current_range( $this->_range );

		$this->_title = sprintf( 'Chart in %s to %s', $this->_start_in, $this->_end_in );
		global $wpdb;
		/**
		 * pll is completed date
		 * ptt is total of booking quantity
		 */

		if( $this->chart_groupby === 'day' )
		{
			$total = $wpdb->prepare("
					(
						SELECT SUM(ptt.meta_value) AS total FROM `$wpdb->posts` pb
						INNER JOIN `$wpdb->postmeta` AS pbl ON pb.ID = pbl.post_id AND pbl.meta_key = %s
						INNER JOIN `$wpdb->postmeta` AS ptt ON pb.ID = ptt.post_id AND ptt.meta_key = %s
						WHERE pb.post_type = %s
						AND DATE(pbl.meta_value) >= %s AND DATE(pbl.meta_value) <= %s
						AND DATE(pbl.meta_value) = completed_date
					)
					", '_hb_booking_payment_completed', '_hb_total', 'hb_booking', $this->_start_in, $this->_end_in
				);

			$query = $wpdb->prepare("
					(
						SELECT DATE(pm.meta_value) AS completed_date,
						{$total} AS total
						FROM `$wpdb->posts` AS p
						INNER JOIN `$wpdb->postmeta` AS pm ON p.ID = pm.post_id AND pm.meta_key = %s
						WHERE p.post_type = %s
						AND p.post_status = %s
						AND DATE(pm.meta_value) >= %s AND DATE(pm.meta_value) <= %s
						GROUP BY completed_date
					)
					", '_hb_booking_payment_completed', 'hb_booking', 'hb-completed', $this->_start_in, $this->_end_in
				);
		}
		else
		{
			$total = $wpdb->prepare("
					(
						SELECT SUM(ptt.meta_value) AS total FROM `$wpdb->posts` pb
						INNER JOIN `$wpdb->postmeta` AS pbl ON pb.ID = pbl.post_id AND pbl.meta_key = %s
						INNER JOIN `$wpdb->postmeta` AS ptt ON pb.ID = ptt.post_id AND ptt.meta_key = %s
						WHERE pb.post_type = %s
						AND MONTH(pbl.meta_value) >= MONTH(%s) AND MONTH(pbl.meta_value) <= MONTH(%s)
						AND MONTH(pbl.meta_value) = completed_date
					)
					", '_hb_booking_payment_completed', '_hb_total', 'hb_booking', $this->_start_in, $this->_end_in
				);

			$query = $wpdb->prepare("
					(
						SELECT MONTH(pm.meta_value) AS completed_date, DATE(pm.meta_value) AS completed_time,
						{$total} AS total
						FROM `$wpdb->posts` AS p
						INNER JOIN `$wpdb->postmeta` AS pm ON p.ID = pm.post_id AND pm.meta_key = %s
						WHERE p.post_type = %s
						AND p.post_status = %s
						AND MONTH(pm.meta_value) >= MONTH(%s) AND MONTH(pm.meta_value) <= MONTH(%s)
						GROUP BY completed_date
					)
					", '_hb_booking_payment_completed', 'hb_booking', 'hb-completed', $this->_start_in, $this->_end_in
				);
		}

		return $this->parseData( $wpdb->get_results( $query ) );
	}

	public function series()
	{
		$default = new stdClass;
		$default->name = '';
		$default->type = 'area';

		$default->data = $this->getOrdersItems();

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
		$excerpts = array();

		foreach ( $results as $key => $item) {

			if( $this->chart_groupby === 'day' )
			{
				$excerpts[ (int)date("z", strtotime($item->completed_date)) ] = $item->completed_date;
				$keyr = strtotime($item->completed_date); // timestamp
				/**
				 * compare 2015-10-30 19:50:50 => 2015-10-30. not use time
				 */
				$data[ $keyr ] = array(
						strtotime( date('Y-m-d', $keyr ) ) * 1000,
						(float)$item->total
					);
			}
			else
			{
				$keyr = strtotime( date( 'Y-m-1', strtotime($item->completed_time) ) ); // timestamp of first day month in the loop
				$excerpts[ (int)date("m", strtotime($item->completed_time)) ] = date( 'Y-m-d', $keyr );
				$data[ $keyr ] = array(
						strtotime( date('Y-m-1', $keyr ) ) * 1000,
						(float)$item->total
					);
			}
		}

		$range = $this->_range_end - $this->_range_start;

		$cache = $this->_start_in;
		for( $i = 0; $i <= $range; $i++ )
		{
			$reg = $this->_range_start + $i;

			if( ! array_key_exists( $reg, $excerpts) )
			{
				if( $this->chart_groupby === 'day' )
				{
					$key = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
					$data[ $key ] = array(
						(float)strtotime( date('Y-m-d', $key ) ) * 1000,
						0
					);
				}
				else
				{

					$cache = date( "Y-$reg-01", strtotime( $cache ) ); // cache current month in the loop

					$data[ strtotime($cache) ] = array(
						(float)strtotime( date('Y-m-1', strtotime($cache) ) ) * 1000,
						0
					);
				}
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

$GLOBAL['hb_report_price'] = HB_Report_Price::instance();