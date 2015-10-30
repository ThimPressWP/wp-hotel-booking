<?php
	$hb_report = HB_Report::instance();
?>
<div id="tp-hotel-booking-chart-container">
	<div id="tp-hotel-booking-canvas-chart"></div>
</div>

<script src="http://dev.foobla.com/sailing/wp-content/plugins/tp-hotel-booking/includes/admin/views/reports/highcharts.js"></script>
<script type="text/javascript">
	(function($){
		$('#tp-hotel-booking-canvas-chart').highcharts({
	            chart: {
	                zoomType: 'x'
	            },
	            title: {
	                text: "<?php echo esc_js( $hb_report->_title ) ?>"
	            },
	            subtitle: {
	                text: document.ontouchstart === undefined ?
	                        "<?php _e('Click and drag in the plot area to zoom in', 'tp-hotel-booking') ?>" : "<?php _e('Pinch the chart to zoom in', 'tp-hotel-booking') ?>"
	            },
	            xAxis: {
	                type: 'datetime',
		            minTickInterval: 3600*24*1000,//time in milliseconds
				    minRange: 3600*24*1000,
				    ordinal: false //this sets the fixed time formats
	            },
	            yAxis: {
	                title: {
	                    text: '<?php echo esc_js( ucfirst($hb_report->_chart_type) ) ?>'
	                }
	            },
	            legend: {
	                enabled: false
	            },
	            tooltip: {
		            headerFormat: '<b>{point.x:%e. %b}</b><br>',
		            pointFormat: '<b><?php _e( "Total", "tp-hotel-booking" ) ?>:</b> ${point.y:.2f}'
		        },
	            plotOptions: {
	                area: {
	                    fillColor: {
	                        linearGradient: {
	                            x1: 0,
	                            y1: 0,
	                            x2: 0,
	                            y2: 1
	                        },
	                        stops: [
	                            [0, Highcharts.getOptions().colors[0]],
	                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
	                        ]
	                    },
	                    marker: {
	                        radius: 2
	                    },
	                    lineWidth: 1,
	                    states: {
	                        hover: {
	                            lineWidth: 1
	                        }
	                    },
	                    threshold: null
	                }
	            },

	            series: <?php echo json_encode( $hb_report->series() ) ?>
	        });
	})(jQuery);
</script>
<?php //var_dump($hb_report->getOrdersItems()) ?>