<?php
	global $hb_report;
	$series = $hb_report->series();
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
	            text: '<?php echo esc_js( $hb_report->_title ) ?>'
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

	        plotOptions: {
	            column: {
	                stacking: 'normal'
	            }
	        },

	        series: <?php echo json_encode( $series ) ?>
	    };

	    <?php if( ! $hb_report->_rooms ): ?>

        	options.subtitle = {
                text: "<?php echo esc_js( 'Please select room to display report chart' ) ?>"
            }

        <?php elseif( ! $series ): ?>

	        options.subtitle = {
	                text: "<?php echo esc_js( 'No results, room search have no order. Try with other rooms.' ) ?>"
	            }

        <?php endif; ?>

        <?php if( $hb_report->chart_groupby === 'day' ) : ?>

	        options.tooltip = {
	            formatter: function () {
	                return '<b>'+this.series.name+'</b>' + ': ' + this.y + '<br/>' +
	                    '<b><?php echo esc_js( "Total: " ) ?></b> ' + this.point.stackTotal;
	            }
	        }

        <?php else: ?>

        	options.tooltip = {
	            formatter: function () {
	                return '<b>'+this.series.name+'</b>' + ': ' + this.y + '<br/>';
	            }
	        }

        <?php endif; ?>
		$('#tp-hotel-booking-canvas-chart').highcharts(options);
	})(jQuery);
</script>