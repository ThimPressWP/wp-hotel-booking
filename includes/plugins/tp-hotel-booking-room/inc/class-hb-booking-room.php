<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-18 15:32:51
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-20 16:37:28
 */

if( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'TP_Hotel_Booking_Room_Extenstion' ) ) {

	class TP_Hotel_Booking_Room_Extenstion {

		function __construct() {
			add_action( 'hb_admin_settings_tab_after', array( $this, 'admin_settings' ) );

			add_action( 'init', array( $this, 'init' ) );
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
				        <th><?php _e( 'Enable book in single room', 'tp-hotel-booking-room' ); ?></th>
				        <td>
				            <input type="hidden" name="<?php echo esc_attr( $settings->get_field_name('enable_single_book') ); ?>" value="0" />
				            <input type="checkbox" name="<?php echo esc_attr( $settings->get_field_name('enable_single_book') ); ?>" <?php checked( $settings->get('enable_single_book') ? 1 : 0, 1 ); ?> value="1" />
				        </td>
				    </tr>
				</table>
			<?php
		}

		function init() {
			if ( ! hb_settings()->get( 'enable_single_book', 0 ) ) {
				return;
			}

			add_action( 'hotel_booking_single_room_title', array( $this, 'single_add_button' ) );
			add_action( 'wp_footer', array( $this, 'wp_footer' ) );
			// enqueue script
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			add_action( 'wp_ajax_check_room_availabel', array( $this, 'check_room_availabel' ) );
			add_action( 'wp_ajax_nopriv_check_room_availabel', array( $this, 'check_room_availabel' ) );

			add_filter( 'hotel_booking_add_to_cart_results', array( $this, 'add_to_cart_redirect' ), 10, 2 );
		}

		function single_add_button() {
			global $hb_room;
			?>
				<a href="#" data-id="<?php echo esc_attr( $hb_room->ID ) ?>" class="hb_button hb_primary" id="hb_room_load_booking_form"><?php _e( 'Book this room', 'tp-hotel-booking-room' ); ?></a>
			<?php
		}

		function wp_footer() {
				global $post;
				if ( ! $post || ! is_single( $post->ID ) || get_post_type( $post->ID ) !== 'hb_room' ) {
					return;
				}
			?>

				<!--Single form-->
				<script type="text/html" id="tmpl-hb-room-load-form">

					<form action="POST" name="hb-search-results" class="hb-search-room-results">

						<div class="hb-booking-room-form-head">
							<h2><?php printf( '%s', $post->post_title ) ?></h2>
						</div>

						<div class="hb-search-results-form-container">
							<div class="hb-booking-room-form-group">
								<label><?php _e( 'Arrival Date', 'tp-hotel-booking-room' ); ?></label>
								<div class="hb-booking-room-form-field">
									<input name="check_in_date" value="{{ data.check_in_date }}" placeholder="<?php _e( 'Select Check in Date', 'tp-hotel-booking-room' ); ?>" />
								</div>
							</div>
							<div class="hb-booking-room-form-group">
								<label><?php _e( 'Departure Date', 'tp-hotel-booking-room' ); ?></label>
								<div class="hb-booking-room-form-field">
									<input name="check_out_date" value="{{ data.check_out_date }}" placeholder="<?php _e( 'Select Check out Date', 'tp-hotel-booking-room' ); ?>" />
								</div>
							</div>
						</div>

						<div class="hb-booking-room-form-footer">
							<input type="hidden" name="room-id" value="<?php printf( '%s', $post->ID ) ?>" />
							<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart"/>
							<input type="hidden" name="is_single" value="1"/>
							<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
							<button type="submit" class="hotel_booking_room_button hb_add_to_cart"><?php _e( 'Select this room', 'tp-hotel-booking-room' ); ?></button>
						</div>
					</form>

				</script>

				<!--Quanity select-->
				<script type="text/html" id="tmpl-hb-room-load-qty">
					<div class="hb-booking-room-form-group">
						<label><?php _e( 'Quantity Available', 'tp-hotel-booking-room' ); ?></label>
						<div class="hb-booking-room-form-field">
							<select name="hb-num-of-rooms" id="hotel_booking_room_qty" class="number_room_select">
								<option value=""><?php _e( '--- Quantity ---', 'tp-hotel-booking-room' ); ?></option>
								<# for( var i = 1; i <= data.qty; i++ ) { #>
									<option value="{{ i }}">{{ i }}</option>
								<# } #>
							</select>
						</div>
					</div>
				</script>

			<?php
		}

		// enqueue script
		function enqueue() {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_register_script( 'magnific-popup', TP_HB_BOOKING_ROOM_URI . 'inc/libraries/magnific-popup/jquery.magnific-popup.min.js', array(), false, true );
			wp_enqueue_script( 'magnific-popup' );

			wp_register_style( 'magnific-popup', TP_HB_BOOKING_ROOM_URI . 'inc/libraries/magnific-popup/magnific-popup.css', array(), false, true );
			wp_enqueue_style( 'magnific-popup' );

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

		function add_to_cart_redirect( $param, $room ) {
			if( isset( $param['status'] ) && $param['status'] === 'success' ) {
				$param['redirect']	= hb_get_cart_url();
			}

			return $param;
		}

	}

	new TP_Hotel_Booking_Room_Extenstion();

}
