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

	public $_query_results = null;

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

		if( ! $this->_rooms )
			return;

		$this->calculate_current_range( $this->_range );

		$this->_title = sprintf( __( 'Chart in %s to %s', 'tp-hotel-booking' ), $this->_start_in, $this->_end_in );

		$this->_query_results = $this->getOrdersItems();
		add_action( 'admin_init', array( $this, 'export_csv' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

	}

	function enqueue()
	{
		wp_enqueue_script( 'jquery-ui-datepicker' );
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
		$transient_name = 'tp_hotel_booking_charts_query' . $this->_chart_type . '_' . $this->chart_groupby . '_' . $this->_range . '_' . $this->_start_in . '_' . $this->_end_in;

		if ( false === ( $results = get_transient( $transient_name ) ) )
		{
			global $wpdb;

			if( $this->chart_groupby === 'day' )
			{
				$total = $wpdb->prepare("
				        (
				            SELECT ra.meta_value
				            FROM {$wpdb->postmeta} ra
				            INNER JOIN {$wpdb->posts} r ON ra.post_id = r.ID AND ra.meta_key = %s
				                WHERE r.ID = room_ID
				        )
				    ", '_hb_num_of_rooms');

				$sub_query = array();
				foreach ( $this->_rooms as $key => $id ) {
					$sub_query[] = ' room_ID = ' . $id;
				}
				$sub_query = implode( ' OR', $sub_query);

				$query = $wpdb->prepare("
						SELECT booked.ID AS book_item_ID,
						checkin.meta_value as checkindate,
						checkout.meta_value as checkoutdate,
						room_id.meta_value AS room_ID,
						{$total} AS total,
						booking.ID AS book_id
						FROM $wpdb->posts AS booked
						INNER JOIN {$wpdb->postmeta} AS room_id ON room_id.post_id = booked.ID AND room_id.meta_key = %s
						INNER JOIN {$wpdb->postmeta} AS checkin ON checkin.post_id = booked.ID AND checkin.meta_key = %s
						INNER JOIN {$wpdb->postmeta} AS checkout ON checkout.post_id = booked.ID AND checkout.meta_key = %s
						INNER JOIN {$wpdb->postmeta} AS pmb ON pmb.post_id = booked.ID AND pmb.meta_key = %s
						RIGHT JOIN {$wpdb->posts} AS booking ON booking.ID = pmb.meta_value AND booking.post_status = %s
						WHERE
							booked.post_type = %s
							AND ( DATE( from_unixtime( checkin.meta_value ) ) <= %s AND DATE( from_unixtime( checkout.meta_value ) ) >= %s )
							OR ( DATE( from_unixtime( checkin.meta_value ) ) >= %s AND DATE( from_unixtime( checkin.meta_value ) ) <= %s )
							OR ( DATE( from_unixtime( checkout.meta_value ) ) > %s AND DATE( from_unixtime( checkout.meta_value ) ) <= %s )
						HAVING {$sub_query}
					", '_hb_id', '_hb_check_in_date', '_hb_check_out_date', '_hb_booking_id', 'hb-completed', 'hb_booking_item',
						$this->_start_in, $this->_end_in,
						$this->_start_in, $this->_end_in,
						$this->_start_in, $this->_end_in
					);
			}
			else
			{
				$total = $wpdb->prepare("
				        (
				            SELECT ra.meta_value
				            FROM {$wpdb->postmeta} ra
				            INNER JOIN {$wpdb->posts} r ON ra.post_id = r.ID AND ra.meta_key = %s
				                WHERE r.ID = room_ID
				        )
				    ", '_hb_num_of_rooms');

				$sub_query = array();
				foreach ($this->_rooms as $key => $id) {
					$sub_query[] = ' room_ID = ' . $id;
				}
				$sub_query = implode( ' OR' , $sub_query);

				$query = $wpdb->prepare("
						SELECT booked.ID AS book_item_ID,
						checkin.meta_value as checkindate,
						checkout.meta_value as checkoutdate,
						room_id.meta_value AS room_ID,
						{$total} AS total,
						booking.ID AS book_id
						FROM $wpdb->posts AS booked
						INNER JOIN {$wpdb->postmeta} AS room_id ON room_id.post_id = booked.ID AND room_id.meta_key = %s
						INNER JOIN {$wpdb->postmeta} AS checkin ON checkin.post_id = booked.ID AND checkin.meta_key = %s
						INNER JOIN {$wpdb->postmeta} AS checkout ON checkout.post_id = booked.ID AND checkout.meta_key = %s
						INNER JOIN {$wpdb->postmeta} AS pmb ON pmb.post_id = booked.ID AND pmb.meta_key = %s
						RIGHT JOIN {$wpdb->posts} AS booking ON booking.ID = pmb.meta_value AND booking.post_status = %s
						WHERE
							booked.post_type = %s
							AND ( MONTH( from_unixtime( checkin.meta_value ) ) <= MONTH(%s) AND MONTH( from_unixtime( checkout.meta_value ) ) >= MONTH(%s) )
							OR ( MONTH( from_unixtime( checkin.meta_value ) ) >= MONTH(%s) AND MONTH( from_unixtime( checkin.meta_value ) ) <= MONTH(%s) )
							OR ( MONTH( from_unixtime( checkout.meta_value ) ) > MONTH(%s) AND MONTH( from_unixtime( checkout.meta_value ) ) <= MONTH(%s) )
						HAVING {$sub_query}
					", '_hb_id', '_hb_check_in_date', '_hb_check_out_date', '_hb_booking_id', 'hb-completed', 'hb_booking_item',
						$this->_start_in, $this->_end_in,
						$this->_start_in, $this->_end_in,
						$this->_start_in, $this->_end_in
					);
			}

			$results = $wpdb->get_results( $query );
		}

		return $results;
	}

	public function series()
	{
		if( ! $this->_rooms )
			return;

		$transient_name = 'tp_hotel_booking_charts_' . $this->_chart_type . '_' . $this->chart_groupby . '_' . $this->_range . '_' . $this->_start_in . '_' . $this->_end_in;
		delete_transient( $transient_name );
		if ( false === ( $chart_results = get_transient( $transient_name ) ) )
		{
			// $chart_results = $this->parseData( $this->_query_results );
			$chart_results = $this->js_data();
			set_transient( $transient_name, $chart_results, 12 * HOUR_IN_SECONDS );
		}
		return apply_filters( 'tp_hotel_booking_charts', $chart_results );
	}

	// new chartjs
	function js_data()
	{
		$results = $this->_query_results;
		$series = array();
		$series['labels'] = array();
		$series['datasets'] = array();

		$ids = array();
		if( ! $results )
			return $series;

		foreach ( $results as $key => $value ) {
			if( ! isset( $ids[ $value->room_ID ] ) )
				$ids[$value->room_ID] = $value->total;
		}

		$label = array();
		foreach( $ids as $id => $total )
		{
			$serie = new stdClass();
			if( ! isset( $series[ $id ] ) )
			{
				$data= new stdCLass();
			}

			$range = $this->_range_end - $this->_range_start;
			$cache = $this->_start_in;
			$data_recode = array();
			for( $i = 0; $i <= $range; $i++ )
			{
				$unavaiable = 0;
				if( $this->chart_groupby === 'day' )
				{
					$current_time = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
					$label[ $current_time ] = date( 'M.d', $current_time );
				}
				else
				{
					$reg = $this->_range_start + $i;
					$cache = date( "Y-$reg-01", strtotime( $cache ) );
					$current_time = strtotime( date( "Y-$reg-01", strtotime( $cache ) ) );
					$label[ $current_time ] = date( 'M.Y', $current_time );
				}

				foreach ( $results as $k => $v )
				{

					if( (int)$v->room_ID !== (int)$id )
						continue;

					if( $this->chart_groupby === 'day' )
					{
						$_in = strtotime( date( 'Y-m-d', $v->checkindate ) );
						$_out = strtotime( date( 'Y-m-d', $v->checkoutdate ) );

						if( $current_time >= $_in && $current_time < $_out )
						{
							$unavaiable++;
						}
					}
					else
					{
						$_in = strtotime( date( 'Y-m-1', $v->checkindate ) );
						$_out = strtotime( date( 'Y-m-1', $v->checkoutdate ) );

						if( $current_time >= $_in && $current_time <= $_out )
						{
							$unavaiable++;
						}
					}

				}

				$data_recode[ $current_time ] = $unavaiable;

			}

			ksort( $data_recode );
			// random color
			$color = hb_random_color();
			$data->fillColor = $color;
	        $data->strokeColor = $color;
	        $data->pointColor = $color;
	        $data->pointStrokeColor = "#fff";
	        $data->pointHighlightFill = "#fff";
	        $data->pointHighlightStroke = $color;

			$data->data = array_values( $data_recode );
			$series['datasets'][] = $data;
		}
		ksort( $label );
		$series['labels'] = array_values( $label );

		return $series;
	}

	// old
	public function parseData( )
	{
		$results = $this->_query_results;
		$series = array();
		$ids = array();
		foreach ($results as $key => $value) {
			if( ! isset( $ids[ $value->room_ID ] ) )
				$ids[$value->room_ID] = $value->total;
		}

		foreach( $ids as $id => $total )
		{
			if( ! isset( $series[ $id ] ) )
			{
				$prepare = array(
						'name'	=> sprintf( __( '%s unavaiable', 'tp-hotel-booking' ), get_the_title( $id ) ),
						'data'	=> array(),
						'stack' => $id
					);

				if( $this->chart_groupby === 'day' )
				{
					$unavaiable = array(
							'name'	=> sprintf( __( '%s avaiable', 'tp-hotel-booking' ), get_the_title( $id ) ),
							'data'	=> array(),
							'stack' => $id
						);
				}
				else
				{
					$unavaiable = array(
							'name'	=> sprintf( __( '%s quantity of room', 'tp-hotel-booking' ), get_the_title( $id ) ),
							'data'	=> array(),
							'stack' => $id
						);
				}
			}

			$range = $this->_range_end - $this->_range_start;
			$cache = $this->_start_in;
			for( $i = 0; $i <= $range; $i++ )
			{
				$avaiable = 0;
				if( $this->chart_groupby === 'day' )
				{
					$current_time = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
				}
				else
				{
					$reg = $this->_range_start + $i;
					$cache = date( "Y-$reg-01", strtotime( $cache ) );
					$current_time = strtotime( date( "Y-$reg-01", strtotime( $cache ) ) );
				}

				foreach ( $results as $k => $v ) {

					if( (int)$v->room_ID !== (int)$id )
						continue;

					if( $this->chart_groupby === 'day' )
					{
						$_in = strtotime( date( 'Y-m-d', $v->checkindate ) );
						$_out = strtotime( date( 'Y-m-d', $v->checkoutdate ) );

						if( $current_time >= $_in && $current_time < $_out )
							$avaiable++;
					}
					else
					{
						$_in = strtotime( date( 'Y-m-1', $v->checkindate ) );
						$_out = strtotime( date( 'Y-m-1', $v->checkoutdate ) );

						if( $current_time >= $_in && $current_time <= $_out )
							$avaiable++;
					}

				}

				$prepare['data'][] = array(
						$current_time * 1000,
						$avaiable
				);

				if( $this->chart_groupby === 'day' )
				{
					$unavaiable['data'][] = array(
							$current_time * 1000,
							$total - $avaiable
					);
				}
				else
				{
					$unavaiable['data'][] = array(
							$current_time * 1000,
							(int)$total
					);
				}

			}

			$series[] = $prepare;
			$series[] = $unavaiable;

		}

		return $series;

	}

	public function export_csv()
	{
		if( ! isset( $_POST ) ) return;

		if( ! isset( $_POST['tp-hotel-booking-report-export'] ) ||
			! wp_verify_nonce( $_POST['tp-hotel-booking-report-export'], 'tp-hotel-booking-report-export' ) )
			return;

		if( ! isset( $_POST['tab'] ) || sanitize_file_name( $_POST['tab'] ) !== $this->_chart_type )
			return;

		$inputs = $this->parseData( $this->_query_results );

		if( ! $inputs ) return;

		$rooms = array();
		foreach ( $inputs as $key => $input ) {
			if( ! isset( $rooms[ $input['stack'] ] ) )
				$rooms[ $input['stack'] ] = array();

			$rooms[ $input['stack'] ][] = $input;
		}

		$filename = 'tp_hotel_export_'.$this->_chart_type.'_'.$this->_start_in.'_to_'. $this->_end_in . '.csv';
		header('Content-Type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		foreach ($rooms as $id => $params) {

			// output the column headings
			fputcsv( $output, array( sprintf( '%s', get_the_title( $id ) ) ) );

			$column = array(
					__( 'Date/Time', 'tp-hotel-booking' )
				);

			$avaiable_data = false;
			$excerpt = array();
			if( isset( $params[0] ) )
			{
				$avaiables = $params[0];

				$avaiable_data = array(
						__( 'Unavaiable', 'tp-hotel-booking' )
					);
				foreach ($avaiables['data'] as $key => $avai) {
					if( (int)$avai[1] === 0 )
					{
						$excerpt[] = $key;
						continue;
					}
					if( $this->chart_groupby === 'day' )
					{
						if( isset( $avai[0], $avai[1] ) )
							$time = $avai[0] / 1000;

						$column[] = date( 'Y-m-d', $time );
						$avaiable_data[] = $avai[1];
					}
					else
					{
						if( isset( $avai[0], $avai[1] ) )
							$time = $avai[0] / 1000;

						$column[] = date( 'F. Y', $time );
						$avaiable_data[] = $avai[1];
					}
				}
			}

			if( $avaiable_data )
			{
				// heading and avaiable
				fputcsv( $output, $column );
				fputcsv( $output, $avaiable_data );
			}

			if( isset($params[1]) )
			{
				$unavaiable = $params[1];

				if( $this->chart_groupby === 'day' )
				{
					$unavaiable_data = array(
							__( 'Avaiable', 'tp-hotel-booking' )
						);
				}
				else
				{
					$unavaiable_data = array(
							__( 'Room Quantity', 'tp-hotel-booking' )
						);
				}
				foreach ($unavaiable['data'] as $key => $avai) {
					if( in_array( $key, $excerpt ) )
						continue;

					if( $this->chart_groupby === 'day' )
					{
						if( isset( $avai[0], $avai[1] ) )
							$time = $avai[0] / 1000;

						$column[] = date( 'Y-m-d', $time );
						$unavaiable_data[] = $avai[1];
					}
					else
					{
						if( isset( $avai[0], $avai[1] ) )
							$time = $avai[0] / 1000;

						$column[] = date( 'F. Y', $time );
						$unavaiable_data[] = $avai[1];
					}
				}
				fputcsv( $output, $unavaiable_data );
				fputcsv( $output, array() );
			}

		}

		fpassthru($output); die();

	}

	static function instance( $range = null )
	{
		if( ! $range && ! isset( $_GET['range'] ) )
			$range = '7day';

		if( ! $range && isset( $_GET['range'] ) )
			$range = $_GET['range'];

		if( ! empty( self::$_instance[ $range ] ) )
			return self::$_instance[ $range ];

		return self::$_instance[ $range ] = new self( $range );
	}

}

if( isset($_REQUEST['tab']) && $_REQUEST['tab'] === 'room' )
{
	$GLOBALS['hb_report'] = HB_Report_Room::instance();
}