<?php

class HB_Extra_Post_Type
{

	static $_instance = null;

	/**
	 * initialize class register post type, insert, update post
	 * with post_type = 'hb_extra_room'
	 */
	function __construct()
	{
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'wp_ajax_tp_extra_package_remove', array( $this, 'tp_extra_package_remove' ) );
	}

	/**
	* Registers a new post type
	* @uses $wp_post_types Inserts new post type object into the list
	*
	* @param string  Post type key, must not exceed 20 characters
	* @param array|string  See optional args description above.
	* @return object|WP_Error the registered post type object, or an error object
	*/
	function init()
	{
		$labels = array(
			'name'                => __( 'Extra Room', 'wp-hotel-booking' ),
			'singular_name'       => __( 'Extra Room', 'wp-hotel-booking' ),
			'add_new'             => _x( 'Add New Extra Room', 'wp-hotel-booking', 'wp-hotel-booking' ),
			'add_new_item'        => __( 'Add New Extra Room', 'wp-hotel-booking' ),
			'edit_item'           => __( 'Edit Extra Room', 'wp-hotel-booking' ),
			'new_item'            => __( 'New Extra Room', 'wp-hotel-booking' ),
			'view_item'           => __( 'View Extra Room', 'wp-hotel-booking' ),
			'search_items'        => __( 'Search Extra Room', 'wp-hotel-booking' ),
			'not_found'           => __( 'No Extra Room found', 'wp-hotel-booking' ),
			'not_found_in_trash'  => __( 'No Extra Room found in Trash', 'wp-hotel-booking' ),
			'parent_item_colon'   => __( 'Parent Singular Extra Room:', 'wp-hotel-booking' ),
			'menu_name'           => __( 'Extra Room', 'wp-hotel-booking' ),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => __( 'Extra room system booking', 'wp-hotel-booking' ),
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => true,
			// 'can_export'          => false,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title', 'editor'
			)
		);

		register_post_type( 'hb_extra_room', $args );
	}

	function add_extra( $post_id, $post = array() )
	{
		global $wpdb;
		$query = $wpdb->prepare( "
				SELECT * FROM $wpdb->posts WHERE `ID` = %d AND `post_type` = %s
			", $post_id ,'hb_extra_room' );

		$results = $wpdb->get_results( $query, OBJECT );

		$args = array(
				'post_title'	=> isset( $post['name'] ) ? $post['name'] : '',
				'post_content'	=> isset( $post['desc'] ) ? $post['desc'] : '',
				'post_type'		=> 'hb_extra_room',
				'post_status'	=> 'publish'
			);

		if( ! $results )
		{
			$post_id = wp_insert_post( $args );
		}
		else
		{
			$args['ID'] = $post_id;
			wp_update_post( $args );
		}

		if( isset( $post['price'] ) )
			$price = (float)$post['price'];
		else
			$price = 0;

		if( get_post_meta( $post_id, 'tp_hb_extra_room_price', true ) || get_post_meta( $post_id, 'tp_hb_extra_room_price', true ) == 0 )
			update_post_meta( $post_id, 'tp_hb_extra_room_price', $price );
		else
			add_post_meta( $post_id, 'tp_hb_extra_room_price', $price );

		unset( $post['name'] );
		unset( $post['desc'] );
		unset( $post['price'] );

		foreach ( $post as $key => $value ) {
			if( get_post_meta( $post_id, 'tp_hb_extra_room_'.$key, true )
				|| get_post_meta( $post_id, 'tp_hb_extra_room_'.$key, true ) === ''
				|| get_post_meta( $post_id, 'tp_hb_extra_room_'.$key, true ) == 0 )
			{
				update_post_meta( $post_id, 'tp_hb_extra_room_'.$key, $value );
			}
			else
			{
				add_post_meta( $post_id, 'tp_hb_extra_room_'.$key, $value );
			}
		}

		return $post_id;
	}

	function tp_extra_package_remove()
	{
		if( ! isset( $_POST ) )
			return;

		if( ! isset( $_POST['package_id'] ) )
			return;

		if( wp_delete_post( $_POST['package_id'] ) || ! get_post( $_POST['package_id'] ) )
		{
			wp_send_json( array( 'status' => 'success' ) );
		}
	}

	/**
	 * get instance return self instead of new Class()
	 * @return object class
	 */
	static function instance()
	{
		if( self::$_instance )
			return self::$_instance;

		return new self();
	}


}

HB_Extra_Post_Type::instance();