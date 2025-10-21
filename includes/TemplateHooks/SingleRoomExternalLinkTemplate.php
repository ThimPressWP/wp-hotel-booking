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

			$title = sprintf( '<h3>%s</h3>', __( 'Reserve via our trusted partner', 'wp-hotel-booking' ) );
			$external_link_html = $this->render_external_link( $room );

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

	public function render_external_link( $room ) {
		$room_id = $room->ID;
		$external_links = get_post_meta( $room_id, '_hb_room_external_link', true );
		$external_links = ! empty( $external_links ) ? json_decode( $external_links, true ) : '';
		$external_link_html = '';
		if ( ! empty( $external_links ) ) {
			foreach ( $external_links as $link ) {
				if( ! $link['enable'] ) {
					continue;
				}
				$alt_text = get_post_meta( $link['icon_id'], '_wp_attachment_image_alt', true );
				$external_link_html .= sprintf( '
					<li>
				    <a href="%1$s" target="_blank" rel="noopener noreferrer">
				      <img src="%2$s" 
				           alt="%3$s" 
				           size="50"/>
				    </a>
				  </li>', esc_url( $link['external_link'] ), $link['icon_url'], $alt_text );
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