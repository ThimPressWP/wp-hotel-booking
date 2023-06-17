<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<ul class="<?php echo esc_attr( apply_filters( 'wphb/filter/loop-v2/room-meta/class', 'hb-room-meta' ) ); ?>">
	<?php do_action( 'wphb/loop-v2/room-meta', $room ); ?>
</ul>
