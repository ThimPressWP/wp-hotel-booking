<?php
/**
 * Other room - Show related room for single pages.
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$room = HB_Room::instance( get_the_ID() );
$room->get_related_rooms();