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
        $this->_title = __( 'Offline Payment', 'tp-hotel-booking' );
        $this->_description = __( '', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('stripe');
        $this->init();
    }

    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
    }

    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/offline-payment.php' );
        include_once $template;
    }

    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }


    function booking_details( $booking_id ){
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
        ob_start();
    ?>
        <table style="border: 1px solid #DDD;font-family: verdana, arial, sans-serif; font-size: 14px;" cellpadding="5">
            <tbody>
                <tr>
                    <td colspan="4">
                        <h3><?php printf( __( 'Booking Details %s', 'tp-hotel-booking' ), hb_format_order_number( $booking_id ) );?></h3>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;">
                        <?php _e( 'Customer Name', 'tp-hotel-booking' );?>
                    </td>
                    <td><?php echo $customer_name;?></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Check In Date', 'tp-hotel-booking' );?></td>
                    <td><?php echo date( 'l d M Y', $check_in );?></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Check Out Date', 'tp-hotel-booking' );?></td>
                    <td><?php echo date( 'l d M Y', $check_out );?></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Total Nights', 'tp-hotel-booking' );?></td>
                    <td><?php echo get_post_meta( $booking_id, '_hb_total_nights', true ) ;?></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Total Rooms', 'tp-hotel-booking' );?></td>
                    <td><?php echo count($_rooms);?></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <h3><?php _e( 'Booking Rooms', 'tp-hotel-booking' ) ;?></h3>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;"><?php _e( 'Number of rooms', 'tp-hotel-booking' );?></td>
                    <td style="font-weight: bold;"><?php _e( 'Room type', 'tp-hotel-booking' );?></td>
                    <td style="font-weight: bold;"><?php _e( 'Capacity', 'tp-hotel-booking' );?></td>
                    <td style="font-weight: bold;text-align: right;"><?php _e( 'Total', 'tp-hotel-booking' );?></td>
                </tr>
                <?php foreach( $rooms as $id => $num_of_rooms ){?>
                <tr>
                    <td><?php echo $num_of_rooms;?></td>
                    <td>
                        <?php
                        echo get_the_title( $id );
                        $term = get_term( get_post_meta( $id, '_hb_room_type', true ), 'hb_room_type' );
                        if( $term ) echo " (", $term->name, ")";
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo get_post_meta( $id, '_hb_room_capacity', true );?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        $room = HB_Room::instance( $id );
                        echo hb_format_price( $room->get_total( $check_in, $check_out, $num_of_rooms, false ), $currency );
                        ?>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Sub Total', 'tp-hotel-booking' );?></td>
                    <td><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_sub_total', true ), $currency );?></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Tax', 'tp-hotel-booking' );?></td>
                    <td><?php echo get_post_meta( $booking_id, '_hb_tax', true ) * 100;?>%</td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold;"><?php _e( 'Grand Total', 'tp-hotel-booking' );?></td>
                    <td><?php echo hb_format_price( get_post_meta( $booking_id, '_hb_sub_total', true ), $currency );?></td>
                </tr>
            </tbody>
        </table>
    <?php
        return ob_get_clean();
    }

    function set_html_content_type(){
        return 'text/html';
    }

    function process_checkout( $customer_id = null ){
        $booking    = hb_generate_transaction_object( $customer_id );
        $transaction = hb_add_transaction(
            array(
                'method'                => 'offline-payment',
                'method_id'             => 'N/A',
                'status'                => 'Pending',
                'customer_id'           => $customer_id,
                'transaction_object'    => $booking
            )
        );

        $settings = HB_Settings::instance()->get('offline-payment');
        $email_subject = ! empty( $settings['email_subject'] ) ? $settings['email_subject'] : false;
        $email_content = ! empty( $settings['email_content'] ) ? $settings['email_content'] : false;

        if( ! $email_subject || ! $email_content ) {
            return array(
                'result'    => 'fail',
                //'redirect'  => '?hotel-booking-offline-payment=1'
            );
        }else{
            if (preg_match('!{{booking_details}}!', $email_content)) {
                $booking_details = $this->booking_details($transaction);
                $email_content = preg_replace('!\{\{booking_details\}\}!', $booking_details, $email_content);
            }
            $headers[]       = 'Content-Type: text/html; charset=UTF-8';
            add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
            $to = get_post_meta($customer_id, '_hb_email', true);
            $return = wp_mail($to, $email_subject, stripslashes( $email_content ), $headers );
            echo "[$to], [$email_subject]";
            remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
            return array(
                'result'    => 'success',
                'r'         => $return
                //'redirect'  => '?hotel-booking-offline-payment=1'
            );
        }

    }

    function form(){
        echo _e( ' Pay on Arrival');
    }
}