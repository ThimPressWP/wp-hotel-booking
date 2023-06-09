<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<div class="<?php echo esc_attr( apply_filters( 'wphb/filter/loop-v2/room-info/class', 'hb-room-info' ) ); ?>">
	<?php do_action( 'wphb/loop-v2/room-info', $room ); ?>
</div>
