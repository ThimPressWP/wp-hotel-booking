<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-14 09:37:57
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WPHB_Admin_Setting_Payments' ) ) {

	class WPHB_Admin_Setting_Payments extends WPHB_Admin_Setting_Page {

		public $id = 'payments';

		public $title = null;

		function __construct() {

			$this->title = __( 'Checkout', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'payment_general_setting',
							'title'		=> __( 'Payment General Options', 'wp-hotel-booking' ),
							'desc'		=> __( 'Payment General options for system.', 'wp-hotel-booking' )
						),

					array(
							'type'		=> 'number',
							'id'		=> 'tp_hotel_booking_cancel_payment',
							'title'		=> __( 'Cancel Payment', 'wp-hotel-booking' ),
							'desc'		=> __( 'Cancel Payment after hour(s)', 'wp-hotel-booking' ),
							'default'	=> 12,
							'min'		=> 1,
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'tp_hotel_booking_guest_checkout',
							'title'		=> __( 'Process', 'wp-hotel-booking' ),
							'desc'		=> __( 'Allows customers to checkout without creating an account.', 'wp-hotel-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'payment_general_setting'
						)

				) );
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
			$sections = array();
			$sections['general'] = __( 'General' );

			$payments = hb_get_payment_gateways();
			foreach( $payments as $payment ) {
				$sections[$payment->slug] = $payment->title;
			}
			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, $sections );
		}

	}

}
return new WPHB_Admin_Setting_Payments();