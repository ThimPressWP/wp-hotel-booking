<?php

	$hb_report = HB_Report::instance();

?>
<div id="tp-hotel-booking-chart-container">
	<?php
		$books = array();
		foreach ( $hb_report->getOrdersItems() as $key => $book) {
			$book = HB_Booking::instance($book->ID);
			$books[] = array(
					'ID'			=> $book->post->ID,
					'total' 		=> get_post_meta( $book->post->ID, '_hb_total', true ),
					'completed_time'=> $book->completed_time,
					'cutomer_id'	=> get_post_meta( $book->post->ID, '_hb_customer_id', true )
				);
		}
	?>
</div>