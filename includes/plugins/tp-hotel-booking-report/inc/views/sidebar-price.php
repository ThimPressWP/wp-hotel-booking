<?php
	$hb_report = HB_Report_Price::instance();
	$sidebarInfo = apply_filters( 'tp_hotel_booking_sidebar_price_info', array() );
?>
<ul class="chart-legend">
	<?php foreach ( $sidebarInfo as $key => $mote ): ?>
		<li style="border-color: <?php echo esc_attr( hb_random_color() ); ?>">
			<span><b><?php echo esc_html( $mote['title'] ); ?></b></span>
			<p class="amount"><?php echo sprintf( '%s', $mote['descr'] ) ?></p>
		</li>
	<?php endforeach; ?>
</ul>
