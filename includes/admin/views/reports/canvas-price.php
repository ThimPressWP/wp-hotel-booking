<?php
	$hb_report = HB_Report_Price::instance();
?>
<h3 class="chart_title"><?php _e( 'Report Chart Amount Total', 'tp-hotel-booking' ) ?></h3>
<canvas id="hotel_canvas_report_price"></canvas>
<script>

	(function($){
		window.onload = function(){
			var ctx = document.getElementById( 'hotel_canvas_report_price' ).getContext( '2d' );

			window.myLine = new Chart( ctx ).Line( <?php echo json_encode( $hb_report->series() ) ?>, {
				responsive: true
			});
		}
	})(jQuery);

</script>
