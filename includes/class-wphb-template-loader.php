<?php
/**
 * WP Hotel Booking template loader.
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

if ( ! class_exists( 'WPHB_TemplateLoader' ) ) {
	/**
	 * Class WPHB_TemplateLoader
	 */
	class WPHB_TemplateLoader {

		/**
		 * The Constructor
		 */
		public function __construct() {
			add_filter( 'template_include', array( $this, 'template_loader' ) );
		}

		/**
		 * @param $template
		 *
		 * @return string
		 */
		public function template_loader( $template ) {
			$post_type = get_post_type();

			$file = '';
			$find = array();
			if ( $post_type !== 'hb_room' ) {
				return $template;
			}

			if ( is_post_type_archive( 'hb_room' ) ) {
				$file   = 'archive-room.php';
				$find[] = $file;
				$find[] = hb_template_path() . '/' . $file;
			} elseif ( is_room_taxonomy() ) {
				$term     = get_queried_object();
				$taxonomy = $term->taxonomy;
				if ( strpos( $term->taxonomy, 'hb_' ) === 0 ) {
					$taxonomy = substr( $term->taxonomy, 3 );
				}

				if ( is_tax( 'hb_room_type' ) || is_tax( 'hb_room_capacity' ) ) {
					$file = 'taxonomy-' . $taxonomy . '.php';
				} else {
					$file = 'archive-room.php';
				}

				$find[] = 'taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
				$find[] = hb_template_path() . '/taxonomy-' . $taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = hb_template_path() . '/taxonomy-' . $taxonomy . '.php';
				$find[] = $file;
			} elseif ( is_single() ) {
				$file   = 'single-room.php';
				$find[] = $file;
				$find[] = hb_template_path() . '/' . $file;
			}

			if ( $file ) {
				$find[]      = hb_template_path() . '/' . $file;
				$hb_template = untrailingslashit( WPHB_PLUGIN_PATH ) . '/templates/' . $file;
				$template    = locate_template( array_unique( $find ) );

				if ( ! $template && file_exists( $hb_template ) ) {
					$template = $hb_template;
				}
			}

			return $template;
		}
	}
}

new WPHB_TemplateLoader();
