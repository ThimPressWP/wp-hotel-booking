<?php

/**
 * Class HB_Payment_Gateway_Stripe
 */
class HB_Payment_Gateway_Offline_Payment extends HB_Payment_Gateway_Base{
    /**
     * @var array
     */
    protected $_settings = array();

    function __construct(){
        parent::__construct();
        $this->_slug = 'offline-payment';
        $this->_title = __( 'Offline Payment', 'tp-hotel-booking' );
        $this->_description = __( 'Pay on arrival', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('offline-payment');
        $this->init();
    }

    /**
     * Init hooks
     */
    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
        add_filter( 'hb_payment_method_title_offline-payment', array( $this, 'payment_method_title' ) );
    }

    /**
     * Payment method title
     *
     * @return mixed
     */
    function payment_method_title(){
        return $this->_description;
    }

    /**
     * Print the text in total column
     *
     * @param $booking_id
     * @param $total
     * @param $total_with_currency
     */
    function column_total_content( $booking_id, $total, $total_with_currency ){
        if( get_post_meta( $booking_id, '_hb_method', true ) == 'offline-payment' ) {
            _e( '<br />(<small>Pay on arrival</small>)', 'tp-hotel-booking' );
        }
    }

    /**
     * Print admin settings
     *
     * @param $gateway
     */
    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/offline-payment.php' );
        include_once $template;
    }

    /**
     * Check to see if this payment is enable
     *
     * @return bool
     */
    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }

    /**
     * Booking details for email content
     *
     * @param $booking_id
     * @return string
     */
    function booking_details( $booking_id ) {
        $booking = HB_Booking::instance( $booking_id );
        $customer = HB_Customer::instance( $booking->customer_id );
        // cart params
        $cart_params = apply_filters( 'hotel_booking_admin_cart_params', $booking->get_cart_params() );

        $title = hb_get_title_by_slug( $customer->get( '_hb_title' ) );
        $first_name = $customer->get( '_hb_first_name' );
        $last_name = $customer->get( '_hb_last_name' );
        $customer_name = sprintf( '%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name );

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
                        <h3 style="margin: 5px 0;"><?php printf( __( 'Booking Details %s', 'tp-hotel-booking' ), hb_format_order_number( $booking_id ) ); ?></h3>
                    </td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td style="font-weight: bold;">
                        <?php _e( 'Customer Name', 'tp-hotel-booking' ); ?>
                    </td>
                    <td colspan="6" ><?php echo $customer_name; ?></td>
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
                                    <a href="<?php echo get_edit_post_link( $room->ID ); ?>"><?php echo $room->name; ?><?php printf( '%s', $room->capacity_title ? ' ('.$room->capacity_title.')' : '' ); ?></a>
                                </td>
                                <td style="text-align: right;"><?php echo sprintf( _n( '%d adult', '%d adults', $room->capacity, 'tp-hotel-booking' ), $room->capacity ); ?> </td>
                                <td style="text-align: right;"><?php echo $room->quantity; ?></td>
                                <td style="text-align: right;"><?php echo $room->get_data( 'check_in_date' ) ?></td>
                                <td style="text-align: right;"><?php echo $room->get_data( 'check_out_date' ) ?></td>
                                <td style="text-align: right;"><?php echo hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ) ?></td>
                                <td style="text-align: right;">
                                    <?php echo hb_format_price( $rooms[ $cart_id ]->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ); ?>
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
                    <td style="text-align: right;"><?php echo hb_format_price( $booking->total, $currency ); ?></td>
                </tr>
            </tbody>
        </table>
    <?php
        return ob_get_clean();
    }

    /**
     * Filter content type to text/html for email
     *
     * @return string
     */
    function set_html_content_type(){
        return 'text/html';
    }

    /**
     * Process checkout booking
     *
     * @param null $booking_id
     * @return array
     */
    function process_checkout( $booking_id = null, $customer_id = null ){
        $booking = HB_Booking::instance( $booking_id );
        if( $booking ){
            $booking->update_status( 'processing' );
        }

        $settings = HB_Settings::instance()->get('offline-payment');
        $email_subject = ! empty( $settings['email_subject'] ) ? $settings['email_subject'] : false;
        $email_content = ! empty( $settings['email_content'] ) ? $settings['email_content'] : false;

        if( ! $email_subject || ! $email_content ) {
            return array(
                'result'    => 'fail'
            );
        } else {
            if( function_exists( 'wpautop' ) ) {
                $email_content = wpautop( $email_content );
            }
            if ( preg_match( '!{{customer_name}}!', $email_content ) ) {
                $email_content = preg_replace( '!\{\{customer_name\}\}!', hb_get_customer_fullname( $customer_id, true ), $email_content );
            }
            if ( preg_match( '!{{site_name}}!', $email_content ) ) {
                $email_content = preg_replace( '!\{\{site_name\}\}!', get_bloginfo( 'name' ), $email_content );
            }
            if ( preg_match( '!{{booking_details}}!', $email_content ) ) {
                // email template
                $booking_details = $this->booking_details( $booking_id );
                $email_content = preg_replace( '!\{\{booking_details\}\}!', $booking_details, $email_content );
            }

            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            // set mail from email
            add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
            // set mail from name
            add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
            add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
            $to = get_post_meta($customer_id, '_hb_email', true);
            $return = wp_mail($to, $email_subject, stripslashes( $email_content ), $headers );

            remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

            hb_add_message( sprintf( __( 'Thank you! Your booking has been placed. Please check your email %s to view booking details', 'tp-hotel-booking' ), $to ) );
            // empty cart
            TP_Hotel_Booking::instance()->cart->empty_cart();
            return array(
                'result'    => 'success',
                'r'         => $return,
                'redirect'  => '?hotel-booking-offline-payment=1'
            );
        }

    }

    function form(){
        echo _e( ' Pay on Arrival', 'tp-hotel-booking' );
    }
}
