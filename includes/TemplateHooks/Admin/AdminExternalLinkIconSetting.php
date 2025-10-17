<?php
namespace WPHB\TemplateHooks\Admin;

use Exception;
use WPHB_Settings;
use WPHB\Helpers\Singleton;
use WPHB\Helpers\Template;
/**
 * AdminExterlinkIconSetting
 */
class AdminExternalLinkIconSetting {
	use Singleton;

	public function init() {
		add_action( 'hotel_booking_setting_field_tp_hotel_booking_external_link_icons', array( $this, 'layout' ) );
	}

	public function layout( $field ) {
		try {
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script(
				'wphb-icon-external-link-upload',
				WPHB_PLUGIN_URL . '/assets/js/admin/icon-external-link.js',
				array(),
				uniqid(),
				array(
					'strategy'  => 'defer',
					'in_footer' => 1,
				)
			);
			$localize = array(
				'uploader_title'       => __( 'Select Images', 'wp-hotel-booking' ),
				'uploader_button_text' => __( 'Add to Gallery', 'wp-hotel-booking' ),
				'remove_button_title'  => __( 'Remove', 'wp-hotel-booking' ),
			);
			wp_localize_script( 'wphb-icon-external-link-upload', 'wphbIconExternalLinkSettings', $localize );
			$field_title   = $this->field_title( $field );
			$field_content = $this->field_content( $field );
			$sections      = array(
				'wrap'     => '<tr valign="top">',
				'title'    => $field_title,
				'content'  => $field_content,
				'wrap_end' => '</tr>',
			);

			echo Template::combine_components( $sections );
		} catch ( Exception $e ) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	public function field_title( $field ) {
		return sprintf( '<th scope="row"><label>%s</label</th>', esc_html( $field['title'] ) );
	}

	public function field_content( $field ) {
		$setting        = WPHB_Settings::instance()->get( 'external_link_icons' );
		$attachment_ids = explode( ',', $setting );
		$icon_html      = '';
		if ( ! empty( $attachment_ids ) ) {
			foreach ( $attachment_ids as $attachment_id ) {
				$attachment_url = wp_get_attachment_image_url( $attachment_id );
				if ( ! $attachment_url ) {
					continue;
				}
				$icon_html .= $this->render_image( $attachment_id, $attachment_url );
			}
		}
		$icon_html .= sprintf( '<li class="gallery-item"><div class="wphb-external-icon--add-new"><span class="dashicons-plus dashicons"></span></div></li>' );
		$section    = array(
			'wrap'        => '<td class="hb-form-field icon-gallery-upload-wrapper"><ul class="icon-gallery-list">',
			'items'       => $icon_html,
			'input_field' => sprintf( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', $field['id'], $setting ),
			'wrap_end'    => '</ul></td>',
		);
		return Template::combine_components( $section );
	}

	public function render_image( $attachment_id, $attachment_url ) {

		return sprintf(
			'<li class="gallery-item" data-id="%1$s">
                <div class="image-container">
                    <img src="%2$s" alt="">
                    <button type="button" class="remove-image" title="%3$s">×</button>
                </div>
            </li>',
			$attachment_id,
			esc_url( $attachment_url ),
			__( 'Remove', 'wp-hotel-booking' )
		);
	}
}
