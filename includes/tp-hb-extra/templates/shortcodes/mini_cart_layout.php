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

            {{ console.debug(data.extra_packages) }}
        <# if ( typeof data.extra_packages !== 'undefined' && data.extra_packages.length > 0 ) { #>
            <div class="hb_mini_cart_price_packages">
                <ul>
                    <#  for ( var i = 0; i < data.extra_packages.length; i++ ) { #>
                            <# var pack = data.extra_packages[i] #>
                            <li>
                                <h5 class="hb_package_title">
                                    <a href="#">{{{ pack.package_title }}}</a>
                                    <span>
                                        ( {{{ pack.package_quantity }}} )
                                        <a href="#" class="hb_package_remove" data-package="{{ pack.package_id }}"><i class="fa fa-times"></i></a>
                                    </span>
                                </h5>
                            </li>
                     <# } #>
                </ul>
            </div>
        <# } #>

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

<script type="text/javascript">
    // if( typeof data.extra_packages != 'undefined' && data.extra_packages )
    // {
    //     <div class="hb_mini_cart_price_packages">
    //         <ul>
    //             for ( var i = 0; i < data.extra_packages.length; i++ )
    //             {
    //                 <li>
    //                     <h5 class="hb_package_title">
    //                         <a href="#">{{{ data.package_title }}}</a>
    //                         <span>
    //                             ( {{ data.package_quantity }} ?>)
    //                             <a href="#" class="hb_package_remove" data-package="{{ data.package_id }}"><i class="fa fa-times"></i></a>
    //                         </span>
    //                     </h5>
    //                 </li>
    //             }
    //         </ul>
    //     </div>
    // }
</script>