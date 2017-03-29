<?php
/**
 * The Template for displaying archive room page
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/taxonomy-room_type.php
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.6
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

hb_get_template_part( 'archive', 'room' );