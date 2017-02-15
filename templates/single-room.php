<?php
/**
 * The Template for displaying all single products
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/single-room.php
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.6
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
get_header(); ?>

<?php
/**
 * hotel_booking_before_main_content hook
 */
do_action( 'hotel_booking_before_main_content' );
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php hb_get_template_part( 'content', 'single-room' ); ?>

<?php endwhile; // end of the loop. ?>

<?php
/**
 * hotel_booking_after_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'hotel_booking_after_main_content' );
?>

<?php
/**
 * hotel_booking_sidebar hook
 *
 * @hooked hotel_booking_sidebar - 10
 */
// do_action( 'hotel_booking_sidebar' );
?>

<?php get_footer(); ?>