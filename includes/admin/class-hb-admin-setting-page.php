<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-29 15:09:28
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-30 14:19:48
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class WPHB_Admin_Setting_Page {

	protected $id = null;

	protected $title = null;

	function __construct() {

		add_filter( 'hb_admin_settings_tabs', array( $this, 'setting_tabs' ) );
		add_action( 'hb_admin_settings_sections_' . $this->id, array( $this, 'setting_sections' ) );
		add_action( 'hb_admin_settings_tab_' . $this->id, array( $this, 'output' ) );
	}

	/**
	 * get_settings field
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

		if ( count( $sections ) === 1 ) {
			return;
		}

		$current_section = null;

		if ( isset( $_REQUEST['section'] ) ) {
			$current_section = sanitize_text_field( $_REQUEST['section'] );
		}

		$html = array();

		$html[] = '<ul class="hb-admin-sub-tab subsubsub">';
		$sub = array();
		foreach( $sections as $id => $text ) {
			$sub[] = '<li>
						<a href="?page=tp_hotel_booking_settings&tab='.$this->id.'&section='.$id.'"'. ( $current_section === $id ? ' class="current"' : '' ) .'>'.esc_html( $text ).'</a>
					</li>';
		}
		$html[] = implode( '&nbsp;|&nbsp;', $sub );
		$html[] = '</ul><br />';

		echo implode( '', $html );
	}

	// save setting option
	public function save() {
		$settings = $this->get_settings();
		WPHB_Admin_Settings::save_fields( $settings );
	}

}