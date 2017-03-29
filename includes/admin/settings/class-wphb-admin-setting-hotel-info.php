<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-01 13:39:08
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WPHB_Admin_Setting_Hotel_Info' ) ) {

	class WPHB_Admin_Setting_Hotel_Info extends WPHB_Admin_Setting_Page {

		public $id = 'hotel_info';

		public $title = null;

		function __construct() {

			$this->title = __( 'Hotel Info', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(

					array(
							'type'			=> 'section_start',
							'id'			=> 'hotel_booking_infomation',
							'title'			=> __( 'Your hotel information','wp-hotel-booking' )
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_name',
							'title'		=> __( 'Hotel Name', 'wp-hotel-booking' ),
							'default'		=> 'Hanoi Daewoo Hotel'
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_address',
							'title'		=> __( 'Hotel Adress', 'wp-hotel-booking' ),
							'default'		=> 'Ha Noi'
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_city',
							'title'		=> __( 'City', 'wp-hotel-booking' ),
							'default'		=> 'Ha Noi'
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_state',
							'title'		=> __( 'State', 'wp-hotel-booking' ),
							'default'		=> 'Hanoi Daewoo Hotel'
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_country',
							'title'		=> __( 'Country', 'wp-hotel-booking' ),
							'default'		=> 'Vietnam'
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_zip_code',
							'title'		=> __( 'Zip / Postal Code', 'wp-hotel-booking' ),
							'default'		=> 10000
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_phone_number',
							'title'		=> __( 'Phone Number', 'wp-hotel-booking' ),
							'default'		=> ''
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_fax_number',
							'title'		=> __( 'Fax', 'wp-hotel-booking' ),
							'default'		=> ''
						),

					array(
							'type'			=> 'text',
							'id'			=> 'tp_hotel_booking_hotel_email_address',
							'title'		=> __( 'Email', 'wp-hotel-booking' ),
							'default'		=> ''
						),

					array(
							'type'			=> 'section_end',
							'id'			=> 'hotel_booking_infomation'
						)

				) );
		}

	}

}
return new WPHB_Admin_Setting_Hotel_Info();