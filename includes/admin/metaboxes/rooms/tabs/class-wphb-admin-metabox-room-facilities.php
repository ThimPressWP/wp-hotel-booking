<?php
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Admin_Metabox_Room_Facilities extends WPHB_Meta_Box {

	public function render( $post ) {
		require_once WPHB_PLUGIN_PATH . '/includes/admin/metaboxes/views/tabs/meta-room-facilities.php';
	}

	public function save( $post_id ) {
		$fac_label      = isset( $_POST['_hb_room_fac_label'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hb_room_fac_label'], 'html' ) : array();
		$fac_attr_label = isset( $_POST['_hb_room_fac_attr_label'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hb_room_fac_attr_label'], 'html' ) : array();
		$fac_attr_image = isset( $_POST['_hb_room_fac_attr_image'] ) ? WPHB_Helpers::sanitize_params_submitted( $_POST['_hb_room_fac_attr_image'], 'html' ) : array();

		$facs = array();
		if ( ! empty( $fac_label ) ) {
			$fac_size = count( $fac_label );

			for ( $i = 0; $i < $fac_size; $i ++ ) {
				if ( ! empty( $fac_label[ $i ] ) ) {
					$attr = array();
					if ( isset( $fac_attr_label[ $i ] ) ) {
						$fac_attr_size = count( $fac_attr_label[ $i ] );

						for ( $j = 0; $j < $fac_attr_size; $j ++ ) {
							$attr[] = array(
								'label' => $fac_attr_label[ $i ][ $j ],
								'image' => $fac_attr_image[ $i ][ $j ],
							);
						}

					}
					$facs[] = array(
						'label' => $fac_label[ $i ],
						'attr'  => $attr,
					);

				}
			}
		}

		update_post_meta( $post_id, '_wphb_room_facilities', $facs );
	}
}
