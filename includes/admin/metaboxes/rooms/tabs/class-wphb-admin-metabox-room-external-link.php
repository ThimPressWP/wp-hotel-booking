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
		$setting = WPHB_Settings::instance()->get( 'external_link_icons', '' );
		$post_id = $post->ID;
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-external-link.php';
	}

	public function save( $post_id ) {
		if ( ! isset( $_POST['_hb_room_external_link'] ) ) {
			return;
		}
		update_post_meta( $post_id, '_hb_room_external_link', sanitize_text_field( $_POST['_hb_room_external_link'] ) );
	}
}