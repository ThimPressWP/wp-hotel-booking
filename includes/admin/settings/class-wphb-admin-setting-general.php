<?php
/**
 * WP Hotel Booking admin setting general.
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

if ( ! class_exists( 'WPHB_Admin_Setting_General' ) ) {

	class WPHB_Admin_Setting_General extends WPHB_Admin_Setting_Page {

		public $id    = 'general';
		public $title = null;

		function __construct() {

			$this->title = __( 'General', 'wp-hotel-booking' );

			parent::__construct();
		}

		public function get_settings() {

			$currency_code_options = hb_payment_currencies();

			foreach ( $currency_code_options as $code => $name ) {
				$currency_code_options[ $code ] = $name . ' (' . hb_get_currency_symbol( $code ) . ')';
			}

			return apply_filters(
				'hotel_booking_admin_setting_fields_' . $this->id,
				array(
					array(
						'type'  => 'section_start',
						'id'    => 'general_settings',
						'title' => __( 'General Options', 'wp-hotel-booking' ),
						'desc'  => __( 'General options for system.', 'wp-hotel-booking' ),
					),

					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_single_purchase',
						'title'   => __( 'Single Purchase', 'wp-hotel-booking' ),
						'desc'    => __( 'Disable select quantity in Hotel Search page (default: one at a time)', 'wp-hotel-booking' ),
						'default' => 1,
					),
					array(
						'type'    => 'checkbox',
						'id'      => 'tp_hotel_booking_custom_process',
						'title'   => __( 'Custom Process', 'wp-hotel-booking' ),
						'desc'    => __( 'Choose extra options after select room in search page', 'wp-hotel-booking' ),
						'default' => 1,
					),
					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_currency',
						'title'   => __( 'Currency', 'wp-hotel-booking' ),
						'options' => $currency_code_options,
						'default' => 'USD',
					),
					array(
						'type'    => 'select',
						'id'      => 'tp_hotel_booking_price_currency_position',
						'title'   => __( 'Currency Position', 'wp-hotel-booking' ),
						'options' => array(
							'left'             => __( 'Left ( $69.99 )', 'wp-hotel-booking' ),
							'right'            => __( 'Right ( 69.99$ )', 'wp-hotel-booking' ),
							'left_with_space'  => __( 'Left with space ( $ 69.99 )', 'wp-hotel-booking' ),
							'right_with_space' => __( 'Right with space ( 69.99 $ )', 'wp-hotel-booking' ),
						),
						'default' => 'left',
					),
					array(
						'type'    => 'text',
						'id'      => 'tp_hotel_booking_price_thousands_separator',
						'title'   => __( 'Thousands Separator', 'wp-hotel-booking' ),
						'default' => ',',
					),
					array(
						'type'    => 'text',
						'id'      => 'tp_hotel_booking_price_decimals_separator',
						'title'   => __( 'Decimals Separator', 'wp-hotel-booking' ),
						'default' => '.',
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
						'step'    => 'any',
					),
					array(
						'type'    => 'number',
						'id'      => 'tp_hotel_booking_tax',
						'title'   => __( 'Tax', 'wp-hotel-booking' ),
						'default' => 10,
						'min'     => 0,
						'step'    => 'any',
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
							'min_to_max' => __( 'Min to Max', 'wp-hotel-booking' ),
						),
						'default' => 1,
					),
					array(
						'type'    => 'number',
						'id'      => 'tp_hotel_booking_advance_payment',
						'title'   => __( 'Advance Payment', 'wp-hotel-booking' ),
						'desc'    => __( 'Advance payment, eg: 50%', 'wp-hotel-booking' ),
						'default' => 50,
						'min'     => 0,
						'max'     => 100,
					),
					array(
						'type' => 'section_end',
						'id'   => 'general_settings',
					),
				)
			);
		}

	}

}

return new WPHB_Admin_Setting_General();
