<?php
	$hb_report = HB_Report_Room::instance();
?>
<h3 class="chart_title"><?php _e( 'Report Chart Room Unavailable', 'tp-hotel-booking' ) ?></h3>
<canvas id="hotel_canvas_report_room"></canvas>
<script>
	(function($){
	    var randomScalingFactor = function() {
	        return Math.round(Math.random() * 100);
	    };

		window.onload = function(){
			var ctx = document.getElementById( 'hotel_canvas_report_room' ).getContext( '2d' );
			window.myBar = new Chart(ctx).Bar( <?php echo json_encode( $hb_report->js_data() ) ?>, {
				responsive : true,
				scaleGridLineColor : "rgba(0,0,0,.05)"
			});
		}

		$.datepicker.setDefaults({ dateFormat: hotel_booking_l18n.date_time_format });
        $('#tp-hotel-report-checkin').datepicker({
        	dateFormat		: hotel_booking_l18n.date_time_format,
			monthNames 	  	: hotel_booking_l18n.monthNames,
			monthNamesShort	: hotel_booking_l18n.monthNamesShort,
            onSelect: function(){
                var date = $(this).datepicker('getDate');

                $("#tp-hotel-report-checkout").datepicker( 'option', 'minDate', date)
            }
        });
        $('#tp-hotel-report-checkout').datepicker({
        	dateFormat		: hotel_booking_l18n.date_time_format,
			monthNames 	  	: hotel_booking_l18n.monthNames,
			monthNamesShort	: hotel_booking_l18n.monthNamesShort,
            onSelect: function(){
                var date = $(this).datepicker('getDate');
                $("#tp-hotel-report-checkin").datepicker( 'option', 'maxDate', date)
            }
        });
	})(jQuery);

</script>
