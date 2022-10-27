<?php
/**
 * WP Hotel Booking GDPR.
 *
 * @version       1.9.7
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
/**
 * Class WPHB_Personal_Data
 */
class WPHB_Personal_Data {

	/**
	 * WPHB_Personal_Data constructor.
	 */
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_booking_personal_data_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_booking_personal_data_eraser' ) );
	}

	/**
	 * @param $exporters
	 *
	 * @return mixed
	 */
	public function register_booking_personal_data_exporter( $exporters ) {
		$exporters['wphb-booking'] = array(
			'exporter_friendly_name' => __( 'WPHB Booking', 'wp-hotel-booking' ),
			'callback'               => array( $this, 'exporter_personal_data' ),
		);

		return $exporters;
	}

	public function register_booking_personal_data_eraser( $erasers ) {
		$erasers['wphb-booking'] = array(
			'eraser_friendly_name' => __( 'WPHB Booking', 'wp-hotel-booking' ),
			'callback'             => array( $this, 'eraser_personal_data' ),
		);

		return $erasers;
	}

	/**
	 * @param $email_address
	 * @param int           $page
	 *
	 * @return array
	 */
	public function exporter_personal_data( $email_address, $page = 1 ) {

		$data_to_export = array();

		$user = get_user_by( 'email', $email_address );
		if ( false === $user ) {
			return array(
				'data' => $data_to_export,
				'done' => true,
			);
		}

		$bookings = $this->_query_booking( $email_address );

		foreach ( $bookings as $booking_id ) {
			$booking  = WPHB_Booking::instance( $booking_id );
			$total    = $booking->total();
			$currency = $booking->payment_currency;
			$method   = hb_get_user_payment_method( $booking->method );
			$rooms    = hb_get_order_items( $booking_id );

			$items = '';
			foreach ( $rooms as $room ) {
				$items .= $room->order_item_name . ' (x' . hb_get_order_item_meta( $room->order_item_id, 'qty', true ) . ') ' . date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ) . ' - ' . date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ) ) . "\n";
			};

			$customer_details = hb_get_customer_fullname( $booking_id, true )
								. ' - Email: ' . $booking->customer_email
								. ' - Phone: ' . $booking->customer_phone
								. ' - Address: ' . $booking->customer_address . ', ' . $booking->customer_city . ', ' . $booking->customer_state . ', ' . $booking->customer_country
								. ' - Postal code: ' . $booking->customer_postal_code;

			$post_data_to_export = array(
				array(
					'name'  => __( 'ID', 'wp-hotel-booking' ),
					'value' => hb_format_order_number( $booking_id ),
				),
				array(
					'name'  => __( 'Created Date', 'wp-hotel-booking' ),
					'value' => get_the_date( get_option( 'date_format' ), $booking_id ),
				),
				array(
					'name'  => __( 'Customer Details', 'wp-hotel-booking' ),
					'value' => $customer_details,
				),
				array(
					'name'  => __( 'Items', 'wp-hotel-booking' ),
					'value' => nl2br( $items ),
				),
				array(
					'name'  => __( 'Total', 'wp-hotel-booking' ),
					'value' => hb_format_price( $total, hb_get_currency_symbol( $currency ) ),
				),
				array(
					'name'  => __( 'Payment Method', 'wp-hotel-booking' ),
					'value' => $method->title,
				),
				array(
					'name'  => __( 'Status', 'wp-hotel-booking' ),
					'value' => hb_get_booking_status_label( $booking_id ),
				),
			);

			$data_to_export[] = array(
				'group_id'    => 'hb_booking',
				'group_label' => __( 'Hotel Booking', 'wp-hotel-booking' ),
				'item_id'     => "post-{$booking_id}",
				'data'        => $post_data_to_export,
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * @param $email_address
	 * @param int           $page
	 *
	 * @return array
	 */
	public function eraser_personal_data( $email_address, $page = 1 ) {
		$eraser_data = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => 1,
		);

		if ( ! $wp_user = get_user_by( 'email', $email_address ) ) {
			return $eraser_data;
		}

		$bookings = $this->_query_booking( $email_address );
		foreach ( $bookings as $booking_id ) {
			$this->_eraser_booking_data( $booking_id );
		}
		$eraser_data['items_removed'] = true;

		return $eraser_data;
	}

	/**
	 * @param $user_email
	 *
	 * @return array|null|object
	 */
	private function _query_booking( $user_email ) {

		if ( ! $user_email ) {
			return array();
		}

		global $wpdb;

		$booking = array();
		$query   = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT booking.ID FROM {$wpdb->prefix}posts AS booking 
				INNER JOIN {$wpdb->prefix}postmeta AS booking_meta ON booking.ID = booking_meta.post_id
				WHERE 
				booking.post_type = %s AND booking_meta.meta_key = %s AND booking_meta.meta_value = %s",
				'hb_booking',
				'_hb_customer_email',
				$user_email
			),
			ARRAY_A
		);

		if ( $query ) {
			foreach ( $query as $item ) {
				$booking[] = $item['ID'];
			}
		}

		return $booking;
	}

	/**
	 * @param $booking_id
	 */
	private function _eraser_booking_data( $booking_id ) {
		$data = array(
			'customer_title',
			'customer_first_name',
			'customer_last_name',
			'customer_address',
			'customer_city',
			'customer_state',
			'customer_postal_code',
			'customer_fax',
			'customer_phone',
			'customer_country',
			'customer_email',
		);

		$prefix = '_hb_';
		foreach ( $data as $_data ) {
			update_post_meta( $booking_id, $prefix . $_data, '' );
		}
	}
}

new WPHB_Personal_Data();
