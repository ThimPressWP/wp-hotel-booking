<?php
/**
 * The template for displaying search room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/search-page.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9.7
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( hb_get_request( 'is_page_room_extra' ) == 'select-room-extra' && ! ( isset( $atts['widget_search'] ) && $atts['widget_search'] ) ) {

	hb_get_template( 'search/select-extra.php' );

} else {

	hb_get_template( 'search/search-form.php', $atts );

}