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

	protected $id = null;

	protected $title = null;

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
				$this->save( $this->current_tab );
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
		wp_nonce_field( 'wphb_update_meta_box_settings', 'wphb_meta_box_settings_nonce' );
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

		if ( isset( $_REQUEST['section'] ) ) {
			$current_section = sanitize_text_field( $_REQUEST['section'] );
		}

		$html = array();

		$html[] = '<ul class="hb-admin-sub-tab subsubsub">';
		$sub    = array();
		foreach ( $sections as $id => $text ) {
			$sub[] = '<li>
						<a href="?page=tp_hotel_booking_settings&tab=' . esc_attr( $this->id ) . '&section=' . esc_attr( $id ) . '"' . ( $current_section === $id ? ' class="current"' : '' ) . '>' . esc_html( $text ) . '</a>
					</li>';
		}
		$html[] = implode( '&nbsp;|&nbsp;', $sub );
		$html[] = '</ul><br />';

		echo implode( '', $html );
	}

	// save setting option
	public function save( $tab = '' ) {
		$class_name   = strtolower( static::class );
		$class_prefix = 'wphb_admin_setting_';
		$name_compare = str_replace( $class_prefix, '', $class_name );

		if ( $name_compare != $tab ) {
			return;
		}

		$settings = $this->get_settings();
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
					$option_value = isset( $_POST[ $id ] ) ? floatval( $_POST[ $id ] ) : 0;
					break;
				case 'text':
					$option_value = isset( $_POST[ $id ] ) ? sanitize_text_field( $_POST[ $id ] ) : $default;
					break;
				case 'textarea':
					$option_value = isset( $_POST[ $id ] ) ? wp_kses_post( $_POST[ $id ] ) : $default;
					break;
				case 'image_size':
					$option_width  = isset( $_POST[ $id . '_width' ] ) ? floatval( $_POST[ $id . '_width' ] ) : $default['width'];
					$option_height = isset( $_POST[ $id . '_height' ] ) ? floatval( $_POST[ $id . '_height' ] ) : $default['height'];
					WPHB_Settings::instance()->set( $id . '_width', $option_width );
					WPHB_Settings::instance()->set( $id . '_height', $option_height );
					break;
				default:
					if ( ! isset( $_POST[ $id ] ) ) {
						break;
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
