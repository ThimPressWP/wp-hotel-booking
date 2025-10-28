<?php 
namespace WPHB\TemplateHooks;

use Exception;
use WPHB_Settings;
use WPHB\Helpers\Singleton;
use WPHB\Helpers\Template;
/**
 * SingleRoomExternalLinkTemplate
 */
class SingleRoomExternalLinkTemplate {
	use Singleton;

	public function init() {
		add_action( 'hotel_booking_single_room_after_booking_form', array( $this, 'layout' ) );
	}

	public function layout( $room ) {
		try {
			if ( ! $room ) {
				return;
			}

			$hb_extenal_link_settings = WPHB_Settings::instance()->get( 'external_link_settings' );

			$setting_fields   = ! empty( $hb_extenal_link_settings ) ? json_decode( $hb_extenal_link_settings, true ) : array();
			// check external link global settings
			if ( empty( $setting_fields ) ) {
				return;
			}

			$room_id = $room->ID;
			$external_links = get_post_meta( $room_id, '_hb_room_external_link', true );
			$external_links = ! empty( $external_links ) ? json_decode( $external_links, true ) : array();
			// check room external link settings
			if ( empty( $external_links ) ) {
				return;
			}
			$show = false;
			foreach( $external_links as $field_id => $field ) {
				if ( $field['enabled'] ) {
					$show = true;
					break;
				}
			}
			if ( ! $show ) {
				return;
			}

			$title = sprintf( '<p>%s</p>', __( 'Reserve via our trusted partner', 'wp-hotel-booking' ) );
			$external_link_html = $this->render_external_link( $room, $external_links, $setting_fields );

			$sections      = array(
				'wrap'     => '<div class="wphb-single-room-external-link">',
				'title'    => $title,
				'content'  => $external_link_html,
				'wrap_end' => '</div>',
			);

			echo Template::combine_components( $sections );
		} catch ( Exception $e ) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function render_external_link( $room, $external_links = array(), $setting_fields = array() ) {
		$external_link_html = '';
		if ( ! empty( $setting_fields ) ) {
			foreach ( $setting_fields as $field_id => $field ) {
				if( ! isset( $external_links[ $field_id ] ) || ! $external_links[ $field_id ]['enabled'] ) {
					continue;
				}
				$default_icon_url = WPHB_PLUGIN_URL . '/assets/images/icon-128x128.png';

				$icon_id = $field['icon_id'] ? $field['icon_id'] : 0;
				$alt_text = (string) get_post_meta( $icon_id, '_wp_attachment_image_alt', true );
				$icon_url = $field['icon_url'] ? $field['icon_url'] : $default_icon_url;
				$external_link = $external_links[ $field_id ]['external_link'] ? $external_links[ $field_id ]['external_link'] : $field['external_link'];
				$external_link_html .= sprintf( '
					<li>
				    <a href="%1$s" target="_blank" rel="noopener noreferrer">
				      <img src="%2$s" 
				           alt="%3$s" 
				           size="50" height="50" width="50"/>
				    </a>
				  </li>', esc_url( $external_link ), esc_url( $icon_url ), $alt_text );
			}
		}
		$sections = array(
			'wrap' => '<ul class="wphb-partner-links">',
			'links' => $external_link_html,
			'wrap_end' => '</ul>',
		);
		return Template::combine_components( $sections );
	}
}
 ?>