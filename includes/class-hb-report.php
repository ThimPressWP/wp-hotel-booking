<?php

/**
 * Report Class
 */
abstract class HB_Report
{
	public $_title;

	protected $_chart_type = 'price';

	public $_start_in;

	public $_end_in;

	public $chart_groupby;

	public $_range_start;
	public $_range_end;

	public $_range;

	public function __construct( $range = null )
	{
		if( ! $range ) return;

		$this->_range = $range;

		if( isset( $_GET['tab'] ) && $_GET['tab'] )
			$this->_chart_type = sanitize_text_field( $_GET['tab'] );

	}

	protected function calculate_current_range( $current_range )
	{
		switch ( $current_range ) {

			case 'custom':
				if( ! wp_verify_nonce( $_GET['tp-hotel-booking-report'], 'tp-hotel-booking-report' ) )
					return;

				if( isset( $_GET, $_GET['report_in'] ) && $_GET['report_in'] )
				{
					$this->_start_in = strtotime( sanitize_text_field( $_GET['report_in'] ) );

					if( isset($_GET['report_out']) && $_GET['report_out'] )
					{
						$this->_end_in = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET['report_out'] ) ) );
					}
					else
					{
						$this->_end_in = strtotime( 'midnight', current_time( 'timestamp' ) );
					}

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

					$this->_start_in = date( 'Y-m-d', $this->_start_in );
					$this->_end_in   = date( 'Y-m-d', $this->_end_in );
				}
				break;

			case 'year' :
				$this->_start_in    = date( 'Y-01-01', current_time('timestamp') );
				$this->_end_in        = date('Y-12-31', current_time('timestamp')); // date( 'Y-m-d', current_time( 'timestamp' ) );
				$this->chart_groupby = 'month';
			break;

			case 'last_month' :
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->_start_in         = date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) );
				$this->_end_in           = date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) );
				$this->chart_groupby     = 'day';
			break;

			case 'current_month' :
				$this->_start_in     = date( 'Y-m-01', current_time('timestamp') );
				$this->_end_in       = date( 'Y-m-t', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
			break;

			case '7day' :
				$this->_start_in     = date( 'Y-m-d', strtotime( '-6 days', current_time( 'timestamp' ) ) );
				$this->_end_in       = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );
				$this->chart_groupby = 'day';
			break;
		}

		$this->_start_in = apply_filters( 'tp_hotel_booking_report_start_in', $this->_start_in );
		$this->_end_in = apply_filters( 'tp_hotel_booking_report_end_in', $this->_end_in );

		if( $this->chart_groupby === 'day' )
		{
			$this->_range_start 	= date( 'z', strtotime($this->_start_in) );
			$this->_range_end 	= date( 'z', strtotime($this->_end_in) );
		}
		else
		{
			$this->_range_start 	= date( 'm', strtotime($this->_start_in) );
			$this->_range_end 	= date( 'm', strtotime($this->_end_in) );
		}
	}

	/**
	 * get all post have post_type = hb_booking
	 * completed > start and < end
	 * @return object
	 */
	protected function getOrdersItems()
	{
		return true;
	}

	protected function series()
	{
		return true;
	}

	protected function parseData( $results )
	{
		return true;
	}

}
