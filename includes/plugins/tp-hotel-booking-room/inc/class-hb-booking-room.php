<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-18 15:32:51
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-19 10:09:25
 */

if( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'TP_Hotel_Booking_Room_Extenstion' ) ) {

	class TP_Hotel_Booking_Room_Extenstion {

		function __construct() {
			add_action( 'hb_admin_settings_tab_after', array( $this, 'admin_settings' ) );

			add_action( 'hotel_booking_single_room_title', array( $this, 'single_add_button' ) );
			// enqueue script
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			// add_action( 'hotel_booking_single_room_gallery', array( $this, 'booking_form' ) );

			add_action( 'wp_ajax_check_room_availabel', array( $this, 'check_room_availabel' ) );
			add_action( 'wp_ajax_nopriv_check_room_availabel', array( $this, 'check_room_availabel' ) );

			add_filter( 'hotel_booking_add_to_cart_results', array( $this, 'add_to_cart_redirect' ), 10, 2 );
		}

		// add admin setting
		function admin_settings( $tab ) {
			if ( $tab !== 'room' ) {
				return;
			}

			$settings = hb_settings();
			?>
				<table class="form-table">
				    <tr>
				        <th><?php _e( 'Enable book in single room', 'tp-hotel-booking' ); ?></th>
				        <td>
				            <input type="hidden" name="<?php echo esc_attr( $settings->get_field_name('enable_single_book') ); ?>" value="0" />
				            <input type="checkbox" name="<?php echo esc_attr( $settings->get_field_name('enable_single_book') ); ?>" <?php checked( $settings->get('enable_single_book') ? 1 : 0, 1 ); ?> value="1" />
				        </td>
				    </tr>
				</table>
			<?php
		}

		function single_add_button() {
			if ( ! hb_settings()->get( 'enable_single_book', 0 ) ) {
				return;
			}

			global $hb_room;

			?>
				<form>

					<input type="hidden" name="room_id" value="<?php echo esc_attr( $hb_room->ID ) ?>" />

					<div class="hb_book_room_form">
						<button class="hb_buton hb_primary"><?php _e( 'Book this room' ); ?></button>
					</div>
				</form>
			<?php
		}

		// enqueue script
		function enqueue() {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'tp-hotel-booking-room', TP_HB_BOOKING_ROOM_URI . 'assets/css/site.css' );

			wp_enqueue_script( 'tp-hotel-booking-room', TP_HB_BOOKING_ROOM_URI . 'assets/js/site.js' );
		}

		function check_room_availabel() {
			// ajax referer
			if ( ! isset( $_POST['check-room-availabel-nonce'] ) || ! check_ajax_referer( 'check_room_availabel_nonce', 'check-room-availabel-nonce' ) ) {
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
				wp_send_json( array( 'status' => true, 'qty' => $available, 'check_in_date' => date( 'm/d/Y', $check_in_date ), 'check_out_date' => date( 'm/d/Y', $check_out_date ) ) ); die();
			} else {
				wp_send_json( array( 'status' => false, 'message' => $available->get_error_message() ) ); die();
			}

			wp_send_json( array( 'status' => false, 'message' => __( 'No room found.', 'tp-hotel-booking-room' ) ) ); die();
		}

		// book room layout
		function booking_form() {
			ob_start();
			global $hb_room;
		?>
			<div class="hotel_booking_room_detail">
				<h3 id="hbr-nav">
					<a href="#hbr-check-date"><?php _e( 'Step 1: Check in - Check out date', 'tp-hotel-booking-room' ); ?></a>
					<a href="#hbr-quantity"><?php _e( 'Step 2: Quantity', 'tp-hotel-booking-room' ); ?></a>
				</h3>
				<form name="hotel_booking_room_check_available" method="POST" class="hbr-add-to-cart-step hotel_booking_room_check_available" id="hbr-check-date">
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
							<input type="hidden" name="hotel_booking_room_id" value="<?php printf( '%s', $hb_room->ID ) ?>" />
							<input type="hidden" name="action" value="check_room_availabel"/>
							<?php wp_nonce_field( 'check_room_availabel_nonce', 'check-room-availabel-nonce' ); ?>
							<button type="submit" id="hotel_booking_room_check_avibility" class="hotel_booking_room_button"><?php _e( 'Check Available', 'tp-hotel-booking-room' ); ?></button>
						</li>
					</ul>
				</form>
				<form name="hb-search-results" class="hbr-add-to-cart-step hb-search-room-results" id="hbr-quantity">
					<ul>
						<li>
							<input type="hidden" name="room-id" value="<?php printf( '%s', $hb_room->ID ) ?>" />
							<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart"/>
							<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
							<button type="submit" class="hotel_booking_room_button hb_add_to_cart"><?php _e( 'Select this room', 'tp-hotel-booking-room' ); ?></button>
						</li>
					</ul>
				</form>
				<div class="hotel_booking_room_overflow">
					<div class="hotel_booking_room_overflow_spinner">
						<div class="hbr_spinner_ball hbr_spinner_ball-1"></div>
						<div class="hbr_spinner_ball hbr_spinner_ball-2"></div>
						<div class="hbr_spinner_ball hbr_spinner_ball-3"></div>
						<div class="hbr_spinner_ball hbr_spinner_ball-4"></div>
					</div>
				</div>
			</div>

			<script type="text/html" id="tmpl-hotel-booking-select-qty">
				<li>
					<label for="hb-num-of-rooms"><?php _e( 'Quantity:' ); ?></label>
					<select name="hb-num-of-rooms" id="hotel_booking_room_qty" class="number_room_select">
						<option value=""><?php _e( '--- Quantity ---', 'tp-hotel-booking-room' ); ?></option>
						<# for( var i = 1; i <= data.qty; i++ ) { #>
							<option value="{{ i }}">{{ i }}</option>
						<# } #>
					</select>
				</li>
				<li>
					<input type="hidden" name="check_in_date" value="{{ data.check_in_date }}" />
					<input type="hidden" name="check_out_date" value="{{ data.check_out_date }}" />
				</li>
				<li>
					<?php do_action( 'hotel_booking_after_add_room_to_cart_form', $hb_room->ID ); ?>
				</li>
			</script>
		<?php

			echo ob_get_clean();
		}

		function add_to_cart_redirect( $param, $room ) {
			if( isset( $param['status'] ) && $param['status'] === 'success' ) {
				$param['redirect']	= hb_get_cart_url();
			}

			return $param;
		}

	}

	new TP_Hotel_Booking_Room_Extenstion();

}
