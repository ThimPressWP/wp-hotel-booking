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
		$post_id = $post->ID;
		// require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-block-date.php';
	}

	public function save( $post_id ) {
	}
}