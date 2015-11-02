<?php
	$hb_report_room = HB_Report_Room::instance();
	$series = $hb_report_room->series();
?>
<div id="tp-hotel-booking-chart-container">
	<div id="tp-hotel-booking-canvas-chart"></div>
</div>

<script type="text/javascript">
	(function($){
		var options = {
	        chart: {
	            type: 'column'
	        },

	        title: {
	            text: '<?php echo esc_js( $hb_report_room->_title ) ?>'
	        },

	        xAxis: {
	        	type: 'datetime'
	        },

	        yAxis: {
	            allowDecimals: false,
	            min: 0,
	            title: {
	                text: '<?php echo esc_js( "Number of rooms" ) ?>'
	            }
	        },

	        tooltip: {
	            formatter: function () {
	                return '<b>' + '<?php echo esc_js( "Quantity: " ) ?>' + this.y + '</b><br/>' +
	                '<b>'+this.series.name+'</b>' + ': ' + this.y + '<br/>' +
	                    '<b><?php echo esc_js( "Total: " ) ?></b> ' + this.point.stackTotal;
	            }
	        },

	        plotOptions: {
	            column: {
	                stacking: 'normal'
	            }
	        },

	        series: <?php echo json_encode( $series ) ?>
	    };

	    <?php if( ! $hb_report_room->_rooms ): ?>

        	options.subtitle = {
                text: "<?php echo esc_js( 'Please select room to display report chart' ) ?>"
            }

        <?php elseif( ! $series ): ?>

	        options.subtitle = {
	                text: "<?php echo esc_js( 'No results, room search have no order. Try with other rooms.' ) ?>"
	            }

        <?php endif; ?>
		$('#tp-hotel-booking-canvas-chart').highcharts(options);
	})(jQuery);
</script>