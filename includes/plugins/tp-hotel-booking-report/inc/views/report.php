<?php
	global $hb_report;
	/* allow hook */
	$report_tabs = apply_filters( 'tp_hotel_booking_report_tab', array(
		array(
				'id'		=> 'price',
				'title'		=> __( 'Booking Price', 'tp-hotel-booking' ),
			),
		array(
				'id'		=> 'room',
				'title'		=> __( 'Room availability', 'tp-hotel-booking' ),
			)
	));

	$currenttab = 'price';
	if( isset($_REQUEST['tab']) && $_REQUEST['tab'] )
		$currenttab = $_REQUEST['tab'];
?>

<ul class="tp_hotel_booking subsubsub">
	<?php $html = array(); ?>
	<?php foreach( $report_tabs as $key => $tab ): ?>

		<?php $html[] =
			'<li>
				<a '.( $tab['id'] === $currenttab ? 'class="current" ' : '' ).'href="'.admin_url( 'admin.php?page=tp_hotel_booking_report&tab='.$tab['id'] ).'" >'.sprintf( '%s', $tab['title'] ).'</a>
			</li>';
 		?>
	<?php endforeach; ?>
	<?php echo implode( ' | ', $html); ?>
</ul>

<p style="clear:both"></p>

<?php //$rooms = hb_get_rooms(); ?>
<?php
	$date = apply_filters( 'tp_hotel_booking_report_date', array(
		array(
				'id'	=> 'year',
				'title'	=> __('Year', 'tp-hotel-booking')
			),
		array(
				'id'	=> 'last_month',
				'title'	=> __('Last Month', 'tp-hotel-booking')
			),
		array(
				'id'	=> 'current_month',
				'title'	=> __('This Month', 'tp-hotel-booking')
			),
		array(
				'id'	=> '7day',
				'title'	=> __('Last 7 Days', 'tp-hotel-booking')
			)
	));

?>

<?php
	$currentRang = '7day';
	if( isset($_REQUEST['range']) && $_REQUEST['range'] )
		$currentRang = $_REQUEST['range'];
?>
<div id="tp-hotel-booking-report" class="postbox">

	<div id="poststuff">
		<h3>
			<!-- <a href="<?php //echo admin_url( 'admin.php?page=tp_hotel_booking_report&action=export' ) ?>" class="export_csv"><?php //_e( 'Export CSV', 'tp-hotel-booking' ) ?></a> -->
			<!--export-->
			<form id="tp-hotel-booking-export" method="POST">
				<input type="hidden" name="page" value="<?php echo isset($_REQUEST['page']) ? esc_attr($_REQUEST['page']) : '' ?>" >
				<input type="hidden" name="range" value="<?php echo isset($_REQUEST['range']) ? esc_attr($_REQUEST['range']) : '7day' ?>" >
				<input type="hidden" name="tab" value="<?php echo isset($_REQUEST['tab']) ? esc_attr($_REQUEST['tab']) : 'price' ?>" >
				<?php if( isset($_REQUEST['report_in']) ): ?>
					<input type="hidden" name="report_in" value="<?php echo isset($_REQUEST['report_in']) ? esc_attr($_REQUEST['report_in']) : '' ?>" >
				<?php endif; ?>
				<?php if( isset($_REQUEST['report_out']) ): ?>
					<input type="hidden" name="report_out" value="<?php echo isset($_REQUEST['report_out']) ? esc_attr($_REQUEST['report_out']) : '' ?>" >
				<?php endif; ?>
				<?php wp_nonce_field( 'tp-hotel-booking-report-export', 'tp-hotel-booking-report-export' ) ?>
				<button type="submit"><?php _e( 'Export', 'tp-hotel-booking' ) ?></button>
			</form>
			<ul>
				<?php foreach( $date as $key => $d ): ?>
					<li <?php echo $d['id'] === $currentRang ? 'class="active"' : '' ?>>
						<a href="<?php echo admin_url( 'admin.php?page=tp_hotel_booking_report&tab='.$currenttab.'&range='.$d['id'] ) ?>">
							<?php printf( '%s', $d['title'] ) ?>
						</a>
					</li>
				<?php endforeach; ?>
				<li>
					<form method="GET">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
						<input type="hidden" name="tab" value="<?php echo isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'price' ?>" />
						<input type="hidden" name="range" value="custom" />
						<input type="text" id="tp-hotel-report-checkin" name="report_in" value="<?php echo isset( $_REQUEST['report_in'] ) ? esc_attr($_REQUEST['report_in'] ) : ''; ?>" />
						<input type="text" id="tp-hotel-report-checkout" name="report_out" value="<?php echo isset( $_REQUEST['report_out'] ) ? esc_attr($_REQUEST['report_out'] ) : ''; ?>" />
						<?php if( isset($_GET['room_id']) && $_GET['room_id'] ): ?>
							<?php foreach( $_GET['room_id'] as $key => $room ): ?>
								<input type="hidden" name="room_id[]" value="<?php echo esc_attr( $room ) ?>">
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_nonce_field( 'tp-hotel-booking-report', 'tp-hotel-booking-report' ); ?>
						<button type="submit"><?php _e( 'Go', 'tp-hotel-booking' ) ?></button>
					</form>
				</li>
			</ul>
		</h3>

	</div>

	<!-- booking_page_tp_hotel_booking_report -->
	<div id="tp-hotel-booking-report-main">
		<div id="chart-sidebar">
			<?php do_action( 'tp_hotel_booking_chart_sidebar', $currenttab, $currentRang ) ?>
		</div>

		<div id="tp-hotel-booking-chart-content">
			<?php do_action( 'tp_hotel_booking_chart_canvas', $currenttab, $currentRang ) ?>
		</div>
	</div>

</div>