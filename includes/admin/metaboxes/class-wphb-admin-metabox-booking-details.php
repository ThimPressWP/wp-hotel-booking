<?php
/**
 * WP Hotel Booking admin metabox booking details.
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

class WPHB_Admin_Metabox_Booking_Details {

	public $id = 'hb-booking-details';

	public $title = '';

	public $context = 'advanced';

	public $screen = 'hb_booking';

	public $priority = 'high';

	public $callback_args = null;

	function __construct() {

		$this->title = __( 'Booking Details', 'wp-hotel-booking' );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10 );
		add_action( 'save_post', array( __CLASS__, 'update' ) );
	}

	public function add_meta_box() {
		add_meta_box( $this->id, $this->title, array( $this, 'render' ), $this->screen, $this->context, $this->priority, $this->callback_args );
	}

	public function render() {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-booking-details.php';
	}

	public static function update( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['hotel_booking_metabox_booking_details_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['hotel_booking_metabox_booking_details_nonce'] ), 'hotel-booking-metabox-booking-details' ) ) {
			return;
		}

		foreach ( $_POST as $k => $vl ) {
			$k  = sanitize_text_field( $k );
			$vl = sanitize_text_field( $vl );

			if ( strpos( $k, '_hb_' ) !== 0 ) {
				continue;
			}

			update_post_meta( $post_id, $k, $vl );
			do_action( 'hb_booking_detail_update_meta_box_' . $k, $vl, $post_id );
			do_action( 'hb_booking_detail_update_meta_box', $k, $vl, $post_id );
		}
	}

}
