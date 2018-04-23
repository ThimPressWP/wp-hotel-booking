<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'hb_template_path' ) ) {
	function hb_template_path() {
		return apply_filters( 'hb_template_path', 'wp-hotel-booking' );
	}
}

/**
 * get template part
 *
 * @param   string $slug
 * @param   string $name
 *
 * @return  string
 */
if ( ! function_exists( 'hb_get_template_part' ) ) {

	function hb_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", hb_template_path() . "/{$slug}-{$name}.php" ) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( WPHB_PLUGIN_PATH . "/templates/{$slug}-{$name}.php" ) ) {
			$template = WPHB_PLUGIN_PATH . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", hb_template_path() . "{$slug}.php" ) );
		}

		// Allow 3rd party plugin filter template file from their plugin
		if ( $template ) {
			$template = apply_filters( 'hb_get_template_part', $template, $slug, $name );
		}
		if ( $template && file_exists( $template ) ) {
			load_template( $template, false );
		}

		return $template;
	}
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 *
 * @return void
 */
if ( ! function_exists( 'hb_get_template' ) ) {

	function hb_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = hb_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );

			return;
		}
		// Allow 3rd party plugin filter template file from their plugin
		$located = apply_filters( 'hb_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'hb_before_template_part', $template_name, $template_path, $located, $args );

		if ( $located && file_exists( $located ) ) {
			include( $located );
		}

		do_action( 'hb_after_template_part', $template_name, $template_path, $located, $args );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        yourtheme        /    $template_path    /    $template_name
 *        yourtheme        /    $template_name
 *        $default_path    /    $template_name
 *
 * @access public
 *
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 *
 * @return string
 */
if ( ! function_exists( 'hb_locate_template' ) ) {

	function hb_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		if ( ! $template_path ) {
			$template_path = hb_template_path();
		}

		if ( ! $default_path ) {
			$default_path = WPHB_PLUGIN_PATH . '/templates/';
		}

		$template = null;
		// Look within passed path within the theme - this is priority
		// if( hb_enable_overwrite_template() ) {
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);
		// }
		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters( 'hb_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'hb_get_template_content' ) ) {

	function hb_get_template_content( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		hb_get_template( $template_name, $args, $template_path, $default_path );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'hb_enqueue_lightbox_assets' ) ) {

	function hb_enqueue_lightbox_assets() {
		do_action( 'hb_lightbox_assets_lightbox2' );
	}
}

if ( ! function_exists( 'hb_lightbox_assets_lightbox2' ) ) {

	function hb_lightbox_assets_lightbox2() {
		wp_enqueue_script( 'lightbox2', WP_Hotel_Booking::instance()->plugin_url( 'includes/libraries/lightbox/lightbox2/js/lightbox.min.js' ) );
		wp_enqueue_style( 'lightbox2', WP_Hotel_Booking::instance()->plugin_url( 'includes/libraries/lightbox/lightbox2/css/lightbox.min.css' ) );
		?>
        <script type="text/javascript">
            jQuery(function () {

            });
        </script>
		<?php

	}
}

if ( ! function_exists( 'hb_display_message' ) ) {

	function hb_display_message() {
		hb_get_template( 'messages.php' );
	}

}

/* * **************************************************************** Loop ***************************************************************** */

if ( ! function_exists( 'hotel_booking_page_title' ) ) {

	/**
	 * hotel_booking_page_title function.
	 *
	 * @param  boolean $echo
	 *
	 * @return string
	 */
	function hotel_booking_page_title( $echo = true ) {

		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'wp-hotel-booking' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'wp-hotel-booking' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_tax() ) {

			$page_title = single_term_title( "", false );
		} else {

			$shop_page_id = hb_get_page_id( 'shop' );
			$page_title   = get_the_title( $shop_page_id );
		}

		$page_title = apply_filters( 'hotel_booking_page_title', $page_title );

		if ( $echo ) {
			echo sprintf( '%s', $page_title );
		} else {
			return $page_title;
		}
	}

}

