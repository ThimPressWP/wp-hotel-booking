<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * class Block process
 */
class Hotel_Booking_Block
{

	public static $instance = null;

	function __construct()
	{
		// admin menu
		add_filter( 'hotel_booking_menu_items', array( $this, 'block_menu' ) );

		// enqueue script
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'init', array( $this, 'register_post_type' ) );

		// js template
		add_action( 'wp_ajax_hotel_block_update', array( $this, 'hotel_block_update' ) );
		add_action( 'wp_ajax_nopriv_hotel_block_update', array( $this, 'notLogin' ) );

		// remove calendar
		add_action( 'wp_ajax_hotel_block_delete_post_type', array( $this, 'wp_ajax_hotel_block_delete_post_type' ) );
		add_action( 'wp_ajax_nopriv_hotel_block_delete_post_type', array( $this, 'notLogin' ) );

		add_filter( 'hb_search_query', array( $this, 'search' ), 10, 2 );
	}

	function register_post_type()
	{
		$labels = array(
			'name'               => _x( 'Blocked', 'post type general name', 'tp-hotel-booking-block' ),
			'singular_name'      => _x( 'Blocked', 'post type singular name', 'tp-hotel-booking-block' ),
			'menu_name'          => _x( 'Blocked', 'admin menu', 'tp-hotel-booking-block' ),
			'name_admin_bar'     => _x( 'Blocked', 'add new on admin bar', 'tp-hotel-booking-block' ),
			'add_new'            => _x( 'Add New', 'block', 'tp-hotel-booking-block' ),
			'add_new_item'       => __( 'Add New Blocked', 'tp-hotel-booking-block' ),
			'new_item'           => __( 'New Blocked', 'tp-hotel-booking-block' ),
			'edit_item'          => __( 'Edit Blocked', 'tp-hotel-booking-block' ),
			'view_item'          => __( 'View Blocked', 'tp-hotel-booking-block' ),
			'all_items'          => __( 'All Blocked', 'tp-hotel-booking-block' ),
			'search_items'       => __( 'Search Blocked', 'tp-hotel-booking-block' ),
			'parent_item_colon'  => __( 'Parent Blocked:', 'tp-hotel-booking-block' ),
			'not_found'          => __( 'No blocked found.', 'tp-hotel-booking-block' ),
			'not_found_in_trash' => __( 'No blocked found in Trash.', 'tp-hotel-booking-block' )
		);

		$args = array(
			'labels'             => $labels,
	                'description'        => __( 'Blocked days.', 'tp-hotel-booking-block' ),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'block' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
            // 'can_export'         => false,
			'supports'           => array( 'title' )
		);

		register_post_type( 'hb_blocked', $args );
	}

	/**
	 * block_menu
	 * @param  $menus array
	 * @return $menus array
	 */
	function block_menu( $menus )
	{
		$menus[ 'block' ] = array(
                'tp_hotel_booking',
                __( 'Block Special Date', 'tp-hotel-booking-block' ),
                __( 'Block Special Date', 'tp-hotel-booking-block' ),
                'manage_options',
                'tp_hotel_block',
                array( $this, 'block_build_page' )
            );
		return $menus;
	}

	/**
	 * block_build_page
	 * @return html page
	 */
	function block_build_page()
	{
		require_once TP_HB_BLOCK_DIR . '/inc/admin/views/block.php';
	}

	// enqueue scripts js multidatepicker libraries
	function enqueue_scripts()
	{
		wp_enqueue_script( 'tp_hotel_booking_block_angular', TP_HB_BLOCK_URI . 'inc/libraries/angular.min.js', array(), TP_HB_BLOCK_VER );
		wp_enqueue_script( 'tp_hotel_booking_block_moment', TP_HB_BLOCK_URI . 'inc/libraries/multidatespicker/moment.min.js', array(), TP_HB_BLOCK_VER );
		wp_enqueue_script( 'tp_hotel_booking_block_lib_datepicker', TP_HB_BLOCK_URI . 'inc/libraries/multidatespicker/multipleDatePicker.min.js', array(), TP_HB_BLOCK_VER );
		wp_enqueue_style( 'tp_hotel_booking_block_lib_datepicker', TP_HB_BLOCK_URI . 'inc/libraries/multidatespicker/multiple-date-picker.css' );
		wp_enqueue_script( 'wp-util' );

		wp_enqueue_script( 'tp_hotel_booking_block', TP_HB_BLOCK_URI . 'assets/js/admin.js', array(), TP_HB_BLOCK_VER );
		wp_enqueue_style( 'tp_hotel_booking_block', TP_HB_BLOCK_URI . 'assets/css/admin.css' );

		$l10n = apply_filters( 'hote_booking_block_l10n', array(
				'ajaxurl'		=> admin_url( 'admin-ajax.php?schema=hotel-block' ),
				'error_ajax'	=> __( 'Request has error. Please try again.', 'tp-hotel-booking-block' )
			) );
		wp_localize_script( 'tp_hotel_booking_block', 'Hotel_Booking_Block', $l10n );

		wp_enqueue_script( 'tp_hotel_booking_block' );
	}

	function hotel_block_update()
	{
		if( ! isset( $_REQUEST[ 'schema' ] ) || sanitize_text_field( $_REQUEST[ 'schema' ] ) !== 'hotel-block' )
		{
			wp_send_json( array( 'status' => 'failed', 'message' => __( 'Something went wrong.', 'tp-hotel-booking-block' ) ) );
		}

		$calendars = json_decode( file_get_contents('php://input') );
		$calendars = json_decode( $calendars->data );

		global $wpdb;
		// echo '<pre>'; print_r($calendars); die();
		foreach( $calendars as $k => $calendar )
		{
			if( ! isset( $calendar->post_id ) || empty( $calendar->post_id ) )
				continue;

			if( ! isset( $calendar->selected ) || empty( $calendar->selected ) )
				continue;

			$calendar_id = $calendar->id;
			if( ! get_post( $calendar_id ) )
			{
				$calendar_id = wp_insert_post( array(
						'post_type'		=> 'hb_blocked',
						'post_status'	=> 'publish',
						'post_title'	=> __( 'Block item', 'tp-hotel-booking-block' ),
						'post_content'	=> __( 'Block item', 'tp-hotel-booking-block' )
					));
			}

			delete_post_meta( $calendar_id, 'hb_blocked_time' );

			// delete all blocked time
			$times = get_post_meta( $calendar_id, 'hb_blocked_time' );

			// add post meta for post type hb_blocked
			foreach ( $calendar->selected as $key => $timestamp ) {
				// $timestamp is millicecond in UTC +0
				$time = $timestamp / 1000 + HOUR_IN_SECONDS * 12;
				$time = hotel_block_convert_current_time( $time );

				if( ! in_array( $time, $times ) )
				{
					add_post_meta( $calendar_id, 'hb_blocked_time', $time );
				}

			}

			// delete old room selected
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => 'hb_blocked_id', 'meta_value' => $calendar_id ) );
			// add post meta blocked id
			foreach ( $calendar->post_id as $key => $post )
			{
				if( ! in_array( $calendar_id, get_post_meta( $post->ID, 'hb_blocked_id' ) ) )
				{
					add_post_meta( $post->ID, 'hb_blocked_id', $calendar_id );
				}
			}
		}

		wp_send_json( array( 'status' => 'success', 'data' => $this->get_blocked(), 'message' => __( 'Update Completed.', 'tp-hotel-booking-block' ) ) );
	}

	// ajax not loggin
	function notLogin()
	{
		wp_send_json( array( 'status' => 'failed', 'message' => __( 'You must Login System', 'tp-hotel-booking-block' ) ) );
	}

	// get blocked time return object
	function get_blocked()
	{
		global $wpdb;

		$title = $wpdb->prepare("
				SELECT room.post_title
				FROM $wpdb->posts AS room
				WHERE
					room.post_status = %s
					AND room.post_type = %s
					AND room.ID = room_meta.post_id
				GROUP BY room.ID
			", 'publish', 'hb_room');

		$query = $wpdb->prepare("
				SELECT calendar.ID as calendarID, blocked.meta_value AS selected, room_meta.post_id AS ID, ( $title ) AS post_title
				FROM $wpdb->posts AS calendar
				INNER JOIN $wpdb->postmeta AS blocked ON calendar.ID = blocked.post_id
				INNER JOIN $wpdb->postmeta AS room_meta ON room_meta.meta_value = calendar.ID
				WHERE
					calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked.meta_key = %s
					AND room_meta.meta_key = %s
				ORDER BY calendarID
			", 'hb_blocked', 'publish', 'hb_blocked_time', 'hb_blocked_id' );

		$results = $wpdb->get_results( $query, OBJECT );

		$calendars = array();
		if( $results )
		{
			foreach ( $results as $key => $post ) {
				if( ! isset( $calendars[ $post->calendarID ] ) )
				{
					$calendars[ $post->calendarID ] = new stdClass();
				}

				if( ! isset( $calendars[ $post->calendarID ]->id ) )
				{
					$calendars[ $post->calendarID ]->id = (int)$post->calendarID;
				}

				if( ! isset( $calendars[ $post->calendarID ]->post_id ) )
				{
					$calendars[ $post->calendarID ]->post_id = array();
				}

				// post_id
				$room = new stdClass();
				$room->ID = $post->ID;
				$room->post_title = $post->post_title;

				$calendars[ $post->calendarID ]->post_id[] = $room;

				// selected
				if( ! isset( $calendars[ $post->calendarID ]->selected ) )
				{
					$calendars[ $post->calendarID ]->selected = array();
				}

				if( $post->selected <= current_time( 'timstamp' ) )
				{
					$time = hotel_block_convert_current_time( $post->selected, 1 ) * 1000;
					if( ! in_array( $time, $calendars[ $post->calendarID ]->selected ) )
					{
						$calendars[ $post->calendarID ]->selected[] = $time;
					}
				}

			}
		}
		else
		{
			$time = time();
			$object = new stdClass();
			$object->id = $time;
			$object->post_id = array();
			$object->selected = array();

			$calendars[ $time ] = $object;
		}

		return $calendars;

	}

	function wp_ajax_hotel_block_delete_post_type()
	{
		if( ! isset( $_REQUEST[ 'schema' ] ) || sanitize_text_field( $_REQUEST[ 'schema' ] ) !== 'hotel-block' )
		{
			wp_send_json( array( 'status' => 'failed', 'message' => __( 'Something went wrong. Please try again!', 'tp-hotel-booking-block' ) ) );
		}

		$calendar = json_decode( file_get_contents( 'php://input' ) );
		if( $calendar_id = $calendar->calendar_id )
		{
			if( get_post( $calendar_id ) && wp_delete_post( $calendar_id ) )
			{
				wp_send_json( array( 'status' => 'success', 'data' => $this->get_blocked(), 'message' => __( 'Remove completed!', 'tp-hotel-booking-block' ) ) );
			}
		}

		wp_send_json( array( 'status' => 'success', 'data' => $this->get_blocked(), 'message' => __( 'Remove completed!', 'tp-hotel-booking-block' ) ) );
	}

	// custom search query
	function search( $query, $param )
	{
		$check_in = isset( $param[ 'check_in' ] ) ? $param[ 'check_in' ] : time();
		$check_out = isset( $param[ 'check_out' ] ) ? $param[ 'check_out' ] : time();
		$adults = isset( $param[ 'adults' ] ) ? (int)$param[ 'adults' ] : hb_get_max_capacity_of_rooms();
		$child = isset( $param[ 'child' ] ) ? (int)$param[ 'child' ] : hb_get_max_child_of_rooms();

		global $wpdb;
	    /**
	     * blocked
	     * @var
	     */
		$blocked = $wpdb->prepare( "
				SELECT COUNT( blocked_time.meta_value )
				FROM $wpdb->postmeta AS blocked_post
				INNER JOIN $wpdb->posts AS calendar ON calendar.ID = blocked_post.meta_value
				INNER JOIN $wpdb->postmeta AS blocked_time ON blocked_time.post_id = calendar.ID
				WHERE
					blocked_post.post_id = rooms.ID
					AND calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked_post.meta_key = %s
					AND blocked_time.meta_key = %s
					AND blocked_time.meta_value >= %d
					AND blocked_time.meta_value <= %d
			", 'hb_blocked', 'publish', 'hb_blocked_id', 'hb_blocked_time', $check_in, $check_out );

	    $not = $wpdb->prepare("
			(
				SELECT COALESCE( SUM( meta.meta_value ), 0 ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
					LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id AND meta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS itemmeta ON order_item.order_item_id = itemmeta.hotel_booking_order_item_id AND itemmeta.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id AND checkin.meta_key = %s
					LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id AND checkout.meta_key = %s
					LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
				WHERE
						itemmeta.meta_value = rooms.ID
					AND (
							( checkin.meta_value >= %d AND checkin.meta_value <= %d )
						OR 	( checkout.meta_value >= %d AND checkout.meta_value <= %d )
						OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
					)
					AND booking.post_type = %s
					AND booking.post_status IN ( %s, %s, %s )
			)
		", 'qty', 'product_id', 'check_in_date', 'check_out_date',
			$check_in, $check_out,
			$check_in, $check_out,
			$check_in, $check_out,
			'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
		);

		$query = $wpdb->prepare("
				SELECT rooms.*, ( number.meta_value - {$not} ) AS available_rooms, ($blocked) AS blocked FROM $wpdb->posts AS rooms
					LEFT JOIN $wpdb->postmeta AS number ON rooms.ID = number.post_id AND number.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = rooms.ID AND pm1.meta_key = %s
					LEFT JOIN {$wpdb->termmeta} term_cap ON term_cap.term_id = pm1.meta_value AND term_cap.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
				WHERE
					rooms.post_type = %s
					AND rooms.post_status = %s
					AND term_cap.meta_value <= %d
					AND pm2.meta_value <= %d
				GROUP BY rooms.post_name
				HAVING ( available_rooms > 0 AND blocked = 0 )
				ORDER BY term_cap.meta_value DESC
			", '_hb_num_of_rooms', '_hb_room_capacity', 'hb_max_number_of_adults', '_hb_max_child_per_room', 'hb_room', 'publish', $adults, $child );

		return $query;
	}

	static function instance()
	{
		if( self::$instance ){
			return self::$instance;
		}

		return self::$instance = new self();
	}

}

Hotel_Booking_Block::instance();