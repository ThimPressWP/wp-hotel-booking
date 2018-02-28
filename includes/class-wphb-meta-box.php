<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WPHB_Meta_Box
 */
class WPHB_Meta_Box {

	/**
	 * @var object
	 */
	protected static $_meta_boxes = array();

	/**
	 * @var array
	 */
	protected $_args = array();

	/**
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Construction
	 *
	 * @param array
	 * @param array
	 */
	function __construct( $args = array(), $fields = array() ) {
		$this->_args   = wp_parse_args(
			$args, array(
				'title'           => '',
				'post_type'       => 'post',
				'meta_key_prefix' => ''
			)
		);
		$this->_fields = $fields;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Add meta box to post
	 */
	function add_meta_box() {
		$meta_box_id    = $this->_args['name'];
		$meta_box_title = $this->_args['title'];
		$callback       = ! empty( $this->_args['callback'] ) ? $this->_args['callback'] : array( $this, 'render' );
		$post_types     = ! empty( $this->_args['post_type'] ) ? $this->_args['post_type'] : 'post';
		$priority       = ! empty( $this->_args['priority'] ) ? $this->_args['priority'] : 'default';
		$context        = ! empty( $this->_args['context'] ) ? $this->_args['context'] : 'normal';
		if ( is_string( $post_types ) ) {
			$post_types = explode( ',', $post_types );
		}

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				$meta_box_id, $meta_box_title, $callback, $post_type, $context, $priority
			);
		}
	}

	/**
	 * Add new field to meta box
	 *
	 * @param array
	 *
	 * @return WPHB_Meta_Box instance
	 */
	function add_field( $field ) {
		$args = func_get_args();
		foreach ( $args as $f ) {
			$this->_fields[] = (array) $f;
		}
		// metabox hook
		$this->_fields = apply_filters( 'hb_metabox_' . $this->_args['name'], $this->_fields );

		return $this;
	}

	/**
	 * Return all fields of meta box
	 * @return array
	 */
	function get_fields() {
		return $this->_fields;
	}

	/**
	 * Check to see if a meta key is already added to the post
	 *
	 * @param int
	 * @param string
	 *
	 * @return bool
	 */
	function has_post_meta( $object_id, $meta_key ) {
		$meta_type  = 'post';
		$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
			$meta_cache = $meta_cache[ $object_id ];
		}

		return array_key_exists( $this->_args['meta_key_prefix'] . $meta_key, $meta_cache );
	}

	/**
	 * Output meta box content
	 *
	 * @param int
	 */
	function render( $post ) {
		if ( $fields = $this->_fields ) {
			echo '<ul class="hb-form-table">';
			foreach ( $fields as $field ) {
				echo '<li class="hb-form-field">';
				if ( isset( $field['label'] ) && $field['label'] != '' ) {
					echo '<label class="hb-form-field-label">' . $field['label'] . '</label>';
				}
				if ( $this->has_post_meta( $post->ID, $field['name'] ) ) {
					$field['std'] = get_post_meta( $post->ID, $this->_args['meta_key_prefix'] . $field['name'], true );
				}
				$field['name'] = $this->_args['meta_key_prefix'] . $field['name'];
				if ( empty( $field['id'] ) ) {
					$field['id'] = sanitize_title( $field['name'] );
				}
				echo '<div class="hb-form-field-input">';
				echo '<div class="hb-form-field-input-inner">';
				$tmpl = WP_Hotel_Booking::instance()->locate( "includes/admin/metaboxes/views/fields/{$field['type']}.php" );
				require $tmpl;
				if ( ! empty( $field['desc'] ) ) {
					printf( '<p class="description">%s</p>', $field['desc'] );
				}
				echo '</div>';
				echo '</div>';
				echo '</li>';
			}
			echo '</ul>';
		}
		wp_nonce_field( $this->get_nonce_field_action(), $this->get_nonce_field_name() );
	}

	/**
	 * Get name of nonce field for this meta box
	 *
	 * @return string
	 */
	function get_nonce_field_name() {
		return 'meta_box_' . $this->_args['name'];
	}

	/**
	 * Get name of nonce field action for this meta box
	 *
	 * @return string
	 */
	function get_nonce_field_action() {
		return 'update_meta_box_' . $this->_args['name'];
	}

	/**
	 * Update meta data when saving post
	 *
	 * @param int
	 */
	function update( $post_id ) {
		if ( ! isset( $_POST[ $this->get_nonce_field_name() ] ) || ! wp_verify_nonce( sanitize_text_field( $_POST[ $this->get_nonce_field_name() ] ), $this->get_nonce_field_action() ) ) {
			return;
		}
		if ( ! $this->_fields ) {
			return;
		}

		foreach ( $this->_fields as $field ) {
			if ( array_key_exists( $this->_args['meta_key_prefix'] . $field['name'], (array) $_POST ) ) {
				$meta_value = $_POST[ $this->_args['meta_key_prefix'] . $field['name'] ];
				$meta_value = apply_filters( 'hb_meta_box_update_meta_value', $meta_value, $field['name'], $this->_args['name'], $post_id );
				update_post_meta( $post_id, $this->_args['meta_key_prefix'] . $field['name'], $meta_value );
				if ( $field['name'] == 'room_capacity' ) {
					update_post_meta( $post_id, $this->_args['meta_key_prefix'] . 'room_origin_capacity', $meta_value );
				}
			}

			if ( ! $_POST[ $this->_args['meta_key_prefix'] . 'room_extra' ] ) {
				update_post_meta( $post_id, $this->_args['meta_key_prefix'] . 'room_extra', '' );
			}
		}

		do_action( 'hb_update_meta_box_' . $this->_args['name'], $post_id );
	}

	/**
	 * Update all meta boxes registered
	 *
	 * @param $post_id
	 */
	static function update_meta_boxes( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( 'post' != strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		if ( ! ( $meta_boxes = self::$_meta_boxes ) ) {
			return;
		}

		foreach ( $meta_boxes as $meta_box ) {
			$meta_box->update( $post_id );
		}
	}

	/**
	 * Get an instance of a meta box, create a new one if it is not exists
	 *
	 * @param string $id
	 * @param array $args
	 * @param array $fields
	 *
	 * @return WPHB_Meta_Box instance
	 */
	static function instance( $id, $args, $fields ) {
		if ( empty( self::$_meta_boxes[ $id ] ) ) {
			if ( empty( $args['name'] ) ) {
				$args['name'] = $id;
			}
			self::$_meta_boxes[ $id ] = new self( $args, $fields );
		}

		return self::$_meta_boxes[ $id ];
	}

}

/**
 * Save post action
 */
add_action( 'save_post', array( 'WPHB_Meta_Box', 'update_meta_boxes' ) );
