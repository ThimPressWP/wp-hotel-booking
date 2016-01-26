<?php
/**
 * Template File Email New Booking
 * @since 1.0.4
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<style type="text/css">
			.booking-table{
		        color: #444444;
		        background-color: #DDD;
		        font-family: verdana, arial, sans-serif;
		        font-size: 14px;
		        min-width: 800px;
		    }
		    .booking-table-head{
		        background-color: #F5F5F5;
		    }
		    .booking-table-head h3{
		        margin: 5px 0;
		    }
		    .booking-table-row{
		        background-color: #FFFFFF;
		    }
		    .booking-table .bold-text{
		        font-weight: bold;
		    }
		    .booking-table .text-align-right{
		        text-align: right;
		    }
		</style>
	</head>
	<body>
		<?php
			$booking_cart_params = $booking->get_cart_params();
			var_dump($booking_cart_params);
		?>
	</body>
</html>