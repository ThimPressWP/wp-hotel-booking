<?php

/**
 * Report Class
 */
class HB_Report_Room extends HB_Report
{
	public $_title;

	public $_chart_type = 'room';

	public $_rooms = array();

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

		if( isset( $_GET['room_id'] ) && $_GET['room_id'] )
			$this->_rooms = $_GET['room_id'];

		$this->calculate_current_range( $this->_range );

		$this->_title = sprintf( 'Chart in %s to %s', $this->_start_in, $this->_end_in );
	}

	public function get_rooms()
	{
		global $wpdb;
		$query = $wpdb->prepare( "
				(
					SELECT ID, post_title FROM {$wpdb->posts}
					WHERE
						`post_type` = %s
						AND `post_status` = %s
				)
			", 'hb_room', 'publish' );

		return $wpdb->get_results( $query );
	}

	/**
	 * get all post have post_type = hb_booking
	 * completed > start and < end
	 * @return object
	 */
	public function getOrdersItems()
	{
		global $wpdb;

		$total = $wpdb->prepare("
		        (
		            SELECT ra.meta_value
		            FROM {$wpdb->postmeta} ra
		            INNER JOIN {$wpdb->posts} r ON ra.post_id = r.ID AND ra.meta_key = %s
		                WHERE r.ID = room_ID
		        )
		    ", '_hb_num_of_rooms');

		// count(booked.ID) AS avaiable
		$query = $wpdb->prepare("
				SELECT booked.ID AS book_item_ID,
				checkin.meta_value as checkindate,
				checkout.meta_value as checkoutdate,
				room_id.meta_value AS room_ID,
				{$total} AS total
				FROM $wpdb->posts AS booked
				INNER JOIN {$wpdb->postmeta} AS room_id ON room_id.post_id = booked.ID AND room_id.meta_key = %s
				INNER JOIN {$wpdb->postmeta} AS checkin ON checkin.post_id = booked.ID AND checkin.meta_key = %s
				INNER JOIN {$wpdb->postmeta} AS checkout ON checkout.post_id = booked.ID AND checkout.meta_key = %s
				WHERE
					booked.post_type = %s
					AND ( DATE( from_unixtime( checkin.meta_value ) ) <= %s AND DATE( from_unixtime( checkout.meta_value ) ) >= %s )
					OR ( DATE( from_unixtime( checkin.meta_value ) ) >= %s AND DATE( from_unixtime( checkin.meta_value ) ) <= %s )
					OR ( DATE( from_unixtime( checkout.meta_value ) ) > %s AND DATE( from_unixtime( checkout.meta_value ) ) <= %s )
					AND room_id.meta_value IN( %s )
			", '_hb_id', '_hb_check_in_date', '_hb_check_out_date', 'hb_booking_item',
				$this->_start_in, $this->_end_in,
				$this->_start_in, $this->_end_in,
				$this->_start_in, $this->_end_in,
				implode( ', ', $this->_rooms)
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
		// var_dump($results);
		$data = array();
		$excerpts = array();

		foreach ( $results as $key => $item) {

			if( $this->chart_groupby === 'day' )
			{
				$room_in = $item->checkindate;
				$room_out = $item->checkoutdate;

				// if(  )

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

// $GLOBAL['hb_report_room'] = HB_Report_Room::instance();