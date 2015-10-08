<?php do_action( 'hb_before_search_result' );?>
<div id="hotel-booking-results">
    <h3><?php _e( 'Search results', 'tp-hotel-booking' );?></h3>
    <?php if( $results ):?>
        <?php hb_get_template( 'results/list.php', array( 'results' => $results ) );?>
        <p>
            <a href="<?php echo hb_get_url( array('hotel-booking' => 'cart') ); ?>" class="hb_button hb_view_cart"><?php _e( 'View Cart', 'tp-hotel-booking' );?></a>
        </p>
    <?php else: ?>
        <p><?php _e( 'No room found', 'tp-hotel-booking' );?></p>
        <p>
            <a href="<?php echo get_cart_url( array('hotel-booking' => 'cart') );?>"><?php _e( 'Search again!', 'tp-hotel-booking' );?></a>
        </p>
    <?php endif;?>
</div>