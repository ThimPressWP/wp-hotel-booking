<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-28 16:31:22
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 14:02:53
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HB_Install {

	// install hook
	static function install() {

		// create pages
		self::create_pages();

		// create update options
		self::create_options();

		// create term default. Eg: Room Capacities
		self::create_terms();

		// create tables
		self::create_tables();

		// upgrade database
		self::upgrade_database();

	}

	// upgrade database
	static function upgrade_database() {

	}

	// create options default
	static function create_options() {
		if ( ! class_exists( 'HB_Admin_Settings' ) ) {
			TP_Hotel_Booking::instance()->_include( 'includes/admin/class-hb-admin-settings.php' );
		}

		$settings_pages = HB_Admin_Settings::get_settings_pages();

		foreach ( $settings_pages as $setting ) {
			$options = $setting->get_settings();
			foreach ( $options as $option ) {
				if ( isset( $option[ 'id' ], $option[ 'default' ] ) ) {
					if ( ! get_option( $option[ 'id' ], false ) ) {
						update_option( $option['id'], $option['default'] );
					}
				}
			}
		}

		update_option( 'hotel_booking_version', HB_VERSION );
	}

	// create page. Eg: room-checkout, my-rooms
	static function create_pages() {
		if( ! function_exists( 'hb_create_page' ) ){
            TP_Hotel_Booking::instance()->_include( 'includes/admin/hb-admin-functions.php' );
            TP_Hotel_Booking::instance()->_include( 'includes/hb-functions.php' );
        }

		$pages = array();
		if( ! hb_get_page_id( 'my-rooms' ) || ! get_post( hb_get_page_id( 'my-rooms' ) ) )
		{
		    $pages['my-rooms'] = array(
		        'name'    => _x( 'my-rooms', 'my-rooms', 'tp-hotel-booking' ),
		        'title'   => _x( 'My Rooms', 'My Rooms', 'tp-hotel-booking' ),
		        'content' => '[' . apply_filters( 'hotel_booking_cart_shortcode_tag', 'hotel_booking_cart' ) . ']'
		    );
		}

		if( ! hb_get_page_id( 'checkout' ) || ! get_post( hb_get_page_id( 'checkout' ) ) )
		{
		    $pages['checkout'] = array(
		        'name'    => _x( 'room-checkout', 'room-checkout', 'tp-hotel-booking' ),
		        'title'   => _x( 'Checkout', 'Checkout', 'tp-hotel-booking' ),
		        'content' => '[' . apply_filters( 'hotel_booking_checkout_shortcode_tag', 'hotel_booking_checkout' ) . ']'
		    );
		}

		if( ! hb_get_page_id( 'search' ) || ! get_post( hb_get_page_id( 'search' ) ) )
		{
		    $pages['search'] = array(
		        'name'    => _x( 'hotel-booking', 'hotel-booking', 'tp-hotel-booking' ),
		        'title'   => _x( 'Hotel Booking', 'Hotel Booking', 'tp-hotel-booking' ),
		        'content' => '[' . apply_filters( 'hotel_booking_search_shortcode_tag', 'hotel_booking' ) . ']'
		    );
		}

		if( ! hb_get_page_id( 'terms' ) || ! get_post( hb_get_page_id( 'terms' ) ) )
		{
		    $pages['terms'] = array(
		        'name'    => _x( 'term-condition', 'term-condition', 'tp-hotel-booking' ),
		        'title'   => _x( 'Terms and Conditions ', 'Terms and Conditions', 'tp-hotel-booking' ),
		        'content' => apply_filters( 'hotel_booking_terms_content', 'Something notices' )
		    );
		}

		if( $pages && function_exists( 'hb_create_page' ) )
		{
		    foreach ( $pages as $key => $page ) {
		        $pageId = hb_create_page( esc_sql( $page['name'] ), 'hotel_booking_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? hb_get_page_id( $page['parent'] ) : '' );
		        hb_settings()->set( $key.'_page_id', $pageId );
		    }
		}

	}

	// create terms default for system
	static function create_terms() {
		if ( ! class_exists( 'HB_Post_Types' ) ) {
			TP_Hotel_Booking::instance()->_include( 'includes/class-hb-post-types.php' );
		}

		// register taxonomies
		HB_Post_Types::register_taxonomies();

		$taxonomies = array(
				'hb_room_capacity'	=> array(
						'double'	=> array(
								'hb_max_number_of_adults'	=> 2
							),
						'single'	=> array(
								'hb_max_number_of_adults'	=> 1,
								'alias_of'					=> 2
							)
					)
			);

		// insert term
		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term => $term_op ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					$term = wp_insert_term( $term, $taxonomy, array() );

					if ( ! is_wp_error( $term ) ) {
						foreach ( $term_op as $k => $v ) {
							add_term_meta( $term['term_id'], $k, $v, true );
						}
					}
				}
			}
		}
	}

	// create tables. Eg: booking_items
	static function create_tables( $network_wide = false ) {
		global $wpdb;
		if ( is_multisite() && $network_wide ) {
	        // store the current blog id
	        $current_blog = $wpdb->blogid;
	        // Get all blogs in the network and activate plugin on each one
	        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	        foreach ( $blog_ids as $blog_id ) {
	        	// each blog
	            switch_to_blog( $blog_id );

	            self::schema();

	            // restore
	            restore_current_blog();
	        }
	    } else {
	        self::schema();
	    }
	}

	// do create table
	static function schema() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "
			CREATE TABLE {$wpdb->prefix}hotel_booking_order_items (
				order_item_id bigint(20) NOT NULL AUTO_INCREMENT,
				order_item_name longtext NOT NULL,
				order_item_type varchar(255) NOT NULL,
				order_id bigint(20) NOT NULL,
				UNIQUE KEY order_item_id (order_item_id),
				PRIMARY KEY  (order_item_id)
			) $charset_collate;
			CREATE TABLE {$wpdb->prefix}hotel_booking_order_itemmeta (
				meta_id bigint(20) NOT NULL AUTO_INCREMENT,
				order_item_id bigint(20) NOT NULL,
				meta_key varchar(255) NULL,
				meta_value longtext NULL,
				UNIQUE KEY meta_id (meta_id),
				PRIMARY KEY  (meta_id),
				KEY order_item_id(order_item_id),
				KEY meta_key(meta_key)
			) $charset_collate;
		";

		return dbDelta( $sql );

	}

	// delete table when delete blog
	static function delete_tables( $tables ) {
		global $wpdb;
   		$tables[] = $wpdb->prefix . 'table_name';
		return $tables;
	}

	// create new table when create new blog multisite
	static function create_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		$plugin = basename( HB_PLUGIN_PATH ) . '/' . basename( HB_PLUGIN_PATH ) . '.php';
		if ( is_plugin_active_for_network( $plugin ) ) {
			// switch to current blog
	        switch_to_blog( $blog_id );

	        self::create_tables( true );

	        // restore
	        restore_current_blog();
	    }
	}

}
