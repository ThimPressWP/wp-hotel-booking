<?php
/**
 * template extra admin cart
 * @since  1.1
 */
?>
<table class="hb-booking-table hb-table-width30">
    <thead>
        <tr>
            <th colspan="2">
                <h3><?php _e( 'Payment Details', 'tp-hotel-booking') ?></h3>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>
                <?php _e( 'Payment Gateway', 'tp-hotel-booking' ); ?>
            </th>
            <td>
                <?php echo $booking->method_title; ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php _e( 'Booking status', 'tp-hotel-booking' ); ?>
            </th>
            <td>
                <span class="hb-booking-status <?php echo get_post_status( $booking->id ); ?>"><?php echo hb_get_booking_status_label( $booking->id ); ?></span>
            </td>
        </tr>
    </tbody>
</table>
