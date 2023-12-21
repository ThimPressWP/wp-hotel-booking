<?php

add_filter(
	'thim_ekit/rest_api/select_query_conditions',
	function ( $output, $type, $search ) {
		if ( $type === 'hb_room_type' ) {
			$taxonomy = array();

			if ( $type === 'hb_room_type' || 'hb_room_type' === $type ) {
				$taxonomy[] = 'hb_room_type';
			}

			$terms = get_terms(
				array(
					'hide_empty' => false,
					'fields'     => 'all',
					'taxonomy'   => $taxonomy,
					'search'     => $search,
				)
			);

			if ( count( $terms ) > 0 ) {
				foreach ( $terms as $term ) {
					$output[] = array(
						'id'    => $term->term_id,
						'title' => htmlspecialchars_decode( $term->name ) . ' (ID: ' . $term->term_id . ', Tax: ' . $term->taxonomy . ')',
					);
				}
			}
		}

		return $output;
	},
	10,
	3
);
