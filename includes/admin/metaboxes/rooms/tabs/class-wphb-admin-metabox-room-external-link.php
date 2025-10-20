<?php
/**
 * WP Hotel Booking admin metabox room external link
 *
 * @version     2.2.4
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Classes
 * @category    Classes
 * @author      Thimpress
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Admin_Metabox_Room_External_Link extends WPHB_Meta_Box {

	public function render( $post ) {
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_script( 'wphb-admin-room-external-link' );
		$setting  = WPHB_Settings::instance()->get( 'external_link_icons', '' );
		$localize = array(
			'list_icon_ids'        => $setting,
			'uploader_title'       => __( 'Select Icon', 'wp-hotel-booking' ),
			'uploader_button_text' => __( 'Add Icon', 'wp-hotel-booking' ),
			'remove_button_title'  => __( 'Remove', 'wp-hotel-booking' ),
			'no_icons_found'       => __('No icons found, go to Settings > Room > External Link Icon to add icons', 'wp-hotel-booking' ),
		);
		wp_localize_script( 'wphb-admin-room-external-link', 'wphbAdminRoomExternalLink', $localize );
		$post_id = $post->ID;
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-external-link.php';
	}

	public function save( $post_id ) {
	}
}