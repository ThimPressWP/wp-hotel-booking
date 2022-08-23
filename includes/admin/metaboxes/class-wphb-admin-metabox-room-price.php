<?php
/**
 * WP Hotel Booking admin metabox room price.
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

class WPHB_Admin_Metabox_Room_Price {

	public $id = 'hb-booking-items';

	public $title = '';

	public $context = 'normal';

	public $screen = 'hb_room';

	public $priority = 'high';

	public $callback_args = null;

	function __construct() {

		$this->title = __( 'Regular Price', 'wp-hotel-booking' );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'update' ) );
	}

	public function add_meta_box() {
		add_meta_box( $this->id, $this->title, array( $this, 'render' ), $this->screen, $this->context, $this->priority, $this->callback_args );
	}

	public function render( $post ) {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/meta-room-pricing.php';
	}

	public function update( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['hotel-booking-room-pricing-nonce'] ) || ! wp_verify_nonce( $_POST['hotel-booking-room-pricing-nonce'], 'hotel_booking_room_pricing_nonce' ) ) {
			return;
		}

		if ( ! isset( $_POST['_hbpricing'] ) ) {
			return;
		}

		$plan_ids = WPHB_Helpers::sanitize_params_submitted( $_POST['_hbpricing']['plan_id'] ?? [] );
		$prices   = WPHB_Helpers::sanitize_params_submitted( $_POST['_hbpricing']['prices'] ?? [] );

		foreach ( $plan_ids as $plan_id ) {
			if ( array_key_exists( $plan_id, $prices ) ) {
				hb_room_set_pricing_plan(
					array(
						'start_time' => sanitize_text_field( $_POST['start_time'][ $plan_id ] ?? null ),
						'end_time'   => sanitize_text_field( $_POST['end_time'][ $plan_id ] ?? null ),
						'pricing'    => $prices[ $plan_id ] ?? null,
						'room_id'    => $post_id,
						'plan_id'    => $plan_id,
					)
				);
			} else {
				hb_room_remove_pricing( $plan_id );
			}
		}

	}

}
