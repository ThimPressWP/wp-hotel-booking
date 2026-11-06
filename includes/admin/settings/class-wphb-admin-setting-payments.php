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
			$sections        = $this->get_sections();
			$current_section = null;

			if ( isset( $_REQUEST['section'] ) && isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] === $this->id && array_key_exists( $_REQUEST['section'], $sections ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			if ( ! $current_section ) {
				$section_keys    = array_keys( $sections );
				$current_section = reset( $section_keys );
			}

			$payments = hb_get_payment_gateways();

			// Render 'general' sub-section
			$is_general_active = ( $current_section === 'general' );
			echo '<div class="wphb-settings-section-panel' . ( $is_general_active ? ' is-active' : '' ) . '" data-tab="' . esc_attr( $this->id ) . '" data-section="general">';
			parent::output();
			echo '</div>';

			// Render each payment gateway sub-section
			if ( ! empty( $payments ) && is_array( $payments ) ) {
				foreach ( $payments as $payment ) {
					$slug      = $payment->slug;
					$is_active = ( $current_section === $slug );
					echo '<div class="wphb-settings-section-panel' . ( $is_active ? ' is-active' : '' ) . '" data-tab="' . esc_attr( $this->id ) . '" data-section="' . esc_attr( $slug ) . '" data-gateway-panel="' . esc_attr( $slug ) . '">';
					echo '<div class="wphb-settings-card">';
					echo '<div class="wphb-settings-card__header">';
					echo '<div class="wphb-settings-card__header-main">';
					echo '<h3 class="wphb-settings-card__title">' . esc_html( $payment->title ) . '</h3>';
					if ( ! empty( $payment->description ) ) {
						echo '<p class="description wphb-settings-card__desc">' . esc_html( $payment->description ) . '</p>';
					}
					echo '</div>';
					$gw_settings = WPHB_Settings::instance()->get( $slug );
					$is_enabled  = is_array( $gw_settings ) && isset( $gw_settings['enable'] )
						? ( 'on' === $gw_settings['enable'] || 1 == $gw_settings['enable'] )
						: ( method_exists( $payment, 'is_enable' ) && $payment->is_enable() );
					echo '<span class="wphb-gateway-badge ' . ( $is_enabled ? 'is-enabled' : 'is-disabled' ) . '" data-gateway-badge="' . esc_attr( $slug ) . '">' . esc_html( $is_enabled ? __( 'Active', 'wp-hotel-booking' ) : __( 'Inactive', 'wp-hotel-booking' ) ) . '</span>';
					echo '</div>';
					echo '<div class="wphb-settings-card__body">';
					$payment->admin_settings();
					echo sprintf(
						'<input type="hidden" name="wphb_save_section_fields_settings[]" value="%s" />',
						esc_attr( $payment->key_save_settings )
					);
					echo '</div>';
					echo '</div>';
					echo '</div>';
				}
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
