<form method="GET">

	<h4><?php _e( 'Rooms Search', 'tp-hotel-booking' ) ?></h4>
	<ul id="tp-hotel-booking-search">
		<?php if( isset($_REQUETS['room_id']) && $_REQUETS['room_id'] ): ?>
			<?php $rooms = explode(',', $_REQUETS['room_id']) ?>
			<?php foreach( $_REQUETS['room_id'] as $key => $id ): ?>

				<li><?php echo trim($id) ?></li>

			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<input type="hidden" name="tab" value="room" />
	<input type="hidden" name="range" value="<?php echo isset( $_REQUETS['range'] ) ? $_REQUETS['range'] : '7day' ?>" />

	<?php if( isset($_REQUETS['room_id']) && $_REQUETS['room_id'] ): ?>
		<input id="tp-hotel-booking-room_id" type="hidden" name="room_id" value="<?php echo esc_attr( $_REQUETS['room_id'] ); ?>">
	<?php endif; ?>

	<p>
		<button type="submit"><?php _e( 'Show', 'tp-hotel-booking' ) ?></button>
	</p>

</form>
<script type="text/javascript">
	(function($){
		var rooms = '';
		$('#tp-hotel-booking-search').tagit({
            availableTags: [{value:1,label:'Pizza'},{value:2,label:'Burger'},{value:3,label:'Salad'}],
            singleField: true,
            singleFieldNode: $('#tp-hotel-booking-room_id')
        });
	})(jQuery);
</script>