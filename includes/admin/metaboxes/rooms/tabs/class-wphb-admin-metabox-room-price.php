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

class WPHB_Admin_Metabox_Room_Price extends WPHB_Meta_Box {

	public function render( $post ) {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-pricing.php';
	}

	public function save( $post_id ) {

		if ( ! isset( $_POST['hb-update-pricing-plan-field'] ) || ! wp_verify_nonce( $_POST['hb-update-pricing-plan-field'], 'hb-update-pricing-plan' ) ) {
			return;
		}

		if ( ! isset( $_POST['_hbpricing'] ) ) {
			return;
		}

		if ( ! isset( $post_id ) ) {
			return;
		}

		$ignore = array();
		$plans  = hb_room_get_pricing_plans( $post_id );

		$plan_ids = isset( $_POST['_hbpricing']['plan_id'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hbpricing']['plan_id'] ) : array();
		$prices   = isset( $_POST['_hbpricing']['prices'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hbpricing']['prices'] ) : array();
		$start    = isset( $_POST['_hbpricing']['date-start-timestamp'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hbpricing']['date-start-timestamp'] ) : array();
		$end      = isset( $_POST['_hbpricing']['date-end-timestamp'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hbpricing']['date-end-timestamp'] ) : array();

		foreach ( $plan_ids as $plan_id ) {
			hb_room_set_pricing_plan(
				array(
					'start_time' => isset( $start[ $plan_id ] ) ? $start[ $plan_id ] : '',
					'end_time'   => isset( $end[ $plan_id ] ) ? $end[ $plan_id ] : '',
					'pricing'    => isset( $prices[ $plan_id ] ) ? $prices[ $plan_id ] : null,
					'room_id'    => $post_id,
					'plan_id'    => $plan_id,
				)
			);
			$ignore[] = $plan_id;
		}

		foreach ( $plans as $id => $plan ) {
			if ( ! in_array( $id, $ignore ) ) {
				hb_room_remove_pricing( $id );
			}
		}
	}
}
