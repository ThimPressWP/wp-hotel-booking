<?php

/**
 * Report Class
 */
class HB_Report_Room extends HB_Report
{
	public $_title;

	public $_chart_type = 'room';

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

		$this->calculate_current_range( $this->_range );

		$this->_title = sprintf( 'Chart in %s to %s', $this->_start_in, $this->_end_in );
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
		            SELECT count(booking_item.ID)
		            FROM {$wpdb->posts} booking_item
		            INNER JOIN {$wpdb->postmeta} bi ON bi.post_id = booking_item.ID AND bi.meta_key = %s
		            INNER JOIN {$wpdb->postmeta} bo ON bo.post_id = booking_item.ID AND bo.meta_key = %s
		            WHERE
		                booking_item.post_type = %s
		                AND bm.meta_value = rooms.ID
		                AND (bi.meta_value <= %d AND bo.meta_value >= %d)
		                OR (bi.meta_value >= %d AND bi.meta_value <= %d)
		                OR (bo.meta_value > %d AND bo.meta_value <= %d)
		        )
		    ", '_hb_check_in_date', '_hb_check_out_date', 'hb_booking_item',
		        strtotime($this->_start_in), strtotime($this->_end_in),
		        strtotime($this->_start_in), strtotime($this->_end_in),
		        strtotime($this->_start_in), strtotime($this->_end_in)
			);

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

$GLOBAL['hb_report_room'] = HB_Report_Room::instance();