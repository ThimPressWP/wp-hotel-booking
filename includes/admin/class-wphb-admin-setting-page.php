<?php
/**
 * WP Hotel Booking setting page.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class WPHB_Admin_Setting_Page {

	public $id = null;

	public $title = null;

	public $current_tab = null;

	function __construct() {

		add_filter( 'hb_admin_settings_tabs', array( $this, 'setting_tabs' ) );
		add_action( 'hb_admin_settings_sections_' . $this->id, array( $this, 'setting_sections' ) );
		add_action( 'hb_admin_settings_tab_' . $this->id, array( $this, 'output' ) );

		// Save setting
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';
		if ( $page === 'tp_hotel_booking_settings' ) {
			$this->current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
			if ( ! empty( $_POST ) ) {
				$save_tab = ! empty( $_POST['wphb_save_all_tabs'] ) ? (string) $this->id : $this->current_tab;
				$this->save( $save_tab );
			}
		}
	}

	/**
	 * get_settings field
	 *
	 * @return array settings fields
	 */
	public function get_settings() {
		return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array() );
	}

	public function get_sections() {
		return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, array() );
	}

	// filter tab id
	public function setting_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->title;
		return $tabs;
	}

	// output setting page
	public function output() {
		$settings = $this->get_settings();
		WPHB_Admin_Settings::render_fields( $settings );
	}

	// filter section in tab id
	public function setting_sections() {
		$sections = $this->get_sections();

		if ( count( $sections ) === 0 ) {
			return;
		}

		$current_section = null;

		if ( isset( $_REQUEST['section'] ) && isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] === $this->id && array_key_exists( $_REQUEST['section'], $sections ) ) {
			$current_section = sanitize_text_field( $_REQUEST['section'] );
		}

		if ( ! $current_section ) {
			$section_keys    = array_keys( $sections );
			$current_section = reset( $section_keys );
		}

		$payments_by_slug = array();
		if ( $this->id === 'payments' && function_exists( 'hb_get_payment_gateways' ) ) {
			$gateways = hb_get_payment_gateways();
			if ( is_array( $gateways ) ) {
				foreach ( $gateways as $gw ) {
					if ( isset( $gw->slug ) ) {
						$payments_by_slug[ $gw->slug ] = $gw;
					}
				}
			}
		}

		echo '<div class="wphb-sub-tabs-wrap">';
		echo '<ul class="hb-admin-sub-tab subsubsub wphb-sub-tabs" data-tab="' . esc_attr( $this->id ) . '">';
		foreach ( $sections as $id => $text ) {
			$is_current = ( $current_section === $id );
			$badge_html = '';
			if ( isset( $payments_by_slug[ $id ] ) ) {
				$gw_settings = WPHB_Settings::instance()->get( $id );
				$is_enabled  = is_array( $gw_settings ) && isset( $gw_settings['enable'] )
					? ( 'on' === $gw_settings['enable'] || 1 == $gw_settings['enable'] )
					: ( method_exists( $payments_by_slug[ $id ], 'is_enable' ) && $payments_by_slug[ $id ]->is_enable() );
				$badge_html  = sprintf(
					'<span class="wphb-gateway-status-dot %s" data-gateway="%s" title="%s"></span>',
					$is_enabled ? 'is-enabled' : 'is-disabled',
					esc_attr( $id ),
					esc_attr( $is_enabled ? __( 'Enabled', 'wp-hotel-booking' ) : __( 'Disabled', 'wp-hotel-booking' ) )
				);
			}
			echo '<li class="wphb-sub-tab-item">';
			echo '<a href="?page=tp_hotel_booking_settings&tab=' . esc_attr( $this->id ) . '&section=' . esc_attr( $id ) . '" data-tab="' . esc_attr( $this->id ) . '" data-section="' . esc_attr( $id ) . '" class="wphb-sub-tab-link' . ( $is_current ? ' current is-active' : '' ) . '">';
			echo $badge_html;
			echo '<span>' . esc_html( $text ) . '</span>';
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	// save setting option
	public function save( $tab = '' ) {
		$nonce = WPHB_Helpers::get_param( 'wphb_meta_box_settings_nonce', '' );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wphb_update_meta_box_settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_hb_booking' ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$class_name   = strtolower( static::class );
		$class_prefix = 'wphb_admin_setting_';
		$name_compare = str_replace( $class_prefix, '', $class_name );

		if ( $name_compare != $tab && $this->id != $tab ) {
			return;
		}

		// Save multiple section field settings (e.g., all payment gateways rendered in DOM)
		$keys_section_settings = WPHB_Helpers::get_param( 'wphb_save_section_fields_settings', array() );
		if ( ! empty( $keys_section_settings ) && is_array( $keys_section_settings ) && $this->id === 'payments' ) {
			foreach ( $keys_section_settings as $key_setting ) {
				$key_setting = sanitize_text_field( $key_setting );
				if ( ! empty( $key_setting ) ) {
					$section_fields_setting = WPHB_Helpers::get_param( $key_setting, array() );
					WPHB_Settings::instance()->set( $key_setting, $section_fields_setting );
				}
			}
		}

		// Legacy single section fields setting fallback
		$section                   = WPHB_Helpers::get_param( 'section', '' );
		$key_section_field_setting = WPHB_Helpers::get_param( 'wphb_save_section_fields_setting', '' );
		if ( ! empty( $section ) && ! empty( $key_section_field_setting ) && empty( $keys_section_settings ) ) {
			$section_fields_setting = WPHB_Helpers::get_param( $key_section_field_setting, [] );
			WPHB_Settings::instance()->set( $key_section_field_setting, $section_fields_setting );

			return;
		}

		$settings = $this->get_settings();
		$this->save_settings_fields( $settings );
	}

	/**
	 * Save an array of setting field definitions.
	 *
	 * @param array $settings Field definitions.
	 * @return void
	 */
	protected function save_settings_fields( $settings = array() ) {
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return;
		}
		foreach ( $settings as $setting ) {
			$id           = $setting['id'] ?? '';
			$type         = $setting['type'] ?? '';
			$default      = $setting['default'] ?? '';
			$option_value = '';
			if ( empty( $type ) || empty( $id ) ) {
				continue;
			}

			if ( in_array( $type, array( 'section_start', 'section_end' ) ) ) {
				continue;
			}

			$type_custom_save = apply_filters(
				"wphb_admin_setting/{$type}/custom_save",
				[ 'image_size' ],
				$id,
				$setting
			);

			switch ( $type ) {
				case 'checkbox':
					$option_value = isset( $_POST[ $id ] ) ? 1 : 0;
					break;
				case 'number':
					if ( isset( $_POST[ $id ] ) && $_POST[ $id ] !== '' ) {
						$option_value = floatval( $_POST[ $id ] );
						if ( isset( $setting['min'] ) && is_numeric( $setting['min'] ) && $option_value < floatval( $setting['min'] ) ) {
							$option_value = floatval( $setting['min'] );
						}
						if ( isset( $setting['max'] ) && is_numeric( $setting['max'] ) && $option_value > floatval( $setting['max'] ) ) {
							$option_value = floatval( $setting['max'] );
						}
					} else {
						$option_value = isset( $setting['default'] ) && $setting['default'] !== '' ? floatval( $setting['default'] ) : 0;
						if ( isset( $setting['min'] ) && is_numeric( $setting['min'] ) && $option_value < floatval( $setting['min'] ) ) {
							$option_value = floatval( $setting['min'] );
						}
					}
					break;
				case 'text':
					$option_value = isset( $_POST[ $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) : $default;
					break;
				case 'textarea':
					$option_value = isset( $_POST[ $id ] ) ? wp_kses_post( wp_unslash( $_POST[ $id ] ) ) : $default;
					break;
				case 'image_size':
					$option_width  = isset( $_POST[ $id . '_width' ] ) ? floatval( $_POST[ $id . '_width' ] ) : $default['width'];
					$option_height = isset( $_POST[ $id . '_height' ] ) ? floatval( $_POST[ $id . '_height' ] ) : $default['height'];
					WPHB_Settings::instance()->set( $id . '_width', $option_width );
					WPHB_Settings::instance()->set( $id . '_height', $option_height );
					break;
				case 'select':
				case 'multiselect':
				case 'select_page':
					if ( isset( $_POST[ $id ] ) ) {
						$option_value = WPHB_Helpers::sanitize_params_submitted( $_POST[ $id ] );
						if ( 'select_page' === $type && 'add_new_page' === $option_value ) {
							$option_value = WPHB_Settings::instance()->get( $id, 0 );
						}
					} else {
						$option_value = ( $type === 'multiselect' || ( isset( $setting['type'] ) && $setting['type'] === 'multiselect' ) ) ? array() : ( isset( $setting['default'] ) ? $setting['default'] : '' );
					}
					$option_value = apply_filters( "wphb_admin_setting_save/{$type}", $option_value, $id, $setting );
					break;
				case 'radio':
					$option_value = isset( $_POST[ $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) : ( isset( $setting['default'] ) ? $setting['default'] : '' );
					$option_value = apply_filters( "wphb_admin_setting_save/{$type}", $option_value, $id, $setting );
					break;
				default:
					if ( ! isset( $_POST[ $id ] ) ) {
						continue 2;
					}

					$option_value = $_POST[ $id ];
					$option_value = apply_filters( "wphb_admin_setting_save/{$type}", $option_value, $id, $setting );
					if ( ! in_array( $type, $type_custom_save ) ) {
						$option_value = WPHB_Helpers::sanitize_params_submitted( $option_value );
					}

					break;
			}

			if ( ! in_array( $type, $type_custom_save ) ) {
				WPHB_Settings::instance()->set( $id, $option_value );
			}
		}
	}
}
