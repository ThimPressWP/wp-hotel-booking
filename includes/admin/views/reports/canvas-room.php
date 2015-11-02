<?php
	$hb_report_room = HB_Report_Room::instance();
	$hb_report_room->series();
?>
<div id="tp-hotel-booking-chart-container">
	<div id="tp-hotel-booking-canvas-chart"></div>
</div>

<script type="text/javascript">
	(function($){
		$('#tp-hotel-booking-canvas-chart').highcharts({
	        chart: {
	            type: 'column'
	        },

	        title: {
	            text: 'Total fruit consumtion, grouped by gender'
	        },

	        xAxis: {
	        	type: 'datetime',
                // dateTimeLabelFormats: { // don't display the dummy year
                //       day: '%e. %b',
                // }
	        },

	        yAxis: {
	            allowDecimals: false,
	            min: 0,
	            title: {
	                text: 'Number of fruits'
	            }
	        },

	        tooltip: {
	            formatter: function () {
	                return '<b>' + this.x + '</b><br/>' +
	                    this.series.name + ': ' + this.y + '<br/>' +
	                    'Total: ' + this.point.stackTotal;
	            }
	        },

	        plotOptions: {
	            column: {
	                stacking: 'normal'
	            }
	        },

	        series: [
	        	{
            		name: 'serie1',
		            data: [
							[Date.UTC(1970,  9, 18), 1   ],
							[Date.UTC(1970,  9, 19), 2   ],
							[Date.UTC(1970,  9, 20), 2   ],
							[Date.UTC(1970,  9, 21), 1   ]
		                ],
		            stack: 0
		        }, {
		            name: 'serie2',
		            data: [
		                 [Date.UTC(1970,  9, 18), 1   ],
		                 [Date.UTC(1970,  9, 19), 2   ],
		                 [Date.UTC(1970,  9, 20), 2   ],
		                 [Date.UTC(1970,  9, 21), 1   ]
		                ],
		            stack: 0
		        },
		        // second stack
		        {
		            name: 'serie3',
		            data: [
		                 [Date.UTC(1970,  9, 18), 1   ],
		                 [Date.UTC(1970,  9, 19), 2   ],
		                 [Date.UTC(1970,  9, 20), 2   ],
		                 [Date.UTC(1970,  9, 21), 1   ]
		                ],
		            stack: 1
		        }, {
		            name: 'serie4',
		            data: [
		                 [Date.UTC(1970,  9, 18), 1   ],
		                 [Date.UTC(1970,  9, 19), 2   ],
		                 [Date.UTC(1970,  9, 20), 2   ],
		                 [Date.UTC(1970,  9, 21), 1   ]
		                ],
		            stack: 1
		        }
	        ]
	    });
	})(jQuery);
</script>