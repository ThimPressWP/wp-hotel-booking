<?php
/**
 * Template check rooms
 */

namespace WPHB\TemplateHooks;

use WPHB\Helpers\Singleton;

class CheckRoomsTemplate {
	use Singleton;

	public function init() {
		add_action( 'wphb/check-single-room/layout', [ $this, 'check_single_room_layout' ] );
	}

	public function check_single_room_layout( $room ) {
		wp_enqueue_script( 'wpdb-single-room-js' );
		wphb_get_template_no_override( 'single-room/search/check-dates-available.php', compact( 'room' ) );
	}
}

