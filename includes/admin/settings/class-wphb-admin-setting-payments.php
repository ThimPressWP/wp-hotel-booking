<?php
/**
 * WP Hotel Booking admin setting payments.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Setting_Payments' ) ) {

	class WPHB_Admin_Setting_Payments extends WPHB_Admin_Setting_Page {

		public $id = 'payments';

		public $title = null;

		function __construct() {

			$this->title = __( 'Checkout', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters(
				'hotel_booking_admin_setting_fields_' . $this->id,
				array(

					array(
						'type'  => 'section_start',
						'id'    => 'payment_general_setting',
						'title' => __( 'Payment General Options', 'wp-hotel-booking' ),
						'desc'  => __( 'Payment General options for system.', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'number',
						'id'      => 'tp_hotel_booking_cancel_payment',
						'title'   => __( 'Cancel Payment', 'wp-hotel-booking' ),
						'desc'    => __( 'Cancel Payment after hour(s)', 'wp-hotel-booking' ),
						'default' => 12,
						'step'    => 0.1,
						'min'     => 0.1,
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_guest_checkout',
						'title'   => __( 'Process', 'wp-hotel-booking' ),
						'desc'    => __( 'Enable the option to allow guests checkout.', 'wp-hotel-booking' ),
						'default' => 1,
					),

					array(
						'type' => 'section_end',
						'id'   => 'payment_general_setting',
					),

				)
			);
		}

		public function output() {
			$current_section = null;

			if ( isset( $_REQUEST['section'] ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			$payments = hb_get_payment_gateways();
			if ( $current_section && $current_section !== 'general' ) {
				foreach ( $payments as $payment ) {
					if ( $payment->slug === $current_section ) {
						$payment->admin_settings();
						break;
					}
				}
			} else {
				parent::output();
			}
		}

		public function get_sections() {
			$sections            = array();
			$sections['general'] = __( 'General', 'wp-hotel-booking' );

			$payments = hb_get_payment_gateways();
			foreach ( $payments as $payment ) {
				$sections[ $payment->slug ] = $payment->title;
			}
			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, $sections );
		}

	}

}
return new WPHB_Admin_Setting_Payments();
