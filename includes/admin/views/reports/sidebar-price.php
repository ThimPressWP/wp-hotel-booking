<?php global $hb_report; ?>
<ul class="chart-legend">
	<?php foreach ( $hb_report->_query_results as $key => $mote ): ?>
		<li style="border-color: <?php echo hb_random_color() ?>">
			<span><b><?php echo $hb_report->date_format( $mote->completed_date ) ?></b></span>
			<p class="amount"><?php echo hb_format_price( $mote->total ) ?></p>
		</li>
	<?php endforeach; ?>
</ul>