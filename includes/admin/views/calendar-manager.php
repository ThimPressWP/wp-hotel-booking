<?php
/**
 * Admin View: Calendar Manager
 *
 * @version     2.3.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$statuses = hb_get_booking_statuses();
?>

<div class="wrap" id="tp_hotel_booking_calendar_manager">
	<div class="wphb-cm-header">
		<div class="wphb-cm-header__title">
			<h1><?php esc_html_e( 'Calendar Manager', 'wp-hotel-booking' ); ?></h1>
			<p class="wphb-cm-header__desc"><?php esc_html_e( 'Overview of all room bookings. Click on a booking to see its details.', 'wp-hotel-booking' ); ?></p>
		</div>
		<ul class="wphb-cm-legend">
			<?php foreach ( $statuses as $status => $label ) : ?>
				<li class="wphb-cm-status--<?php echo esc_attr( $status ); ?>">
					<span class="wphb-cm-legend__dot"></span><?php echo esc_html( $label ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<hr class="wp-header-end">

	<div class="wphb-cm-card">
		<div id="manager_booking"></div>
	</div>
</div>
