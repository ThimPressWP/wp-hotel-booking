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

if ( ! class_exists( 'HB_Admin_Setting_Emails' ) ) {
	class HB_Admin_Setting_Emails extends HB_Admin_Setting_Page {

		public $id = 'emails';

		public $title = null;

		function __construct() {

			$this->title = __( 'Emails', 'tp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			$section = 'email-options';
			$sections = $this->get_sections();
			if ( isset( $_REQUEST['section'] ) && array_key_exists( $_REQUEST['section'], $sections ) ) {
				$section = sanitize_text_field( $_REQUEST['section'] );
			}

			$settings = array(
					array(
							'type'		=> 'section_start',
							'id'		=> 'email_options',
							'title'		=> __( 'Email Sender', 'tp-hotel-booking' ),
							'desc'		=> __( 'The name and email address of the sender displays in email', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_general_from_name',
							'title'		=> __( 'From name', 'tp-hotel-booking' ),
							'default'	=> get_option( 'blogname' ),
							'placeholder'	=> get_option( 'blogname' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_general_from_email',
							'title'		=> __( 'From Email', 'tp-hotel-booking' ),
							'default'	=> get_option( 'admin_email' ),
							'placeholder'	=> get_option( 'admin_email' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_general_subject',
							'title'		=> __( 'Email subject', 'tp-hotel-booking' ),
							'default'	=> __( 'Reservation', 'tp-hotel-booking' ),
							'placeholder'	=> __( 'Reservation', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'email_options'
						)
				);
			if ( $section === 'new-booking' ) {
				$settings = array(
					array(
							'type'		=> 'section_start',
							'id'		=> 'new_booking',
							'title'		=> __( 'New Booking', 'tp-hotel-booking' ),
							'desc'		=> __( 'New booking emails are sent to user admin when a booking is received.', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'tp_hotel_booking_email_new_booking_enable',
							'title'		=> __( 'Enable', 'tp-hotel-booking' ),
							'default'	=> 1,
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_new_booking_recipients',
							'title'		=> __( 'Recipient(s)', 'tp-hotel-booking' ),
							'desc'		=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'tp-hotel-booking' ), get_option( 'admin_email' ) ),
							'default'	=> get_option( 'admin_email' ),
							'placeholder'	=> get_option( 'admin_email' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_new_booking_subject',
							'title'		=> __( 'Subject', 'tp-hotel-booking' ),
							'desc'		=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'tp-hotel-booking' ), get_option( 'admin_email' ) ),
							'default'	=> '[{site_title}] New customer booking ({booking_number}) - {booking_date}',
							'placeholder'	=> '[{site_title}] New customer booking ({booking_number}) - {booking_date}'
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_new_booking_heading',
							'title'		=> __( 'Email Heading', 'tp-hotel-booking' ),
							'desc'		=> __( 'The main heading displays in the top of email. Default heading: New customer booking', 'tp-hotel-booking' ),
							'default'	=> 'New customer booking',
							'placeholder'	=> 'New customer booking'
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_email_new_booking_heading_desc',
							'title'		=> __( 'Email Heading Description', 'tp-hotel-booking' ),
							'default'	=> __( 'Reservated', 'tp-hotel-booking' ),
							'placeholder'	=> __( 'Reservated', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'select',
							'id'		=> 'tp_hotel_booking_email_new_booking_format',
							'title'		=> __( 'Email Format', 'tp-hotel-booking' ),
							'desc'		=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'tp-hotel-booking' ), get_option( 'admin_email' ) ),
							'default'	=> 'html',
							'options'	=> array(
									'plain'		=> __( 'Plain Text', 'tp-hotel-booking' ),
									'html'		=> __( 'HTML', 'tp-hotel-booking' )
								)
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'new_booking'
						)
				);
			}
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, $settings, $section );
		}

		public function get_sections() {
			$sections = array(
					'email-options'		=> __( 'Email Options', 'tp-hotel-booking' ),
					'new-booking'		=> __( 'New Booking', 'tp-hotel-booking' ),
				);
			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, $sections );
		}

	}

}
return new HB_Admin_Setting_Emails();