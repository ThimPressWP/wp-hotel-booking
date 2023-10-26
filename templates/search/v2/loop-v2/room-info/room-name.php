<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<h4 class="hb-room-name">
	<a href="<?php echo get_the_permalink( $room->ID ); ?>">
		<?php echo esc_html( $room->name ); ?><?php // $room->capacity_title ? printf( '(%s)', $room->capacity_title ) : ''; ?>
	</a>
</h4>
