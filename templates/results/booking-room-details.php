<div class="hb-booking-room-details">
    <span class="hb_search_room_item_detail_price_close">
        <i class="fa fa-times"></i>
    </span>
    <?php $details = $room->get_booking_room_details(); ?>
    <table class="hb_search_room_pricing_price">
        <tbody>
            <?php foreach ($details as $day => $info):?>
                <tr>
                    <th><?php printf( '%s', hb_date_to_name( $day ) ) ?></th>
                    <td class="hb_search_item_total_description">
                        <span>
                            <?php printf( 'x%d %s', $info['count'], __('Night', 'tp-hotel-booking') ) ?>
                        </span>
                    </td>
                    <td class="hb_search_item_price">
                        <span><?php echo hb_format_price(round( $info['price'], 2 )); ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="hb_search_item_total_bold">
                    <span><?php _e( 'Total', 'tp-hotel-booking' ) ?></span>
                </td>
                <td class="hb_search_item_total_description">
                    <span><?php _e( '* vat is not included yet', 'tp-hotel-booking' ); ?></span>
                </td>
                <td class="hb_search_item_price">
                    <span><?php echo hb_format_price( $room->room_details_total );?></span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>