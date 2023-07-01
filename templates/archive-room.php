<?php
/**
 * The template for displaying archive room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/archive-room.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

// if ( ! wp_is_block_theme() ) {
get_header();
// };
?>

<?php
/**
 * hotel_booking_before_main_content hook
 */
do_action( 'hotel_booking_before_main_content' );
?>

<?php
/**
 * hotel_booking_archive_description hook
 */
do_action( 'hotel_booking_archive_description' );
?>
    <div class="container room-container">
		<?php
		global $wp_query;

		$total          = $wp_query->queried_object->count ?? 0;
		$posts_per_page = $wp_query->query_vars['posts_per_page'];

		//Search Filter
		hb_get_template( 'search/v2/search-filter-v2.php', array( 'atts' => array() ) );


		//Sort By
		$sort_by = hb_get_request( 'sort_by' );

		$data = array(
			'sort_by' => $sort_by
		);

		if ( $total ) {
			$data['show_number'] = hb_get_show_room_text(
				array(
					'paged'         => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
					'total'         => $total,
					'item_per_page' => $posts_per_page
				)
			);
		}

		hb_get_template( 'search/v2/sort-by.php', compact( 'data' ) );
		?>
		<?php if ( have_posts() ) : ?>

			<?php
			/**
			 * hotel_booking_before_room_loop hook
			 */
			do_action( 'hotel_booking_before_room_loop' );

			?>
			<?php hotel_booking_room_loop_start(); ?>
			<?php hotel_booking_room_subcategories(); ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<?php hb_get_template_part( 'content', 'room' ); ?>

			<?php endwhile; ?>

			<?php hotel_booking_room_loop_end(); ?>

			<?php
			/**
			 * hotel_booking_after_room_loop hook
			 */
			do_action( 'hotel_booking_after_room_loop' );
			?>

		<?php endif; ?>
    </div>
<?php
/**
 * hotel_booking_after_main_content hook
 */
do_action( 'hotel_booking_after_main_content' );
?>

<?php
/**
 * hotel_booking_sidebar hook
 */
do_action( 'hotel_booking_sidebar' );
?>

<?php
// if ( ! wp_is_block_theme() ) {
get_footer();
// }