if ( ! function_exists( 'hotel_booking_room_loop_start' ) ) {

	/**
	 * Output the start of a room loop. By default this is a UL
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function hotel_booking_room_loop_start( $echo = true ) {
		ob_start();
		hb_get_template( 'loop/loop-start.php' );
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

}
if ( ! function_exists( 'hotel_booking_room_loop_end' ) ) {

	/**
	 * Output the end of a room loop. By default this is a UL
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function hotel_booking_room_loop_end( $echo = true ) {
		ob_start();

		hb_get_template( 'loop/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

}
if ( ! function_exists( 'hotel_booking_template_loop_room_title' ) ) {

	/**
	 * Show the room title in the room loop. By default this is an H3
	 */
	function hotel_booking_template_loop_room_title() {
		hb_get_template( 'loop/title.php' );
	}

}
if ( ! function_exists( 'hotel_booking_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives
	 *
	 * @subpackage  Archives
	 */
	function hotel_booking_taxonomy_archive_description() {
		if ( is_tax( array( 'room_cat', 'room_tag' ) ) && get_query_var( 'paged' ) == 0 ) {
			$description = hb_format_content( term_description() );
			if ( $description ) {
				echo '<div class="term-description">' . $description . '</div>';
			}
		}
	}

}
if ( ! function_exists( 'hotel_booking_room_archive_description' ) ) {

	/**
	 * Show a shop page description on room archives
	 *
	 * @subpackage  Archives
	 */
	function hotel_booking_room_archive_description() {
		if ( is_post_type_archive( 'room' ) && get_query_var( 'paged' ) == 0 ) {
			$shop_page = get_post( hb_get_page_id( 'shop' ) );
			if ( $shop_page ) {
				$description = hb_format_content( $shop_page->post_content );
				if ( $description ) {
					echo '<div class="page-description">' . $description . '</div>';
				}
			}
		}
	}

}

if ( ! function_exists( 'hotel_booking_room_subcategories' ) ) {

	/**
	 * Display product sub categories as thumbnails.
	 *
	 * @subpackage  Loop
	 *
	 * @param array $args
	 *
	 * @return null|boolean
	 */
	function hotel_booking_room_subcategories( $args = array() ) {

	}

}

/* =====================================================
  =                      template hooks                  =
  ===================================================== */
if ( ! function_exists( 'hotel_booking_before_main_content' ) ) {

	function hotel_booking_before_main_content() {
		return;
	}

}

if ( ! function_exists( 'hotel_booking_after_main_content' ) ) {

	// others room block
	function hotel_booking_after_main_content() {
		return;
	}

}

if ( ! function_exists( 'hotel_booking_sidebar' ) ) {

	function hotel_booking_sidebar() {
		return;
	}

}

if ( ! function_exists( 'hotel_booking_loop_room_thumbnail' ) ) {

	function hotel_booking_loop_room_thumbnail() {
		hb_get_template( 'loop/thumbnail.php' );
	}

}

if ( ! function_exists( 'hotel_booking_room_title' ) ) {

	function hotel_booking_room_title() {
		hb_get_template( 'loop/title.php' );
	}

}

if ( ! function_exists( 'hotel_booking_loop_room_price' ) ) {

	function hotel_booking_loop_room_price() {
		hb_get_template( 'loop/price.php' );
	}

}

if ( ! function_exists( 'hotel_booking_after_room_loop' ) ) {

	function hotel_booking_after_room_loop() {
		hb_get_template( 'pagination.php' );
	}

}

if ( ! function_exists( 'hotel_booking_single_room_gallery' ) ) {

	function hotel_booking_single_room_gallery() {
		hb_get_template( 'single-room/gallery.php' );
	}

}

if ( ! function_exists( 'hotel_booking_single_room_infomation' ) ) {

	function hotel_booking_single_room_infomation() {
		hb_get_template( 'single-room/details.php' );
	}

}

if ( ! function_exists( 'hb_comments' ) ) {

	/**
	 * Output the Review comments template.
	 *
	 * @param WP_Comment object
	 * @param mixed
	 * @param int
	 */
	function hb_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		hb_get_template( 'single-room/review.php', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth ) );
	}

}

