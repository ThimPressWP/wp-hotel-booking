<?php
if ( ! isset( $data ) ) {
	return;
}

$args  = wp_parse_args(
	$data['number'],
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
    <ul class="list">
		<?php
		foreach ( $terms as $term ) {
			?>
            <li class="list-item">
                <div><a href="<?php echo get_term_link( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></a>
                </div>
                <div><?php echo esc_html( $term->count ); ?></div>
            </li>
			<?php
		}
		?>
    </ul>
</div>
