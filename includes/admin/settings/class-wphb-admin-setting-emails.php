<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-14 14:54:23
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WPHB_Admin_Setting_Emails' ) ) {
	class WPHB_Admin_Setting_Emails extends WPHB_Admin_Setting_Page {

		public $id = 'emails';

		public $title = null;

		function __construct() {

			$this->title = __( 'Emails', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			$section  = 'email-options';
			$sections = $this->get_sections();
			if ( isset( $_REQUEST['section'] ) && array_key_exists( $_REQUEST['section'], $sections ) ) {
				$section = sanitize_text_field( $_REQUEST['section'] );
			}

			$settings = array(
				array(
					'type'  => 'section_start',
					'id'    => 'email_options',
					'title' => __( 'Email Sender', 'wp-hotel-booking' ),
					'desc'  => __( 'The name and email address of the sender displays in email', 'wp-hotel-booking' )
				),

				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_general_from_name',
					'title'       => __( 'From name', 'wp-hotel-booking' ),
					'default'     => get_option( 'blogname' ),
					'placeholder' => get_option( 'blogname' )
				),

				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_general_from_email',
					'title'       => __( 'From Email', 'wp-hotel-booking' ),
					'default'     => get_option( 'admin_email' ),
					'placeholder' => get_option( 'admin_email' )
				),

				array(
					'type'        => 'text',
					'id'          => 'tp_hotel_booking_email_general_subject',
					'title'       => __( 'Email subject', 'wp-hotel-booking' ),
					'default'     => __( 'Reservation', 'wp-hotel-booking' ),
					'placeholder' => __( 'Reservation', 'wp-hotel-booking' )
				),

				array(
					'type' => 'section_end',
					'id'   => 'email_options'
				)
			);
			if ( $section === 'new-booking' ) {
				$settings = array(
					array(
						'type'  => 'section_start',
						'id'    => 'new_booking',
						'title' => __( 'New Booking', 'wp-hotel-booking' ),
						'desc'  => __( 'New booking emails are sent to chosen recipient(s) when a booking is received.', 'wp-hotel-booking' )
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_email_new_booking_enable',
						'title'   => __( 'Enable', 'wp-hotel-booking' ),
						'default' => 1,
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_recipients',
						'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => get_option( 'admin_email' ),
						'placeholder' => get_option( 'admin_email' )
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_subject',
						'title'       => __( 'Subject', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => '[{site_title}] Reservation completed ({booking_number}) - {booking_date}',
						'placeholder' => '[{site_title}] Reservation completed ({booking_number}) - {booking_date}'
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_heading',
						'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
						'desc'        => __( 'The main heading displays in the top of email. Default heading: New customer booking', 'wp-hotel-booking' ),
						'default'     => 'New Booking Payment',
						'placeholder' => 'New Booking Payment'
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_new_booking_heading_desc',
						'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
						'default'     => __( 'The customer has completed the transaction', 'wp-hotel-booking' ),
						'placeholder' => __( 'The customer has completed the transaction', 'wp-hotel-booking' )
					),

					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_email_new_booking_format',
						'title'   => __( 'Email Format', 'wp-hotel-booking' ),
						'default' => 'html',
						'options' => array(
							'plain' => __( 'Plain Text', 'wp-hotel-booking' ),
							'html'  => __( 'HTML', 'wp-hotel-booking' )
						)
					),

					array(
						'type' => 'section_end',
						'id'   => 'new_booking'
					)
				);
			}

			if ( $section === 'cancel-booking' ) {
				$settings = array(
					array(
						'type'  => 'section_start',
						'id'    => 'cancel_booking',
						'title' => __( 'Cancel Booking', 'wp-hotel-booking' ),
						'desc'  => __( 'Cancel booking emails are sent to chosen recipient(s) when booking has been marked cancelled.', 'wp-hotel-booking' )
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_email_cancel_booking_enable',
						'title'   => __( 'Enable', 'wp-hotel-booking' ),
						'default' => 1,
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_recipients',
						'title'       => __( 'Recipient(s)', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => get_option( 'admin_email' ),
						'placeholder' => get_option( 'admin_email' )
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_subject',
						'title'       => __( 'Subject', 'wp-hotel-booking' ),
						'desc'        => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wp-hotel-booking' ), get_option( 'admin_email' ) ),
						'default'     => '[{site_title}] Cancelled Reservation ({booking_number}) - {booking_date}',
						'placeholder' => '[{site_title}] Cancelled Reservation  ({booking_number}) - {booking_date}'
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_heading',
						'title'       => __( 'Email Heading', 'wp-hotel-booking' ),
						'desc'        => __( 'The main heading displays in the top of email. Default heading: Cancelled booking', 'wp-hotel-booking' ),
						'default'     => 'Cancelled booking',
						'placeholder' => 'Cancelled booking'
					),

					array(
						'type'        => 'text',
						'id'          => 'tp_hotel_booking_email_cancel_booking_heading_desc',
						'title'       => __( 'Email Heading Description', 'wp-hotel-booking' ),
						'default'     => __( 'Booking has been marked cancelled', 'wp-hotel-booking' ),
						'placeholder' => __( 'Booking has been marked cancelled', 'wp-hotel-booking' )
					),

					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_email_cancel_booking_format',
						'title'   => __( 'Email Format', 'wp-hotel-booking' ),
						'default' => 'html',
						'options' => array(
							'plain' => __( 'Plain Text', 'wp-hotel-booking' ),
							'html'  => __( 'HTML', 'wp-hotel-booking' )
						)
					),

					array(
						'type' => 'section_end',
						'id'   => 'cancel_booking'
					)
				);
			}

			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, $settings, $section );
		}

		public function get_sections() {
			$sections = array(
				'email-options'  => __( 'Email Options', 'wp-hotel-booking' ),
				'new-booking'    => __( 'New Booking', 'wp-hotel-booking' ),
				'cancel-booking' => __( 'Cancel Booking', 'wp-hotel-booking' )
			);

			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, $sections );
		}

	}

}
return new WPHB_Admin_Setting_Emails();