<?php
/**
 * The template for displaying archive room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/archive-room.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6.1
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

	<?php
	/**
	 * @see ArchiveRoomTemplate
	 * wphb/list-rooms/layout hook
	 */
		do_action( 'wphb/list-rooms/layout' );
	?>

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