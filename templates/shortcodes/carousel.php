<?php
$currentcy = hb_get_currency_symbol();
$sliderId = 'hotel_booking_slider_'.uniqid();
$upload_dir = wp_upload_dir();
$upload_base_dir = $upload_dir['basedir'];
$upload_base_url = $upload_dir['baseurl'];
$items = isset($atts['number']) ? (int)$atts['number'] : 4;
?>
<div id="<?php echo $sliderId ?>" class="hb_room_carousel_container tp-hotel-booking">
    <?php if( isset($atts['title']) && $atts['title'] ): ?>
        <h3><?php echo $atts['title'] ?></h3>
    <?php endif; ?>
    <!--navigation-->
    <?php if( !isset($atts['navigation']) || $atts['navigation'] ): ?>
        <div class="navigation">
            <div class="prev"><span class="pe-7s-angle-left"></span></div>
            <div class="next"><span class="pe-7s-angle-right"></span></div>
        </div>
    <?php endif; ?>
    <!--pagination-->
    <?php if( !isset($atts['pagination']) || $atts['pagination'] ): ?>
        <div class="pagination"></div>
    <?php endif; ?>
    <!--text_link-->
    <?php if( isset($atts['text_link']) && $atts['text_link'] !== '' ): ?>
        <div class="text_link"><a href="<?php echo get_post_type_archive_link('hb_room'); ?>"><?php echo $atts['text_link']; ?></a></div>
    <?php endif; ?>
    <div class="hb_room_carousel">
        <?php hotel_booking_room_loop_start(); ?>

        <?php while ( $query->have_posts() ) : $query->the_post(); ?>

            <?php hb_get_template_part( 'content', 'room' ); ?>

        <?php endwhile; // end of the loop. ?>

    <?php hotel_booking_room_loop_end(); ?>
        <?php wp_reset_postdata(); ?>
    </div>
</div>
<script type="text/javascript">
    (function($){
        "use strict";
        $(document).ready(function(){
            $('#<?php echo $sliderId ?> .hb_room_carousel').carouFredSel({
                responsive: true,
                items: {
                    height: 'auto',
                    visible: {
                        min: <?php echo $items ?>,
                        max: <?php echo $items ?>
                    }
                },
                width: 'auto',
                prev: {
                    button: '#<?php echo $sliderId; ?> .navigation .prev'
                },
                next: {
                    button: '#<?php echo $sliderId; ?> .navigation .next'
                },
                pagination: '#<?php echo $sliderId; ?> > .pagination',
                mousewheel: false,
                auto: false,
                pauseOnHover: true,
                onCreate: function()
                {

                },
                swipe: {
                    onTouch: true,
                    onMouse: true
                },
                scroll : {
                    items           : 1,
                    easing          : "swing",
                    duration        : 700,
                    pauseOnHover    : true
                }
            });
        });
    })(jQuery);
</script>