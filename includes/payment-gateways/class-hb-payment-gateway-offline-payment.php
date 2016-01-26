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
    function booking_details( $booking_id ){
        $customer_id = get_post_meta( $booking_id, '_hb_customer_id', true );
        $title = hb_get_title_by_slug(get_post_meta($customer_id, '_hb_title', true));
        $first_name = get_post_meta($customer_id, '_hb_first_name', true);
        $last_name = get_post_meta($customer_id, '_hb_last_name', true);
        $customer_name = sprintf('%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name);

        $currency = hb_get_currency_symbol( get_post_meta( $booking_id, '_hb_currency', true ) );
        $_rooms = get_post_meta( $booking_id, '_hb_room_id' );
        $rooms = array();
        foreach( $_rooms as $id ){
            if( empty( $rooms[ $id ] ) ){
                $rooms[ $id ] = 0;
            }
            $rooms[ $id ] ++;
        }
        ob_start();
    ?>
        <table style="color: #444444;background-color: #DDD;font-family: verdana, arial, sans-serif; font-size: 14px; min-width: 800px;" cellpadding="5" cellspacing="1">
            <tbody>
                <tr style="background-color: #F5F5F5;">
                    <td colspan="4">
                        <h3 style="margin: 5px 0;"><?php printf( __( 'Booking Details %s', 'tp-hotel-booking' ), hb_format_order_number( $booking_id ) ); ?></h3>
                    </td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td style="font-weight: bold;">
                        <?php _e( 'Customer Name', 'tp-hotel-booking' ); ?>
                    </td>
                    <td colspan="3" ><?php echo $customer_name; ?></td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td style="font-weight: bold;"><?php _e( 'Total Nights', 'tp-hotel-booking' ); ?></td>
                    <td colspan="3"><?php echo get_post_meta( $booking_id, '_hb_total_nights', true ) ; ?></td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td style="font-weight: bold;"><?php _e( 'Total Rooms', 'tp-hotel-booking' ); ?></td>
                    <td colspan="3"><?php echo count($_rooms); ?></td>
                </tr>
                <tr style="background-color: #F5F5F5;">
                    <td colspan="4">
                        <h3 style="margin: 5px 0;"><?php _e( 'Booking Rooms', 'tp-hotel-booking' ) ; ?></h3>
                    </td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td style="font-weight: bold;"><?php _e( 'Room type', 'tp-hotel-booking' ); ?></td>
                    <td style="font-weight: bold; text-align: right;"><?php _e( 'Number of rooms', 'tp-hotel-booking' ); ?></td>
                    <td style="font-weight: bold; text-align: right;"><?php _e( 'Capacity', 'tp-hotel-booking' ); ?></td>
                    <td style="font-weight: bold;text-align: right;"><?php _e( 'Total', 'tp-hotel-booking' ); ?></td>
                </tr>
                <?php $booking_rooms_params = get_post_meta( $booking_id, '_hb_booking_params', true ); ?>
                <?php if( $booking_rooms_params ): ?>
                    <?php foreach ($booking_rooms_params as $search_key => $rooms): ?>

                            <?php foreach ($rooms as $id => $room_param) : ?>
                                <tr style="background-color: #FFFFFF;">
                                    <td>
                                        <?php
                                            $room = HB_Room::instance( $id, $room_param );
                                            echo get_the_title( $id );
                                            $terms = wp_get_post_terms( $id, 'hb_room_type' );
                                            $room_types = array();
                                            foreach ($terms as $key => $term) {
                                                $room_types[] = $term->name;
                                            }
                                            if( ! is_wp_error( $term ) && ! empty( $room_types ) ) echo " (", implode(', ', $room_types), ")";
                                        ?>
                                    </td>
                                    <td style="text-align: right;"><?php echo $room->quantity; ?></td>
                                    <td style="text-align: right;">
                                        <?php
                                            $cap_id = get_post_meta( $id, '_hb_room_capacity', true );
                                            $term = get_term( $cap_id, 'hb_room_capacity' );
                                            if( ! is_wp_error( $term ) ){
                                                printf( '%s (%d)', $term->name, get_option( 'hb_taxonomy_capacity_' . $cap_id ) );
                                            }
                                        ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php
                                            echo hb_format_price( $room->get_total( $room->check_in_date, $room->check_out_date, $room->quantity, false ), $currency );
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                    <?php endforeach; ?>
                <?php endif; ?>
                <tr style="background-color: #FFFFFF;">
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Sub Total', 'tp-hotel-booking' ); ?></td>
                    <td style=" text-align: right;"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_sub_total', true ), $currency ); ?></td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Tax', 'tp-hotel-booking' ); ?></td>
                    <td style="text-align: right;"><?php echo get_post_meta( $booking_id, '_hb_tax', true ) * 100; ?>%</td>
                </tr>
                <tr style="background-color: #FFFFFF;">
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Grand Total', 'tp-hotel-booking' ); ?></td>
                    <td style="text-align: right;"><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_total', true ), $currency ); ?></td>
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

        // HB_Cart::instance()->empty_cart();

        // return array(
        //     'result'    => 'success',
        //     'redirect'  => '?hotel-booking-offline-payment=1'
        // );

        $booking    = hb_generate_transaction_object( );

        $settings = HB_Settings::instance()->get('offline-payment');
        $email_subject = ! empty( $settings['email_subject'] ) ? $settings['email_subject'] : false;
        $email_content = ! empty( $settings['email_content'] ) ? $settings['email_content'] : false;

        if( ! $email_subject || ! $email_content ) {
            return array(
                'result'    => 'fail',
                //'redirect'  => '?hotel-booking-offline-payment=1'
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
                // $booking_details = $this->booking_details( $transaction );
                $booking_details = $this->booking_details( $booking_id );
                $email_content = preg_replace( '!\{\{booking_details\}\}!', $booking_details, $email_content );
            }

            $headers[]       = 'Content-Type: text/html; charset=UTF-8';
            add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
            $to = get_post_meta($customer_id, '_hb_email', true);
            $return = wp_mail($to, $email_subject, stripslashes( $email_content ), $headers );
            remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

            hb_add_message( sprintf( __( 'Thank you! Your booking has been placed. Please check your email %s to view booking details', 'tp-hotel-booking' ), $to ) );
            HB_Cart::instance()->empty_cart();
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