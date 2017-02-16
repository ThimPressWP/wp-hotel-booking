<?php

class HB_WC_Booking {

    function __construct() {
        /**
         * booking change status
         */
        add_action( 'woocommerce_order_status_changed', array( $this, 'woo_change_oder_status' ), 10, 3 );

        /**
         * booking status filter
         */
        add_filter( 'hotel_booking_booking_total', array( $this, 'booking_status' ), 10, 3 );
    }

    /**
     * woo_change_oder_status change order status, trigger change booking status
     * @return null
     */
    public function woo_change_oder_status( $id, $old_status, $new_status ) {
        if ( !$booking_id = get_post_meta( $id, 'hb_wc_booking_id', true ) )
            return;

        $book = HB_Booking::instance( $booking_id );

        switch ( $new_status ) {
            case 'processing':
                # code...
                $status = 'processing';
                break;
            case 'pending':
                # code...
                $status = 'pending';
                break;
            case 'completed':
                # code...
                $status = 'completed';
                break;
            default:
                # code...
                $status = 'pending';
                break;
        }
        $book->update_status( $status );
    }

    /**
     * booking_status
     * @param  html $html
     * @param  string $column_name
     * @param  int $post_id
     * @return html
     */
    public function booking_status( $html, $column_name, $post_id ) {
        if ( !$order_id = get_post_meta( $post_id, '_hb_woo_order_id', true ) )
            return $html;

        $status = get_post_status( $post_id );

        if ( $column_name === 'total' ) {
            // display paid
            if ( $status === 'hb-processing' ) {
                $total = get_post_meta( $post_id, '_hb_total', true );
                $currency = get_post_meta( $post_id, '_hb_currency', true );
                $html = wc_price( $total, array( 'currency' => $currency ) );
            }
            $html .= '<br /><small><a href="' . esc_attr( get_edit_post_link( $order_id ) ) . '">(' . __( 'Via WooCommerce', 'tp-hotel-booking-woocommerce' ) . ')</a></small>';
        }
        return $html;
    }

}

new HB_WC_Booking();
