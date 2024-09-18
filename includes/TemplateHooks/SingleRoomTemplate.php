<?php
/**
 * Template single room
 */

namespace WPHB\TemplateHooks;

use WPHB\Helpers\Singleton;

class SingleRoomTemplate {
	use Singleton;

	function init() {
		// TODO: Implement init() method.
	}

	public function html_rating_info( $rating, $count = 0 ) {
		$html = '';

		if ( 0 < $rating ) {
			/* translators: %s: rating */
			$label = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating );
			$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">' . $this->html_rating_star( $rating, $count ) . '</div>';
		}

		return apply_filters( 'wphb/room/html_rating_info', $html, $rating, $count );
	}

	public function html_rating_star( $rating, $count = 0 ) {
		$html = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%">';

		if ( 0 < $count ) {
			/* translators: 1: rating 2: rating count */
			$html .= sprintf(
				_n(
					'Rated %1$s out of 5 based on %2$s customer rating',
					'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'woocommerce'
				),
				'<strong class="rating">' . esc_html( $rating ) . '</strong>',
				'<span class="rating">' . esc_html( $count ) . '</span>' );
		} else {
			/* translators: %s: rating */
			$html .= sprintf( esc_html__( 'Rated %s out of 5', 'woocommerce' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>' );
		}

		$html .= '</span>';

		return apply_filters( 'wphb/room/html_rating_star', $html, $rating, $count );
	}
}

