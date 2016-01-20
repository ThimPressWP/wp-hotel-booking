<?php
	$hb_report = HB_Report_Price::instance();
	$sidebarInfo = apply_filters( 'tp_hotel_booking_sidebar_price_info', array() );
?>
<ul class="chart-legend">
	<?php foreach ( $sidebarInfo as $key => $mote ): ?>
		<li style="border-color: <?php echo hb_random_color() ?>">
			<span><b><?php echo $mote['title'] ?></b></span>
			<p class="amount"><?php echo $mote['descr'] ?></p>
		</li>
	<?php endforeach; ?>
</ul>
