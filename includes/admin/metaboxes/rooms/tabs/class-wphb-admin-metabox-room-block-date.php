<?php
/**
 * WP Hotel Booking admin metabox room block date.
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

class WPHB_Admin_Metabox_Room_Block_Date extends WPHB_Meta_Box {

	public function render( $post ) {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-block-date.php';
	}

	public function save( $post_id ) {

	}

}
