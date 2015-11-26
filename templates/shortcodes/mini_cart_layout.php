<script type="text/html" id="tmpl-hb-minicart-item">
    <div class="hb_mini_cart_item active" data-search-key="{{ data.search_key }}" data-id="{{ data.id }}">

        <div class="hb_mini_cart_top">

        <h4 class="hb_title"><a href="{{{ data.permalink }}}">{{ data.name }}</a></h4>
            <span class="hb_mini_cart_remove"><i class="fa fa-times"></i></span>

        </div>

        <div class="hb_mini_cart_number">

            <label><?php _e( 'Quantity: ', 'tp-hotel-booking' ); ?></label>
            <span>{{ data.quantity }}</span>

        </div>

        <div class="hb_mini_cart_price">

            <label><?php _e( 'Price: ', 'tp-hotel-booking' ); ?></label>
            <span>{{{ data.total }}}</span>

        </div>
    </div>
</script>
<script type="text/html" id="tmpl-hb-minicart-footer">
    <div class="hb_mini_cart_footer">

        <a href="<?php echo hb_get_url(array( 'hotel-booking' => 'checkout')) ?>" class="hb_button hb_checkout"><?php _e( 'Check Out', 'tp-hotel-booking' );?></a>
        <a href="<?php echo hb_get_url( array('hotel-booking' => 'cart') ); ?>" class="hb_button hb_view_cart"><?php _e( 'View Cart', 'tp-hotel-booking' );?></a>

    </div>
</script>
<script type="text/html" id="tmpl-hb-minicart-empty">
    <p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty!', 'tp-hotel-booking' ); ?></p>
</script>