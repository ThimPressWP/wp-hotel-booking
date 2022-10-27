<?php
/**
 * WP Hotel Booking room available.
 *
 * @version       1.9.6
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
 * Class WPHB_Room_Booking_Available
 */
class WPHB_Room_Booking_Available {

	/**
	 * Hold the instance of main class
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Construction
	 */
	public function __construct() {
		add_action( 'delete_post', array( $this, 'remove_order' ), 10, 1 );
		add_action( 'save_post', array( $this, 'wphb_compare_num_of_rooms' ), 10, 1 );
		add_action( 'hb_booking_status_changed', array( $this, 'order_changes_status' ), 10, 3 );
	}

	/**
	 * Compare room booked if change status order booking room
	 */
	public function order_changes_status( $booking_id = null, $old_status = null, $new_status = null ) {
		if ( ! ( $booking_id || $new_status ) ) {
			return;
		}

		$order_items = hb_get_order_items( $booking_id );

		if ( ! empty( $order_items ) ) {
			foreach ( $order_items as $item ) {
				$room_id = hb_get_order_item_meta( $item->order_item_id, 'product_id', true );
				if ( $room_id ) {
					$date_booked = WPHB_Room::instance( $room_id )->get_dates_booked();
					if ( ! empty( $date_booked ) ) {
						if ( $old_status == 'completed' && $new_status != 'completed' ) {
							$date_booked[ $booking_id ]['status'] = 'hb-' . $new_status;
						} elseif ( $old_status != 'completed' && $new_status == 'completed' ) {
							$date_booked[ $booking_id ]['status'] = 'hb-completed';
						}
						update_post_meta( $room_id, '_hb_dates_booked', $date_booked );
					}
					$this->calculate_dates_available( $room_id );
				}
			}
		}
	}

	/**
	 * compare if _hb_num_of_rooms change use booking room
	 */
	public function wphb_compare_num_of_rooms( $room_id ) {
		if ( empty( $room_id ) ) {
			return;
		}

		$post_type = get_post_type( $room_id );
		if ( $post_type != 'hb_room' ) {
			return;
		}

		if ( empty( $_POST['wphb_meta_box_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wphb_meta_box_nonce'] ), 'wphb_update_meta_box' ) ) {
			return;
		}

		$this->calculate_dates_available( $room_id );
	}

	/**
	 * It removes the booking from the room's availability when the order is removed
	 *
	 * @param int $booking_id The ID of the booking that was just created.
	 *
	 * @return void
	 */
	public function remove_order( $booking_id ) {
		if ( get_post_type( $booking_id ) != 'hb_booking' ) {
			return;
		}

		$order_items = hb_get_order_items( $booking_id );

		if ( ! empty( $order_items ) ) {
			foreach ( $order_items as $item ) {
				$room_id = hb_get_order_item_meta( $item->order_item_id, 'product_id', true );
				if ( $room_id ) {
					$room_order_booked = WPHB_Room::instance( $room_id )->get_dates_booked();
					if ( ! empty( $room_order_booked ) ) {
						unset( $room_order_booked[ $booking_id ] );
						update_post_meta( $room_id, '_hb_dates_booked', $room_order_booked );
						$this->calculate_dates_available( $room_id );
					}
				}
			}
		}
	}

	/**
	 * Add or remove the remaining number of rooms per day of each room
	 * Lock the room when out of stock
	 *
	 * @param int $room_id room_id The ID of the booking.
	 *
	 * @return void
	 */
	public function calculate_dates_available( int $room_id = 0 ) {
		if ( ! $room_id ) {
			return;
		}
		try {
			$current_date = strtotime( gmdate( 'Y-m-d' ) );
			$date_booked  = WPHB_Room::instance( $room_id )->get_dates_booked();
			$num_of_room  = absint( WPHB_Room::instance( $room_id )->num_of_rooms );

			if ( ! empty( $_POST['_hb_num_of_rooms'] ) ) {
				$num_of_room = $_POST['_hb_num_of_rooms'];
			}

			$dates_available = array();
			if ( ! empty( $date_booked ) ) {
				foreach ( $date_booked as $order_id => $data_order ) {
					if ( isset( $data_order['dates_booked'] ) ) {
						foreach ( $data_order['dates_booked'] as $key => $date ) {
							if ( $date < $current_date ) {
								unset( $date_booked[ $order_id ]['dates_booked'][ $key ] );
								continue;
							}
							if ( isset( $data_order['status'] ) && $data_order['status'] == 'hb-completed' ) {
								if ( isset( $data_order['quantity'] ) && $num_of_room >= $data_order['quantity'] ) {
									$dates_available[ $date ] = absint( $num_of_room - $data_order['quantity'] );
								}
							} else {
								$dates_available[ $date ] = absint( $num_of_room );
							}
						}
					}
				}

				update_post_meta( $room_id, '_hb_dates_booked', $date_booked );
			}

			update_post_meta( $room_id, '_hb_dates_available', $dates_available );

		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

WPHB_Room_Booking_Available::instance();
