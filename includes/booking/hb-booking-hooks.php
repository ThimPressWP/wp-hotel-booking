<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 15:40:31
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-14 09:20:10
 */

/**
 * Hook
 */
add_action( 'hotel_booking_create_booking', 'hotel_booking_create_booking', 10, 1 );
add_action( 'hb_booking_status_changed', 'hotel_booking_create_booking', 10, 1 );
if ( ! function_exists( 'hotel_booking_create_booking' ) ) {
    function hotel_booking_create_booking( $booking_id ) {
        $booking_status = get_post_status( $booking_id );
        if ( $booking_status === 'hb-pending' ) {
            wp_clear_scheduled_hook( 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
            $time = hb_settings()->get( 'cancel_payment', 12 ) * HOUR_IN_SECONDS;
            wp_schedule_single_event( time() + $time, 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
        }
    }
}

// change booking status pending => status
add_action( 'hotel_booking_change_cancel_booking_status', 'hotel_booking_change_cancel_booking_status', 10, 1 );
if ( ! function_exists( 'hotel_booking_change_cancel_booking_status' ) ) {
    function hotel_booking_change_cancel_booking_status( $booking_id ) {
        global $wpdb;

        $booking_status = get_post_status( $booking_id );
        if ( $booking_status === 'hb-pending' ) {
            wp_update_post( array(
                    'ID'                => $booking_id,
                    'post_status'       => 'hb-cancelled'
                ) );
        }
    }
}

/**
 * filter email from
 */
function hb_wp_mail_from( $email ) {
    global $hb_settings;
    if ( $email = $hb_settings->get( 'email_general_from_email', get_option( 'admin_email' ) ) ) {
        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            return $email;
        }
    }
    return $email;
}
function hb_wp_mail_from_name( $name ) {
    global $hb_settings;
    if ( $name = $hb_settings->get( 'email_general_from_name' ) ) {
        return $name;
    }
    return $name;
}

/**
 * Send email to user after they booked room
 *
 * @param int $booking_id
 */
function hb_new_booking_email( $booking_id ) {
    $settings = HB_Settings::instance();
    $booking  = HB_Booking::instance( $booking_id );

    $to            = $settings->get( 'email_new_booking_recipients' );
    $subject       = $settings->get( 'email_new_booking_subject' );
    $email_heading = $settings->get( 'email_new_booking_heading' );
    $format        = $settings->get( 'email_new_booking_format' );
    if ( ! $subject ) {
        $subject = '[{site_title}] New customer booking ({order_number}) - {order_date}';
    }

    $find = array(
        'order-date'   => '{order_date}',
        'order-number' => '{order_number}',
        'site-title'   => '{site_title}'
    );

    $replace = array(
        'order-date'   => date_i18n( 'd.m.Y', strtotime( date( 'd.m.Y' ) ) ),
        'order-number' => $booking->get_booking_number(),
        'site-title'   => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
    );

    $subject = str_replace( $find, $replace, $subject );

    if ( ! $email_heading ) {
        $email_heading = __( 'New customer booking', 'tp-hotel-booking' );
    }

    $body = null;
    // new version 1.1
    if( get_post_meta( $booking_id, '_hb_booking_cart_params' , true ) ) {
        $body = hb_get_template_content( 'emails/email-booking.php', array(
            'email_heading' => $email_heading,
            'booking'       => HB_Booking::instance( $booking_id )
        ) );
    } else if ( get_post_meta( $booking_id, '_hb_booking_params' , true ) ) {
        $body = hb_get_template_content( 'emails/admin-new-booking.php', array(
            'email_heading' => $email_heading,
            'booking'       => HB_Booking::instance( $booking_id )
        ) );
    }

    if ( ! $body ) {
        return;
    }
    // get CSS styles
    ob_start();
    hb_get_template( 'emails/email-styles.php' );
    $css = apply_filters( 'hb_email_styles', ob_get_clean() );
    $css = preg_replace( '!</?style>!', '', $css );
    print_r( $css );
    try {
        if ( ! class_exists( 'Emogrifier') ) {
            TP_Hotel_Booking::instance()->_include( 'includes/libraries/class-emogrifier.php' );
        }
        // apply CSS styles inline for picky email clients
        $emogrifier = new Emogrifier( $body, $css );
        $body       = $emogrifier->emogrify();

    } catch ( Exception $e ) {

    }

    $headers = "Content-Type: " . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
    $send    = wp_mail( $to, $subject, $body, $headers );
    return $send;
}

// add_action( 'hb_booking_status_pending_to_processing', 'hb_new_booking_email' );
add_action( 'hb_booking_status_pending_to_completed', 'hb_new_booking_email' );

/**
 * Filter content type to text/html for email
 *
 * @return string
 */
function hb_set_html_content_type(){
    return 'text/html';
}

/**
 * Booking details for email content
 *
 * @param $booking_id
 * @return string
 */
function hb_new_customer_booking_details( $booking_id ) {
    $booking = HB_Booking::instance( $booking_id );
    // $customer = HB_Customer::instance( $booking->customer_id );
    // cart params
    $cart_params = apply_filters( 'hotel_booking_admin_cart_params', $booking->get_cart_params() );

    $customer_name = hb_get_customer_fullname( $booking_id, true );

    $currency = hb_get_currency_symbol( $booking->currency );

    $rooms = array();
    $child = array();
    foreach ( $cart_params as $key => $cart_item ) {
        if ( $cart_item->product_data->post && $cart_item->product_data->post->post_type === 'hb_room' ) {
            $rooms[ $key ] = $cart_item->product_data;
        }

        if ( isset( $cart_item->parent_id ) ) {
            if ( ! array_key_exists( $cart_item->parent_id, $child ) ) {
                $child[ $cart_item->parent_id ] = array();
            }
            $child[ $cart_item->parent_id ][] = $key;
        }
    }

    ob_start();
?>
    <table style="color: #444444;background-color: #DDD;font-family: verdana, arial, sans-serif; font-size: 14px; min-width: 800px;" cellpadding="5" cellspacing="1">
        <tbody>
            <tr style="background-color: #F5F5F5;">
                <td colspan="7">
                    <h3 style="margin: 5px 0;"><?php printf( __( 'Booking Details %s(%s)', 'tp-hotel-booking' ), hb_format_order_number( $booking_id ), hb_get_booking_status_label( $booking_id ) ); ?></h3>
                </td>
            </tr>
            <tr style="background-color: #FFFFFF;">
                <td style="font-weight: bold;">
                    <?php _e( 'Customer Name', 'tp-hotel-booking' ); ?>
                </td>
                <td colspan="6" ><?php echo esc_html( $customer_name ); ?></td>
            </tr>
            <tr style="background-color: #F5F5F5;">
                <td colspan="7">
                    <h3 style="margin: 5px 0;"><?php _e( 'Booking Rooms', 'tp-hotel-booking' ) ; ?></h3>
                </td>
            </tr>
            <tr style="background-color: #FFFFFF;">
                <td style="font-weight: bold;"><?php _e( 'Room', 'tp-hotel-booking' ); ?></td>
                <td style="font-weight: bold; text-align: right;"><?php _e( 'Capacity', 'tp-hotel-booking' ); ?></td>
                <td style="font-weight: bold; text-align: right;"><?php _e( 'Quantity', 'tp-hotel-booking' ); ?></td>
                <td style="font-weight: bold; text-align: right;"><?php _e( 'Check in', 'tp-hotel-booking' ); ?></td>
                <td style="font-weight: bold; text-align: right;"><?php _e( 'Check out', 'tp-hotel-booking' ); ?></td>
                <td style="font-weight: bold; text-align: right;"><?php _e( 'Night', 'tp-hotel-booking' ); ?></td>
                <td style="font-weight: bold; text-align: right;"><?php _e( 'Total', 'tp-hotel-booking' ); ?></td>
            </tr>
            <?php if( $cart_params ): ?>
                <?php foreach ( $rooms as $cart_id => $room ): ?>

                        <tr style="background-color: #FFFFFF;">
                            <td style="text-align: center;" rowspan="<?php echo array_key_exists( $cart_id, $child ) ? count( $child[ $cart_id ] ) + 2 : 1 ?>">
                                <a href="<?php echo esc_attr( get_the_permalink( $room->ID ) ); ?>"><?php echo esc_html( $room->name ); ?><?php printf( '%s', $room->capacity_title ? ' ('.$room->capacity_title.')' : '' ); ?></a>
                            </td>
                            <td style="text-align: right;"><?php echo sprintf( _n( '%d adult', '%d adults', $room->capacity, 'tp-hotel-booking' ), $room->capacity ); ?> </td>
                            <td style="text-align: right;"><?php echo esc_html( $room->quantity ); ?></td>
                            <td style="text-align: right;"><?php echo esc_html( date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_in_date' ) ) ) ); ?></td>
                            <td style="text-align: right;"><?php echo esc_html( date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_out_date' ) ) ) ); ?></td>
                            <td style="text-align: right;"><?php echo hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ) ?></td>
                            <td style="text-align: right;">
                                <?php echo sprintf( '%s', hb_format_price( $rooms[ $cart_id ]->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ) ); ?>
                            </td>
                        </tr>

                        <?php do_action( 'hotel_booking_email_new_booking', $cart_params, $cart_id, $booking ); ?>

                <?php endforeach; ?>
            <?php endif; ?>
            <tr style="background-color: #FFFFFF;">
                <td colspan="6" style="font-weight: bold;"><?php _e( 'Sub Total', 'tp-hotel-booking' ); ?></td>
                <td style=" text-align: right;"><?php echo hb_format_price( $booking->sub_total, $currency ); ?></td>
            </tr>
            <?php if ( $booking->tax ) : ?>
                <tr style="background-color: #FFFFFF;">
                    <td colspan="6" style="font-weight: bold;"><?php _e( 'Tax', 'tp-hotel-booking' ); ?></td>
                    <td style="text-align: right;"><?php echo abs( $booking->tax * 100 ) . '%' ?></td>
                </tr>
            <?php endif; ?>
            <tr style="background-color: #FFFFFF;">
                <td colspan="6" style="font-weight: bold;"><?php _e( 'Grand Total', 'tp-hotel-booking' ); ?></td>
                <td style="text-align: right;"><?php echo sprintf( '%s', hb_format_price( $booking->total, $currency ) ); ?></td>
            </tr>
        </tbody>
    </table>
<?php
    return ob_get_clean();
}

add_action( 'hb_new_booking', 'hb_new_customer_booking_email' );
add_action( 'hb_booking_status_changed', 'hb_new_customer_booking_email', 10, 3 );
// send mail to customer
function hb_new_customer_booking_email( $booking_id = null, $old_status = null, $new_status = null ) {
    if ( ! $booking_id ) {
        return;
    }

    if ( $new_status && $new_status !== 'completed' ) {
        return;
    }
    $booking = HB_Booking::instance( $booking_id );
    $settings = HB_Settings::instance()->get('offline-payment');
    $email_subject = ! empty( $settings['email_subject'] ) ? $settings['email_subject'] : false;
    $email_content = ! empty( $settings['email_content'] ) ? $settings['email_content'] : false;

    if( function_exists( 'wpautop' ) ) {
        $email_content = wpautop( $email_content );
    }
    if ( preg_match( '!{{customer_name}}!', $email_content ) ) {
        $email_content = preg_replace( '!\{\{customer_name\}\}!', hb_get_customer_fullname( $booking_id, true ), $email_content );
    }
    if ( preg_match( '!{{site_name}}!', $email_content ) ) {
        $email_content = preg_replace( '!\{\{site_name\}\}!', get_bloginfo( 'name' ), $email_content );
    }
    if ( preg_match( '!{{booking_details}}!', $email_content ) ) {
        // email template
        $booking_details = hb_new_customer_booking_details( $booking_id );
        $email_content = preg_replace( '!\{\{booking_details\}\}!', $booking_details, $email_content );
    }

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    // set mail from email
    add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
    // set mail from name
    add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
    add_filter('wp_mail_content_type', 'hb_set_html_content_type' );
    $to = $booking->customer_email;
    $return = wp_mail( $to, $email_subject, stripslashes( $email_content ), $headers );

    remove_filter('wp_mail_content_type', 'hb_set_html_content_type');
}
