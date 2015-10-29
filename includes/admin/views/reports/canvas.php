<?php
	$hb_report = HB_Report::instance();
?>
<div id="tp-hotel-booking-chart-container">
	<?php
		$books = array();
		$orders = $hb_report->getOrdersItems();
		var_dump($orders); die();
		foreach ( $orders as $key => $book) {
			$book = HB_Booking::instance($book->ID);
			// date( 'N', (int)$book->completed_time );
			$books[] = array(
					'ID'			=> $book->post->ID,
					'total' 		=> get_post_meta( $book->post->ID, '_hb_total', true ),
					'completed_time'=> (int)$book->completed_time,
					'cutomer_id'	=> get_post_meta( $book->post->ID, '_hb_customer_id', true )
				);
		}
	?>
	<canvas id="tp-hotel-booking-canvas-chart"></canvas>
</div>
<script type="text/javascript">
	window.onload = function ()
    {
        var data = [4,8,6,3,5,2,6,8,4,5,7,8];

        var bar = new RGraph.Bar({
            id: 'cvs',
            data: data,
            options: {
                backgroundGridAutofitNumvlines: 0,
                linewidth: 0,
                shadow: false,
                hmargin: 10,
                colors: ['Gradient(pink:red:#f33)', 'Gradient(green:#0f0)'],
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                clearto: 'white',
                variant: '3d',
                gutterBottom: 90
            }
        }).wave({frames: 60});
    };
</script>