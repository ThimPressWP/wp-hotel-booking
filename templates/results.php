<?php do_action( 'hb_before_search_result' );?>
<div id="hotel-booking-results">
    <form name="hb-search-results" action="<?php echo $search_page;?>">
        <h3><?php _e( 'Search results', 'tp-hotel-booking' );?></h3>
        <?php if( $results ):?>
            <?php hb_get_template( 'results/list.php', array( 'results' => $results ) );?>
            <p>
                <button type="submit"><?php _e( 'Continue', 'tp-hotel-booking' );?></button>
            </p>
        <?php else:?>
            <p><?php _e( 'No room found', 'tp-hotel-booking' );?></p>
            <p>
                <a href="<?php echo hb_get_page_permalink( 'search' );?>"><?php _e( 'Search again!', 'tp-hotel-booking' );?></a>
            </p>
        <?php endif;?>
        <?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' );?>
        <input type="hidden" name="check_in_date" value="<?php echo hb_get_request( 'check_in_date' );?>" />
        <input type="hidden" name="check_out_date" value="<?php echo hb_get_request( 'check_out_date' );?>">
        <input type="hidden" name="hotel-booking" value="payment">
        <input type="hidden" name="action" value="hotel_booking_parse_booking_params" />
    </form>
</div>