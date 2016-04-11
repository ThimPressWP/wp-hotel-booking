<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-11 11:15:37
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'HB_Admin_Setting_General' ) ) {

	class HB_Admin_Setting_General extends HB_Admin_Setting_Page {

		public $id = 'general';

		public $title = null;

		function __construct() {

			$this->title = __( 'General', 'tp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(

					array(
							'type'		=> 'section_start',
							'id'		=> 'general_settings',
							'title'		=> __( 'General Options', 'tp-hotel-booking' ),
							'desc'		=> __( 'General options for system.', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'select_page',
							'id'		=> 'tp_hotel_booking_search_page_id',
							'title'		=> __( 'Search Page', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'select_page',
							'id'		=> 'tp_hotel_booking_terms_page_id',
							'title'		=> __( 'Terms And Conditions Page', 'tp-hotel-booking' )
						),

					array(
							'type'		=> 'select',
							'id'		=> 'tp_hotel_booking_currency',
							'title'		=> __( 'Currency', 'tp-hotel-booking' ),
							'options'	=> hb_payment_currencies(),
							'default'	=> 'USD'
						),

					array(
							'type'		=> 'select',
							'id'		=> 'tp_hotel_booking_price_currency_position',
							'title'		=> __( 'Currency Position', 'tp-hotel-booking' ),
							'options'	=> array(
									'left'		=> __('Left ( $69.99 )', 'tp-hotel-booking'),
									'right'		=> __('Right ( 69.99$ )', 'tp-hotel-booking'),
									'left_with_space'	=> __('Left with space ( $ 69.99 )', 'tp-hotel-booking'),
									'right_with_space'	=> __('Right with space ( 69.99 $ )', 'tp-hotel-booking')
								),
							'default'	=> 'left'
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_price_thousands_separator',
							'title'		=> __( 'Thousands Separator', 'tp-hotel-booking' ),
							'default'	=> ','
						),

					array(
							'type'		=> 'text',
							'id'		=> 'tp_hotel_booking_price_decimals_separator',
							'title'		=> __( 'Decimals Separator', 'tp-hotel-booking' ),
							'default'	=> '.'
						),

					array(
							'type'		=> 'number',
							'id'		=> 'tp_hotel_booking_price_number_of_decimal',
							'title'		=> __( 'Number of decimal', 'tp-hotel-booking' ),
							'default'	=> 1,
							'min'		=> 0,
							'max'		=> 3,
						),

					array(
							'type'		=> 'number',
							'id'		=> 'tp_hotel_booking_tax',
							'title'		=> __( 'Tax', 'tp-hotel-booking' ),
							'default'	=> 10,
							'min'		=> 0
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'tp_hotel_booking_price_including_tax',
							'title'		=> __( 'Price including tax', 'tp-hotel-booking' ),
							'default'	=> 1,
						),

					array(
							'type'		=> 'select',
							'id'		=> 'tp_hotel_booking_price_display',
							'title'		=> __( 'Price display', 'tp-hotel-booking' ),
							'options'	=> array(
									'min'	=> __('Min', 'tp-hotel-booking'),
									'max'	=> __('Max', 'tp-hotel-booking'),
									'min_to_max'	=> __('Min to Max', 'tp-hotel-booking')
								),
							'default'	=> 1,
						),

					array(
							'type'		=> 'number',
							'id'		=> 'tp_hotel_booking_advance_payment',
							'title'		=> __( 'Advance Payment', 'tp-hotel-booking' ),
							'desc'		=> __( 'Payment addvance. Eg: 50%', 'tp-hotel-booking' ),
							'default'	=> 50,
							'min'		=> 0,
							'max'		=> 100
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'general_settings'
						)

				) );
		}

	}

}

return new HB_Admin_Setting_General();