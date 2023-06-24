<?php
if ( ! isset( $data ) ) {
	return;
}

$args = wp_parse_args(
	$data,
	array(
		'hide_empty' => false,
		'taxonomy'   => 'hb_room_type'
	)
);

$terms = get_terms( $args );

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

?>
<div class="hb-type-field">
    <h4><?php esc_html_e( ' Room types', 'wp-hotel-booking' ); ?></h4>
    <ul class="room-type-list">
		<?php
		foreach ( $terms as $term ) {
			?>
            <li class="list-item">
                <div class="room-type">
                    <label>
                        <input type="checkbox" name="room_type" value="<?php echo esc_attr( $term->term_id ); ?>">
                        <span><?php echo esc_html( $term->name ); ?></span>
                    </label>
                </div>
                <div class="room-type-number">
                    <a href="<?php echo get_term_link( $term->term_id ); ?>"><?php echo esc_html( $term->count ); ?></a>
                </div>
            </li>
			<?php
		}
		?>
    </ul>
</div>
