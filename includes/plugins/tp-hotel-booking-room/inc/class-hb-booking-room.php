<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-18 15:32:51
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-21 16:47:20
 */

if( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'TP_Hotel_Booking_Room_Extenstion' ) ) {

	class TP_Hotel_Booking_Room_Extenstion {

		function __construct() {
			// enqueue script
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			add_filter( 'hotel_booking_single_room_infomation_tabs', array( $this, 'filter_tabs' ) );

			add_action( 'wp_ajax_check_room_availabel', array( $this, 'check_room_availabel' ) );
			add_action( 'wp_ajax_nopriv_check_room_availabel', array( $this, 'check_room_availabel' ) );
		}

		// enqueue script
		function enqueue() {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'tp-hotel-booking-room', TP_HB_BOOKING_ROOM_URI . 'assets/css/site.css' );

			wp_enqueue_script( 'tp-hotel-booking-room', TP_HB_BOOKING_ROOM_URI . 'assets/js/site.js' );
		}

		function check_room_availabel() {
			// ajax referer
			if ( ! isset( $_POST['check-room-availabel-nonce'] ) || ! check_ajax_referer( 'check_room_availabel_nonce', 'check-room-availabel-nonce' ) ) {echo 2; die();
				return;
			}

			$room_id = false;
			if ( isset( $_POST['hotel_booking_room_id'] ) && is_numeric( $_POST['hotel_booking_room_id'] ) ) {
				$room_id = absint( $_POST['hotel_booking_room_id'] );
			}

			$check_in_date = isset( $_POST['hotel_booking_room_check_in_timestamp'] ) ? sanitize_text_field( $_POST['hotel_booking_room_check_in_timestamp'] ) : '';
			$check_in_date = $check_in_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

			$check_out_date = isset( $_POST['hotel_booking_room_check_out_timestamp'] ) ? sanitize_text_field( $_POST['hotel_booking_room_check_out_timestamp'] ) : '';
			$check_out_date = $check_out_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

			$args = apply_filters( 'hotel_booking_query_room_available', array( 'room_id' => $room_id, 'check_in_date' => $check_in_date, 'check_out_date' => $check_out_date ) );
			// get available room qty
			$available = hotel_booking_get_qty( $args );

			if ( ! is_wp_error( $available ) ) {
				wp_send_json( array( 'status' => true, 'qty' => $available ) ); die();
			} else {
				wp_send_json( array( 'status' => false, 'message' => $available->get_error_message() ) ); die();
			}

			wp_send_json( array( 'status' => false, 'message' => __( 'No room found.', 'tp-hotel-booking-room' ) ) ); die();
		}

		// add single tab
		function filter_tabs( $tabs ) {
			$tabs[] = array(
				'id'        => 'hb_room_book',
	            'title'     => __( 'Book this room', 'tp-hotel-booking-room' ),
	            'content'   => $this->booking_form()
            );
			return $tabs;
		}

		// book room layout
		function booking_form() {
			ob_start();
			global $hb_room;
			$qty = $hb_room->num_of_rooms;
		?>
			<form name="hotel_booking_add_room_to_cart" method="POST" class="hotel_booking_add_room_to_cart">
				<ul>

					<li>
						<input type="text" name="hotel_booking_room_check_in" class="hotel_booking_room_check_in" placeholder="<?php esc_attr_e( 'Checkin', 'tp-hotel-booking-room' ); ?>" />
						<input type="hidden" name="hotel_booking_room_check_in_timestamp" class="hotel_booking_room_check_in_timestamp" />
					</li>

					<li>
						<input type="text" name="hotel_booking_room_check_out" class="hotel_booking_room_check_out" placeholder="<?php esc_attr_e( 'Checkout', 'tp-hotel-booking-room' ); ?>"  />
						<input type="hidden" name="hotel_booking_room_check_out_timestamp" class="hotel_booking_room_check_out_timestamp" />
					</li>

					<li>
						<a href="#" id="hotel_booking_room_check_avibility" class="hotel_booking_room_button"><?php _e( 'Check Available', 'tp-hotel-booking-room' ); ?></a>
					</li>

					<li>
						<input type="hidden" name="hotel_booking_room_id" value="<?php printf( '%s', $hb_room->ID ) ?>" />
						<input type="hidden" name="action" value="check_room_availabel"/>
						<?php wp_nonce_field( 'check_room_availabel_nonce', 'check-room-availabel-nonce' ); ?>
						<button type="submit" form="hotel_booking_add_room_to_cart" class="hotel_booking_room_button"><?php _e( 'Select this room', 'tp-hotel-booking-room' ); ?></button>
					</li>
				</ul>
				<div class="hotel_booking_room_overflow">
					<div class="hotel_booking_room_overflow_spinner">
						<div class="hbr_spinner_ball hbr_spinner_ball-1"></div>
						<div class="hbr_spinner_ball hbr_spinner_ball-2"></div>
						<div class="hbr_spinner_ball hbr_spinner_ball-3"></div>
						<div class="hbr_spinner_ball hbr_spinner_ball-4"></div>
					</div>
				</div>
			</form>

			<script type="text/html" id="tmpl-hote-booking-select-qty">
				<select name="hotel_booking_room_qty">
					<option value="0"><?php _e( '---Quantity---', 'tp-hotel-booking-room' ); ?></option>
					<# for( var i = 1; i <= data.qty; i++ ) { #>
						<# var qty = data.qty[i-1]; #>
						<option value="{{ qty }}">{{ qty }}</option>
					<# } #>
				</select>
			</script>
		<?php

			return ob_get_clean();
		}

	}

	new TP_Hotel_Booking_Room_Extenstion();

}
