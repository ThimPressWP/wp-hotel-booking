<?php
	$hb_report_room = HB_Report_Room::instance();
	$hb_report_room->getOrdersItems();
?>
<div id="tp-hotel-booking-chart-container">
	<div id="tp-hotel-booking-canvas-chart"></div>
</div>

<script type="text/javascript">
	(function($){
		$('#tp-hotel-booking-canvas-chart').highcharts({
	            chart: {
	                zoomType: 'x'
	            },
	            title: {
	                text: "<?php echo esc_js( $hb_report_room->_title ) ?>"
	            },
	            subtitle: {
	                text: document.ontouchstart === undefined ?
	                        "<?php _e('Click and drag in the plot area to zoom in', 'tp-hotel-booking') ?>" : "<?php _e('Pinch the chart to zoom in', 'tp-hotel-booking') ?>"
	            },
	            xAxis: {
	                type: 'datetime',
	            },
	            yAxis: {
	                title: {
	                    text: '<?php echo esc_js( ucfirst($hb_report_room->_chart_type) ) ?>'
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

	            series: <?php echo json_encode( $hb_report_room->series() ) ?>
	        });
	})(jQuery);
</script>
<?php //var_dump($hb_report->getOrdersItems()) ?>