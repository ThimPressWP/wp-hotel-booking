<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-13 13:10:23
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-13 13:31:32
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HB_Admin_Metabox_Room_Price {

	public $id = 'hb-booking-items';

	public $title = '';

	public $context = 'advanced';

	public $screen = 'hb_room';

	public $priority = 'high';

	public $callback_args = null;

	function __construct() {

		$this->title = __( 'Regular Price', 'tp-hotel-booking' );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'update' ) );
	}

	public function add_meta_box( $post_type, $post ) {
		add_meta_box( $this->id, $this->title, array( $this, 'render' ), $this->screen, $this->context, $this->priority, $this->callback_args );
	}

	public function render( $post ) {
		require_once HB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-room-pricing.php';
	}

	public function update( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	}

}
