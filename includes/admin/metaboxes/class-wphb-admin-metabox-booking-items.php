<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-03-25 12:00:54
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 10:51:31
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class WPHB_Admin_Metabox_Booking_Items {

	public $id = 'hb-booking-items';

	public $title = '';

	public $context = 'advanced';

	public $screen = 'hb_booking';

	public $priority = 'high';

	public $callback_args = null;

	function __construct() {

		$this->title = __( 'Booking Items', 'wp-hotel-booking' );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10 );
		add_action( 'save_post', array( $this, 'update' ) );
	}

	public function add_meta_box() {
		add_meta_box( $this->id, $this->title, array( $this, 'render' ), $this->screen, $this->context, $this->priority, $this->callback_args );
	}

	public function render( $post ) {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items.php';
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-items-template-js.php';
	}

	public function update( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	}

}
