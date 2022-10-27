<?php
/**
 * The template for displaying search room results api v2.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/results-v2.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( empty( $results ) || empty( $atts ) ) {
	return;
}

hb_get_template(
	'search/v2/list-v2.php',
	array(
		'results' => $results['data'],
		'atts'    => $atts,
	)
);
?>

