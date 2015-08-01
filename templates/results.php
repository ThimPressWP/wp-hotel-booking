<div id="hotel-booking-results">
    <form name="hb-search-form">
        <h3><?php _e( 'Search results', 'tp-hotel-booking' );?></h3>
        <?php if( $results ):?>
            <?php hb_get_template( 'results/list.php', array( 'results' => $results ) );?>
            <input type="hidden" name="hotel-booking" value="payment">
            <p>
                <button type="submit"><?php _e( 'Continue', 'tp-hotel-booking' );?></button>
            </p>
        <?php else:?>
            <p><?php _e( 'No room found', 'tp-hotel-booking' );?></p>
            <p>
                <a href="<?php echo get_the_permalink(33);?>"><?php _e( 'Search again!', 'tp-hotel-booking' );?></a>
            </p>
        <?php endif;?>
        <input type="hidden" name="check_in_date" value="<?php echo hb_get_request( 'check_in_date' );?>" />
        <input type="hidden" name="check_out_date" value="<?php echo hb_get_request( 'check_out_date' );?>">
    </form>
</div>