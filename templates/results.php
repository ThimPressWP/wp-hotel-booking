<?php do_action( 'hb_before_search_result' );?>
<?php
    global $hb_search_rooms;
?>
<div id="hotel-booking-results">
    <h3><?php _e( 'Search results', 'tp-hotel-booking' );?></h3>
    <?php if( $results ):?>
        <?php hb_get_template( 'results/list.php', array( 'results' => $hb_search_rooms['data'], 'atts' => $atts ) );?>
    <?php else: ?>
        <p><?php _e( 'No room found', 'tp-hotel-booking' );?></p>
        <p>
            <a href="<?php echo get_cart_url( array('hotel-booking' => 'cart') );?>"><?php _e( 'Search again!', 'tp-hotel-booking' );?></a>
        </p>
    <?php endif;?>
    <nav class="rooms-pagination">
        <?php
            echo paginate_links( apply_filters( 'hb_pagination_args', array(
                'base' => add_query_arg( 'hb_page', '%#%' ),
                'format' => '',
                'prev_text' => __('Previous', 'Tp-hotel-booking'),
                'next_text' => __('Next', 'Tp-hotel-booking'),
                'total' => $hb_search_rooms['max_num_pages'],
                'current' => $hb_search_rooms['page'],
                'type'         => 'list',
                'end_size'     => 3,
                'mid_size'     => 3
            ) ) );
        ?>
    </nav>
</div>