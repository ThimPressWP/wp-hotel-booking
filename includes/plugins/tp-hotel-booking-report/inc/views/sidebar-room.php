<?php $hb_report_room = HB_Report_Room::instance(); ?>
<?php
	$selected = array();
	if( isset($_GET['room_id']) )
		$selected = (array)$_GET['room_id'];
?>
<form method="GET">

	<h4><?php _e( 'Rooms Search', 'tp-hotel-booking-report' ) ?></h4>
	<?php wp_nonce_field( 'tp-hotel-booking-report', 'tp-hotel-booking-report' ); ?>
	<input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? sanitize_text_field( $_GET['page'] ) : '' ?>" />
	<input type="hidden" name="tab" value="room" />
	<input type="hidden" name="range" value="<?php echo isset( $_GET['range'] ) ? esc_attr( sanitize_text_field( $_GET['range'] ) ) : '7day' ?>" />

	<?php if( isset($_GET['report_in']) && $_GET['report_in'] ): ?>
		<input type="hidden" name="report_in" value="<?php echo esc_attr( sanitize_text_field( $_GET['report_in'] ) ) ?>">
	<?php endif; ?>
	<?php if( isset($_GET['report_in_timestamp']) ): ?>
		<input type="hidden" name="report_in_timestamp" value="<?php echo isset($_GET['report_in_timestamp']) ? esc_attr( sanitize_text_field( $_GET['report_in_timestamp'] ) ) : '' ?>" >
	<?php endif; ?>

	<?php if( isset($_GET['report_out']) && $_GET['report_out'] ): ?>
		<input type="hidden" name="report_out" value="<?php echo esc_attr( sanitize_text_field( $_GET['report_out'] ) ) ?>" />
	<?php endif; ?>
	<?php if( isset($_GET['report_out_timestamp']) ): ?>
		<input type="hidden" name="report_out_timestamp" value="<?php echo isset($_GET['report_out_timestamp']) ? esc_attr( sanitize_text_field( $_GET['report_out_timestamp'] ) ) : '' ?>" >
	<?php endif; ?>

	<?php $rooms = $hb_report_room->get_rooms(); ?>
	<select name="room_id[]" id="tp-hotel-booking-room_id" multiple="multiple" class="tokenize-sample">
	    <?php foreach( (array)$rooms as $key => $room ): ?>
	    	<option value="<?php echo esc_attr( $room->ID ) ?>"<?php echo ( in_array($room->ID, $selected) ) ? ' selected' : '' ?>><?php printf( '%s', $room->post_title ) ?></option>
	    <?php endforeach; ?>
	</select>
	<p>
		<button type="submit" class="button"><?php _e( 'Show', 'tp-hotel-booking-report' ) ?></button>
	</p>

</form>

<script type="text/javascript">
	(function($){
		$('#tp-hotel-booking-room_id').tokenize();
	})(jQuery);
</script>
