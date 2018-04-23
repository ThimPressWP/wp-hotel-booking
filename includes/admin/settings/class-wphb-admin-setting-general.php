<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 17:13:06
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-11 13:21:30
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WPHB_Admin_Setting_General' ) ) {

	class WPHB_Admin_Setting_General extends WPHB_Admin_Setting_Page {

		public $id = 'general';
		public $title = null;

		function __construct() {

			$this->title = __( 'General', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array(
				array(
					'type'  => 'section_start',
					'id'    => 'general_settings',
					'title' => __( 'General Options', 'wp-hotel-booking' ),
					'desc'  => __( 'General options for system.', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_rooms_page_id',
					'title' => __( 'Rooms Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_search_page_id',
					'title' => __( 'Search Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_checkout_page_id',
					'title' => __( 'Checkout Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_cart_page_id',
					'title' => __( 'Cart Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_account_page_id',
					'title' => __( 'Account Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_terms_page_id',
					'title' => __( 'Terms And Conditions Page', 'wp-hotel-booking' )
				),
				array(
					'type'  => 'select_page',
					'id'    => 'tp_hotel_booking_thankyou_page_id',
					'title' => __( 'Thank You Page', 'wp-hotel-booking' )
				),

				array(
					'type'    => 'checkbox',
					'id'      => 'tp_hotel_booking_single_purchase',
					'title'   => __( 'Single Purchase', 'wp-hotel-booking' ),
					'desc'    => __( 'Disable select quantity in Hotel Search page (default: one at a time)', 'wp-hotel-booking' ),
					'default' => 1,
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_currency',
					'title'   => __( 'Currency', 'wp-hotel-booking' ),
					'options' => hb_payment_currencies(),
					'default' => 'USD'
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_price_currency_position',
					'title'   => __( 'Currency Position', 'wp-hotel-booking' ),
					'options' => array(
						'left'             => __( 'Left ( $69.99 )', 'wp-hotel-booking' ),
						'right'            => __( 'Right ( 69.99$ )', 'wp-hotel-booking' ),
						'left_with_space'  => __( 'Left with space ( $ 69.99 )', 'wp-hotel-booking' ),
						'right_with_space' => __( 'Right with space ( 69.99 $ )', 'wp-hotel-booking' )
					),
					'default' => 'left'
				),
				array(
					'type'    => 'text',
					'id'      => 'tp_hotel_booking_price_thousands_separator',
					'title'   => __( 'Thousands Separator', 'wp-hotel-booking' ),
					'default' => ','
				),
				array(
					'type'    => 'text',
					'id'      => 'tp_hotel_booking_price_decimals_separator',
					'title'   => __( 'Decimals Separator', 'wp-hotel-booking' ),
					'default' => '.'
				),
				array(
					'type'    => 'number',
					'id'      => 'tp_hotel_booking_price_number_of_decimal',
					'title'   => __( 'Number of decimal', 'wp-hotel-booking' ),
					'default' => 1,
					'min'     => 0,
					'max'     => 3,
				),
				array(
					'type'    => 'number',
					'id'      => 'tp_hotel_booking_minimum_booking_day',
					'title'   => __( 'Minimum booking nights', 'wp-hotel-booking' ),
					'default' => 1,
					'min'     => 0,
					'step'    => 'any'
				),
				array(
					'type'    => 'number',
					'id'      => 'tp_hotel_booking_tax',
					'title'   => __( 'Tax', 'wp-hotel-booking' ),
					'default' => 10,
					'min'     => 0,
					'step'    => 'any'
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'tp_hotel_booking_price_including_tax',
					'title'   => __( 'Price including tax', 'wp-hotel-booking' ),
					'default' => 1,
				),
				array(
					'type'    => 'select',
					'id'      => 'tp_hotel_booking_price_display',
					'title'   => __( 'Price display', 'wp-hotel-booking' ),
					'options' => array(
						'min'        => __( 'Min', 'wp-hotel-booking' ),
						'max'        => __( 'Max', 'wp-hotel-booking' ),
						'min_to_max' => __( 'Min to Max', 'wp-hotel-booking' )
					),
					'default' => 1,
				),
				array(
					'type'    => 'number',
					'id'      => 'tp_hotel_booking_advance_payment',
					'title'   => __( 'Advance Payment', 'wp-hotel-booking' ),
					'desc'    => __( 'Payment advance. Eg: 50%', 'wp-hotel-booking' ),
					'default' => 50,
					'min'     => 0,
					'max'     => 100
				),
				array(
					'type' => 'section_end',
					'id'   => 'general_settings'
				)
			) );
		}

	}

}

return new WPHB_Admin_Setting_General();
