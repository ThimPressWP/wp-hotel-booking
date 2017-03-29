<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-03-25 16:11:00
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 10:51:38
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class WPHB_Admin_Metabox_Booking_Actions {

	public $id = 'hb-booking-actions';

	public $title = '';

	public $context = 'side';

	public $screen = 'hb_booking';

	public $priority = 'high';

	public $callback_args = null;

	function __construct() {

		$this->title = __( 'Booking Actions', 'wp-hotel-booking' );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10 );
		add_action( 'save_post', array( $this, 'update' ) );
	}

	public function add_meta_box() {
		add_meta_box( $this->id, $this->title, array( $this, 'render' ), $this->screen, $this->context, $this->priority, $this->callback_args );
	}

	public function render() {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-actions.php';
	}

	public function update( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	}

}
