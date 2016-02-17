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
	public $chart_groupby_title;

	public $_range_start;
	public $_range_end;

	public $_range;

	protected $_query_results = null;

	public function __construct( $range = null )
	{
		if( ! $range ) return;

		$this->_range = $range;

		if( isset( $_GET['tab'] ) && $_GET['tab'] )
			$this->_chart_type = sanitize_text_field( $_GET['tab'] );
	}

	protected function calculate_current_range( $current_range = '7day' )
	{
		switch ( $current_range ) {

			case 'custom':
				if( ! isset($_GET['tp-hotel-booking-report'])  )
					return;

				if( isset( $_GET, $_GET['report_in'] ) && $_GET['report_in'] )
				{
				    // $dateTime_format = get_option( 'date_format' );
				    // $dateCustomFormat = get_option( 'date_format_custom' );
				    // if ( ! $dateTime_format && $dateCustomFormat ) {
				    // 	$dateTime_format = $dateCustomFormat;
				    // }

					$this->_start_in = DateTime::createFromFormat( 'm/d/Y', $_GET['report_in'] )->getTimestamp();

					if( isset($_GET['report_out']) && $_GET['report_out'] )
					{
						$this->_end_in = strtotime( 'midnight', DateTime::createFromFormat( 'm/d/Y', $_GET['report_in'] )->getTimestamp() );
					} else {
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
			$this->_range_start 	= date( 'z', strtotime( $this->_start_in ) );
			$this->_range_end 	= date( 'z', strtotime( $this->_end_in ) );
			$this->chart_groupby_title = __( 'Day', 'tp-hotel-booking' );
		}
		else
		{
			$this->_range_start 	= date( 'm', strtotime( $this->_start_in ) );
			$this->_range_end 	= date( 'm', strtotime( $this->_end_in ) );
			$this->chart_groupby_title = __( 'Month', 'tp-hotel-booking' );
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

	protected function parseData( )
	{
		return true;
	}

	/**
	 * export
	 */
	protected function export_csv()
	{
		return true;
	}

}

// report menu
add_filter( 'tp_hotel_booking_menu_items', 'hotel_report_menu' );
if( ! function_exists( 'hotel_report_menu' ) )
{
	function hotel_report_menu( $menus )
	{
		$menus[ 'reports' ] = array(
                'tp_hotel_booking',
                __( 'Reports', 'tp-hotel-booking' ),
                __( 'Reports', 'tp-hotel-booking' ),
                'manage_options',
                'tp_hotel_booking_report',
                'hotel_create_report_page'
            );
		return $menus;
	}
}

if( ! function_exists( 'hotel_create_report_page' ) )
{
    function hotel_create_report_page()
    {
    	TP_Hotel_Booking::instance()->_include( 'includes/admin/views/report.php' );
    }
}

add_action( 'tp_hotel_booking_chart_sidebar', 'tp_hotel_core_report_sidebar', 10, 2 );

/**
 * @param $tab, $range
 * @return file if file exists
 */
function tp_hotel_core_report_sidebar( $tab = '', $range = '' )
{
	if( ! $tab || ! $range )
		return;

	$file = apply_filters( "tp_hotel_booking_chart_sidebar_{$tab}_{$range}", '', $tab, $range );

	if( ! $file || ! file_exists( $file ) )
	{
		$file = apply_filters( "tp_hotel_booking_chart_sidebar_layout", '', $tab, $range );
	}

	if( file_exists( $file ) )
		require $file;
}

add_action( 'tp_hotel_booking_chart_canvas', 'hotel_report_canvas', 10, 2 );

/**
 * @param $tab, $range
 * @return html file canvas
 */
function hotel_report_canvas( $tab = '', $range = '' )
{
	if( ! $tab || ! $range )
		return;

	$file = apply_filters( "tp_hotel_booking_chart_{$tab}_{$range}_canvas", '', $tab, $range );

	if( ! $file || ! file_exists( $file ) )
		$file = apply_filters( "tp_hotel_booking_chart_layout_canvas", '', $tab, $range );

	if( file_exists( $file ) )
		require $file;
}

add_filter( 'tp_hotel_booking_chart_sidebar_layout', 'hb_report_sidebar_layout', 10, 3 );

if ( ! function_exists( 'hb_report_sidebar_layout' ) )
{
    function hb_report_sidebar_layout( $file, $tab, $range )
    {
        $tab_range = HB_PLUGIN_PATH . '/includes/admin/views/reports/sidebar-'.$tab.'-'.$range.'.php';
        $tab = HB_PLUGIN_PATH . '/includes/admin/views/reports/sidebar-'.$tab.'.php';
        if( file_exists( $tab_range ) )
        {
            return $tab_range;
        }
        else if( file_exists( $tab ) )
        {
            return $tab;
        }

        return HB_PLUGIN_PATH . '/includes/admin/views/reports/sidebar.php';
    }
}

add_filter( 'tp_hotel_booking_chart_layout_canvas', 'hb_report_layout_canvas', 10, 3  );
if( ! function_exists( 'hb_report_layout_canvas' ) )
{
    function hb_report_layout_canvas( $file, $tab, $range )
    {
        $file =  HB_PLUGIN_PATH . '/includes/admin/views/reports/canvas-'.strtolower($tab).'.php';
        if( file_exists($file) )
            return $file;
    }
}
