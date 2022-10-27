<?php
/**
 * WP Hotel Booking admin metabox room faqs.
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

class WPHB_Admin_Metabox_Room_FAQ extends WPHB_Meta_Box {

	public function render( $post ) {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-faq.php';
	}

	public function save( $post_id ) {

		$faq_title   = isset( $_POST['_hb_room_faq_title'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hb_room_faq_title'], 'html' ) : array();
		$faq_content = isset( $_POST['_hb_room_faq_content_input'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hb_room_faq_content_input'], 'html' ) : array();
		$faqs        = array();
		if ( ! empty( $faq_title ) ) {
			$faq_title_size = count( $faq_title );

			for ( $i = 0; $i < $faq_title_size; $i ++ ) {
				if ( ! empty( $faq_title[ $i ] ) ) {
					$faqs[] = array( $faq_title[ $i ], $faq_content[ $i ] );
				}
			}
		}

		update_post_meta( $post_id, '_wphb_room_faq', $faqs );

	}

}
