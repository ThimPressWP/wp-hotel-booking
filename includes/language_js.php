<?php

$language = array(
		'message'	=> array(),
		'waring'	=> array(
				'room_select'	=> __( 'Please select room number', 'tp-hotel-booking' ),
				'try_again'		=> __( 'Plesae try again!', 'tp-hotel-booking' )
			),
	);
?>
<script type="text/javascript">
	hotel_settings_language = <?php echo json_encode($language); ?>,
	hotel_settings_cart = <?php echo ( ! isset($_SESSION['hb_cart']) && empty( $_SESSION['hb_cart'] ) ) ? 'false' : 'true' ?>
</script>