if ( ! function_exists( 'hb_body_class' ) ) {

	function hb_body_class( $classes ) {

		global $post;

		if ( ! isset( $post->ID ) ) {
			return $classes;
		}

		$classes = (array) $classes;

		switch ( $post->ID ) {
			case hb_get_page_id( 'rooms' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-rooms';
				break;
			case hb_get_page_id( 'cart' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-cart';
				break;
			case hb_get_page_id( 'checkout' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-checkout';
				break;
			case hb_get_page_id( 'search' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-search-rooms';
				break;
			case hb_get_page_id( 'account' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-account';
				break;
			case hb_get_page_id( 'terms' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-terms';
				break;
			case hb_get_page_id( 'thankyou' ):
				$classes[] = 'wp-hotel-booking-page';
				$classes[] = 'wp-hotel-booking-thank-you';
				break;
			default:
				break;
		}

		if ( is_room() || is_room_taxonomy() ) {
			$classes[] = 'wp-hotel-booking';
			$classes[] = 'wp-hotel-booking-room-page';
		}

		return array_unique( $classes );
	}

}

if ( ! function_exists( 'hotel_booking_single_room_related' ) ) {
	/*
	 * related room
	 * @return html
	 */

	function hotel_booking_single_room_related() {
		hb_get_template( 'single-room/related-room.php' );
	}

}

if ( ! function_exists( 'hotel_booking_num_room_archive' ) ) {

	function hotel_booking_num_room_archive( $query ) {
		if ( ! is_admin() && isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] === 'hb_room' && is_archive() ) {
			global $hb_settings;
			$query->set( 'posts_per_page', $hb_settings->get( 'posts_per_page', 8 ) );
		}

		return $query;
	}

}

if ( ! function_exists( 'hotel_booking_remove_widget_search' ) ) {

	function hotel_booking_remove_widget_search( $sidebar_widgets ) {
		global $post;

		if ( ! is_page() ) {
			return $sidebar_widgets;
		}

		if ( ! $post->ID == hb_get_page_id( 'search' ) ) {
			return $sidebar_widgets;
		}

		foreach ( $sidebar_widgets as $sidebarID => $widgets ) {
			foreach ( $widgets as $key => $widget ) {
				if ( strpos( $widget, 'hb_widget_search' ) === 0 ) {
					unset( $sidebar_widgets[ $sidebarID ][ $key ] );
				}
			}
		}

		return $sidebar_widgets;
	}

}

if ( ! function_exists( 'hotel_booking_loop_room_rating' ) ) {

	function hotel_booking_loop_room_rating() {
		global $hb_room;
		global $hb_settings;
		if ( $hb_settings->get( 'catalog_display_rating' ) ) {
			hb_get_template( 'loop/rating.php', array( 'rating' => $hb_room->average_rating() ) );
		}
	}

}

if ( ! function_exists( 'hotel_booking_after_loop_room_item' ) ) {

	function hotel_booking_after_loop_room_item() {
		global $hb_settings;
		if ( $hb_settings->get( 'enable_gallery_lightbox' ) ) {
			hb_get_template( 'loop/gallery-lightbox.php' );
		}
	}

}

if ( ! function_exists( 'hb_setup_shortcode_page_content' ) ) {

	function hb_setup_shortcode_page_content( $content ) {
		global $post;

		$page_id = $post->ID;

		if ( ! $page_id ) {
			return $content;
		}

		if ( hb_get_page_id( 'rooms' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_rooms_shortcode_tag', 'hotel_booking_rooms' ) . ']';
		} else if ( hb_get_page_id( 'cart' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_cart_shortcode_tag', 'hotel_booking_cart' ) . ']';
		} else if ( hb_get_page_id( 'checkout' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_checkout_shortcode_tag', 'hotel_booking_checkout' ) . ']';
		} else if ( hb_get_page_id( 'search' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_search_shortcode_tag', 'hotel_booking' ) . ']';
		} else if ( hb_get_page_id( 'account' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_account_shortcode_tag', 'hotel_booking_account' ) . ']';
		} else if ( hb_get_page_id( 'thankyou' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_thankyou_shortcode_tag', 'hotel_booking_thankyou' ) . ']';
		}

		return do_shortcode( $content );
	}

}
if ( ! function_exists( 'hotel_display_pricing_plans' ) ) {

	function hotel_display_pricing_plans( $tabs ) {
		if ( ! hb_settings()->get( 'display_pricing_plans' ) ) {
			return $tabs;
		}

		$tabs[] = array(
			'id'      => 'hb_room_pricing_plans',
			'title'   => __( 'Pricing Plans', 'wp-hotel-booking' ),
			'content' => ''
		);

		return $tabs;
	}

}

if ( ! function_exists( 'hotel_booking_edit_room_link' ) ) {
	function hotel_booking_edit_room_link() {
		if ( $user_id = get_current_user_id() ) {
			$user = get_user_by( 'id', $user_id );
			if ( $user->has_cap( 'edit_hb_rooms' ) ) { ?>
                <a href="<?php echo get_edit_post_link( get_the_ID() ); ?>"><?php _e( 'Edit', 'wp-hotel-booking' ); ?></a>
				<?php
			}
		}

		return '';
	}
}

if ( ! function_exists( 'hotel_show_pricing' ) ) {

	function hotel_show_pricing() {
		hb_get_template( 'loop/pricing_plan.php' );
	}

}
/*=====  End of template hooks  ======*/
add_action( 'wp_footer', 'hb_print_mini_cart_template' );
if ( ! function_exists( 'hb_print_mini_cart_template' ) ) {
	function hb_print_mini_cart_template() {
		echo hb_get_template_content( 'cart/mini_cart_layout.php' );
	}
}