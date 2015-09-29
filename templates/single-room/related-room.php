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

$post_Id = get_the_ID();
$room_types = get_the_terms( $post_Id, 'hb_room_type' );
$room_capacity = (int)get_post_meta( $post_Id, '_hb_room_capacity', true );
$max_adults_per_room = (int)get_post_meta( $post_Id, '_hb_max_adults_per_room', true );
$max_child_per_room = (int)get_post_meta( $post_Id, '_hb_max_child_per_room', true );

$taxonomis = array();
foreach ($room_types as $key => $tax) {
	$taxonomis[] = $tax->term_id;
}
$args = array(
		'post_type'		=> 'hb_room',
		'status'		=> 'publish',
		'meta_query'	=> array(
				array(
		            'key' 		=> '_hb_max_adults_per_room',
		            'value' 	=> $max_adults_per_room,
		            'compare' 	=> '<=',
		        ),
		        array(
		            'key' 		=> '_hb_max_child_per_room',
		            'value' 	=> $max_child_per_room,
		            'compare'	=> '<='
		        ),
			),
		'tax_query' => array(
				array(
					'taxonomy' => 'hb_room_type',
					'field'    => 'term_id',
					'terms'    => $taxonomis,
				),
			),
	);
$query = new WP_Query( $args );