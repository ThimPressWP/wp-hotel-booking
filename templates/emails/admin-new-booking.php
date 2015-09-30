<?php
/**
 * Admin new order email
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$booking_id = $booking->id;
$customer_id = get_post_meta( $booking_id, '_hb_customer_id', true );
$title = hb_get_title_by_slug(get_post_meta($customer_id, '_hb_title', true));
$first_name = get_post_meta($customer_id, '_hb_first_name', true);
$last_name = get_post_meta($customer_id, '_hb_last_name', true);
$customer_name = sprintf('%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name);

$check_in = intval( get_post_meta( $booking_id, '_hb_check_in_date', true ) );
$check_out = intval( get_post_meta( $booking_id, '_hb_check_out_date', true ) );
$currency = hb_get_currency_symbol( get_post_meta( $booking_id, '_hb_currency', true ) );
$_rooms = get_post_meta( $booking_id, '_hb_room_id' );
$rooms = array();
foreach( $_rooms as $id ){
    if( empty( $rooms[ $id ] ) ){
        $rooms[ $id ] = 0;
    }
    $rooms[ $id ] ++;
}

?>

<?php do_action( 'hb_email_header', $email_heading ); ?>

<?php do_action( 'hb_email_before_booking_table', $booking, true, false ); ?>

<h2>
    <a class="link" href="<?php echo admin_url( 'post.php?post=' . $booking->id . '&action=edit' ); ?>"><?php printf( __( 'Booking %s', 'tp-hotel-booking'), $booking->get_booking_number() ); ?></a>
    (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $booking->order_date ) ), date_i18n( hb_date_format(), strtotime( $booking->order_date ) ) ); ?>)
</h2>

<table class="booking-table" cellpadding="5" cellspacing="1">
    <tbody>
    <tr class="booking-table-head">
        <td colspan="4">
            <h3><?php printf( __( 'Booking Details %s', 'tp-hotel-booking' ), hb_format_order_number( $booking_id ) );?></h3>
        </td>
    </tr>
    <tr class="booking-table-row">
        <td class="bold-text">
            <?php _e( 'Customer Name', 'tp-hotel-booking' );?>
        </td>
        <td colspan="3" ><?php echo $customer_name;?></td>
    </tr>
    <tr class="booking-table-row">
        <td class="bold-text"><?php _e( 'Check In Date', 'tp-hotel-booking' );?></td>
        <td colspan="3"><?php echo date( 'l d M Y', $check_in );?></td>
    </tr>
    <tr class="booking-table-row">
        <td class="bold-text"><?php _e( 'Check Out Date', 'tp-hotel-booking' );?></td>
        <td colspan="3"><?php echo date( 'l d M Y', $check_out );?></td>
    </tr>
    <tr class="booking-table-row">
        <td class="bold-text"><?php _e( 'Total Nights', 'tp-hotel-booking' );?></td>
        <td colspan="3"><?php echo get_post_meta( $booking_id, '_hb_total_nights', true ) ;?></td>
    </tr>
    <tr class="booking-table-row">
        <td class="bold-text"><?php _e( 'Total Rooms', 'tp-hotel-booking' );?></td>
        <td colspan="3"><?php echo count($_rooms);?></td>
    </tr>
    <tr class="booking-table-head">
        <td colspan="4">
            <h3><?php _e( 'Booking Rooms', 'tp-hotel-booking' ) ;?></h3>
        </td>
    </tr>
    <tr class="booking-table-row">
        <td class="bold-text"><?php _e( 'Room type', 'tp-hotel-booking' );?></td>
        <td class="text-align-right bold-text"><?php _e( 'Number of rooms', 'tp-hotel-booking' );?></td>
        <td class="text-align-right bold-text"><?php _e( 'Capacity', 'tp-hotel-booking' );?></td>
        <td class="text-align-right bold-text"><?php _e( 'Total', 'tp-hotel-booking' );?></td>
    </tr>
    <?php foreach( $rooms as $id => $num_of_rooms ){?>
        <tr class="booking-table-row">
            <td>
                <?php
                echo get_the_title( $id );
                // $term = get_term( get_post_meta( $id, '_hb_room_type', true ), 'hb_room_type' );
                $terms = wp_get_post_terms( $id, 'hb_room_type' );
                $room_types = array();
                foreach ($terms as $key => $term) {
                    $room_types[] = $term->name;
                }
                // if( $term ) echo " (", $term->name, ")";
                if( $term ) echo " (", implode(', ', $room_types), ")";
                ?>
            </td>
            <td class="text-align-right"><?php echo $num_of_rooms;?></td>
            <td class="text-align-right">
                <?php
                $cap_id = get_post_meta( $id, '_hb_room_capacity', true );
                $term = get_term( $cap_id, 'hb_room_capacity' );
                if( $term ){
                    printf( '%s (%d)', $term->name, get_option( 'hb_taxonomy_capacity_' . $cap_id ) );
                }
                ?>
            </td>
            <td class="text-align-right">
                <?php
                $room = HB_Room::instance( $id );
                echo hb_format_price( $room->get_total( $check_in, $check_out, $num_of_rooms, false ), $currency );
                ?>
            </td>
        </tr>
    <?php }?>
    <tr class="booking-table-row">
        <td colspan="3" class="bold-text"><?php _e( 'Sub Total', 'tp-hotel-booking' );?></td>
        <td class="text-align-right"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_sub_total', true ), $currency );?></td>
    </tr>
    <tr class="booking-table-row">
        <td colspan="3" class="bold-text"><?php _e( 'Tax', 'tp-hotel-booking' );?></td>
        <td class="text-align-right"><?php echo get_post_meta( $booking_id, '_hb_tax', true ) * 100;?>%</td>
    </tr>
    <tr class="booking-table-row">
        <td colspan="3" class="bold-text"><?php _e( 'Grand Total', 'tp-hotel-booking' );?></td>
        <td class="text-align-right"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_total', true ), $currency );?></td>
    </tr>
    </tbody>
</table>
