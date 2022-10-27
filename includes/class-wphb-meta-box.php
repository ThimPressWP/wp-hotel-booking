<?php
/**
 * WP Hotel Booking meta box.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class WPHB_Meta_Box
 */
abstract class WPHB_Meta_Box {
	private static $saved_meta_boxes = false;

	public $post_type = 'hb_room';

	public $meta_key_prefix = '_hb_';

	/**
	 * Construction
	 *
	 * @param array
	 * @param array
	 */
	public function __construct() {

		// add_action( 'admin_print_scripts', array( $this, 'remove_auto_save_script' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 100, 2 );
		add_action( 'wphb_save_' . $this->post_type . '_metabox', array( $this, 'save' ) );
	}

	public function remove_auto_save_script() {
		global $post;

		// if ( $post && in_array( get_post_type( $post->ID ), array( $this->post_type ) ) ) {
			wp_dequeue_script( 'autosave' );
		// }
	}

	/**
	 * Add meta box to post
	 */
	public function add_meta_box() {

	}

	/**
	 * Add new field to meta box
	 *
	 * @param array
	 *
	 * @return WPHB_Meta_Box instance
	 */
	public function metabox( $post_id ) {
		return array();
	}

	/**
	 * Check to see if a meta key is already added to the post
	 *
	 * @param int
	 * @param string
	 *
	 * @return bool
	 */
	public function has_post_meta( $object_id, $meta_key ) {
		$meta_type  = 'post';
		$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
			$meta_cache = $meta_cache[ $object_id ];
		}

		return array_key_exists( $this->meta_key_prefix . $meta_key, $meta_cache );
	}

	/**
	 * Output meta box content
	 *
	 * @param int
	 */
	public function render( $post ) {
		wp_nonce_field( 'wphb_update_meta_box', 'wphb_meta_box_nonce' );
	}

	public function save( $post_id ) {
		$fieldTypeHtmlArr = array( '_hb_room_addition_information' );

		if ( ! empty( $this->metabox( $post_id ) ) ) {
			foreach ( $this->metabox( $post_id ) as $key => $field ) {
				if ( array_key_exists( $this->meta_key_prefix . $field['name'], (array) $_POST ) ) {
					$keyPost    = $this->meta_key_prefix . $field['name'];
					$meta_value = WPHB_Helpers::sanitize_params_submitted( $_POST[ $keyPost ] );

					if ( in_array( $keyPost, $fieldTypeHtmlArr ) ) {
						$meta_value = WPHB_Helpers::sanitize_params_submitted( $meta_value, 'html' );
					} else {
						$meta_value = WPHB_Helpers::sanitize_params_submitted( $meta_value );
					}

					$meta_value = apply_filters( 'hb_meta_box_update_meta_value', $meta_value, $field['name'], $post_id );
					update_post_meta( $post_id, $this->meta_key_prefix . $field['name'], $meta_value );
				} else {
					update_post_meta( $post_id, $this->meta_key_prefix . $field['name'], '' );
				}
			}
		}
	}

	/**
	 * @param id      $post_id
	 * @param WP_Post $post
	 */
	public function save_meta_boxes( $post_id = 0, $post = null ) {

		$post_id = absint( $post_id );

		if ( empty( $_POST['wphb_meta_box_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wphb_meta_box_nonce'] ), 'wphb_update_meta_box' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( $post->post_type == $this->post_type ) {
			do_action( 'wphb_save_' . $post->post_type . '_metabox', $post_id, $post );
		}

	}
}

