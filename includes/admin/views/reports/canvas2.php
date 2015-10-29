<?php
	$hb_report = HB_Report::instance();
?>
<div id="tp-hotel-booking-chart-container">
	<div id="tp-hotel-booking-canvas-chart"></div>
</div>

<script type="text/javascript" src="http://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<script type="text/javascript">
	(function($){
		window.onload = function () {

			//Better to construct options first and then pass it as a parameter
			var options = {
				theme: "theme4",
				title: {
					text: "<?php printf( 'Chart in %s to %s', $hb_report->_start_in, $hb_report->_end_in ) ?>",
					padding: 25,
					margin: 10,
					fontSize: 30,
					fontFamily: "tahoma",
					fontWeight: "normal",
        			horizontalAlign: "center",
				},
		      	toolTip:{
			        enabled: true,       //disable here
			        animationEnabled: true, //disable here
		      	},
                animationEnabled: true,
				axisY:{
					valueFormatString:"$#",
					// interval: 100,
					lineColor: '#e74c3c',
					gridThickness: 1,
					gridDashType: "dot",
					labelAngle: 30,
					labelFontFamily: "tahoma",
					labelFontColor: "#e74c3c"
				},
				axisX: {
					interval: 1,
					intervalType: "<?php echo esc_js($hb_report->chart_groupby) ?>",
					includeZero: true,
					lineColor: '#e74c3c',
					labelFontFamily: "tahoma",
					labelFontColor: "#e74c3c",
					maximum: "<?php echo esc_js($hb_report->_axis_x['maximum']) ?>",
					minimum: "<?php echo esc_js($hb_report->_axis_x['minimum']) ?>",
				},
				data: [
				{
					type: "spline", //change it to line, area, bar, pie, etc
					lineThickness: 1,
					color: "#e74c3c",
					dataPoints: <?php echo json_encode( $hb_report->getOrdersItems() ) ?>
				}
				]
			};

			$("#tp-hotel-booking-canvas-chart").CanvasJSChart(options);

		}
	})(jQuery);
</script>