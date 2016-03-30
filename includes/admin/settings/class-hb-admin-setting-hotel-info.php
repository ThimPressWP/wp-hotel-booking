<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 11:00:02
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HB_Admin_Setting_Hotel_Info extends HB_Admin_Setting_Page {

	public $id = 'hotel_info';

	public $title = null;

	function __construct() {

		$this->title = __( 'Hotel Info', 'tp-hotel-booking' );

		parent::__construct();
	}

	public function get_settings() {
		return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(

				array(
						'type'			=> 'section_start',
						'id'			=> 'hotel_booking_infomation',
						'title'			=> __( 'Your hotel information','tp-hotel-booking' )
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_name',
						'title'		=> __( 'Hotel Name', 'tp-hotel-booking' ),
						'default'		=> 'Hanoi Daewoo Hotel'
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_address',
						'title'		=> __( 'Hotel Adress', 'tp-hotel-booking' ),
						'default'		=> 'Ha Noi'
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_city',
						'title'		=> __( 'City', 'tp-hotel-booking' ),
						'default'		=> 'Ha Noi'
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_state',
						'title'		=> __( 'State', 'tp-hotel-booking' ),
						'default'		=> 'Hanoi Daewoo Hotel'
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_country',
						'title'		=> __( 'Country', 'tp-hotel-booking' ),
						'default'		=> 'Vietnam'
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_zip_code',
						'title'		=> __( 'Zip / Postal Code', 'tp-hotel-booking' ),
						'default'		=> 10000
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_phone_number',
						'title'		=> __( 'Phone Number', 'tp-hotel-booking' ),
						'default'		=> ''
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_fax_number',
						'title'		=> __( 'Fax', 'tp-hotel-booking' ),
						'default'		=> ''
					),

				array(
						'type'			=> 'text',
						'id'			=> 'tp_hotel_booking_hotel_email_address',
						'title'		=> __( 'Email', 'tp-hotel-booking' ),
						'default'		=> ''
					),

				array(
						'type'			=> 'section_end',
						'id'			=> 'hotel_booking_infomation'
					)

			) );
	}

}

return new HB_Admin_Setting_Hotel_Info();