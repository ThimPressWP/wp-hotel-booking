<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class HB_Widget_Room_Carousel
 *
 * Display form for search rooms
 * @extends WP_Widget
 */
class HB_Widget_Room_Carousel extends WP_Widget{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'hb_widget_carousel', // widget base id
            __( 'HB Rooms Carousel', 'tp-hotel-booking' ), // name of widget
            array( 'description' => __( "Display rooms slider", 'tp-hotel-booking' ) ) // description widget
        );
    }

    /**
     * Display the search form in widget
     *
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget( $args, $instance )
    {
        echo $args['before_widget'];
        $number_rooms = isset($instance['rooms']) ? (int)$instance['rooms'] : 10;
        $items = isset($instance['number']) ? (int)$instance['number'] : 4;
        // global $wpdb;
        // $sql = "SELECT * FROM  `$wpdb->terms`
        //         INNER JOIN `$wpdb->term_taxonomy`
        //         ON `$wpdb->terms`.`term_id` = `$wpdb->term_taxonomy`.`term_id`
        //         WHERE `$wpdb->term_taxonomy`.`taxonomy` = 'hb_room_type' LIMIT {$number_rooms}";
        // $posts = $wpdb->get_results(
        //         $sql,
        //         OBJECT
        //     );
        $terms = get_terms( 'hb_room_type', array('hide_empty' => 0));
        if( $terms ):
    ?>
            <div id="<?php echo $args['widget_id'] ?>">
                <div class="hb_room_carousel">
                    <?php foreach ($terms as $key => $term): ?>
                        <?php $galleries = get_option( 'hb_taxonomy_thumbnail_' . $term->term_id ); ?>
                        <?php $gallery = $galleries ? $galleries[0] : HB_PLUGIN_URL . '/includes/assets/js/carousel/default.png'; ?>
                            <div class="item">
                                <div class="media">
                                    <a href="<?php echo esc_attr(get_term_link($term, 'hb_room_type')); ?>" class="media-image" title="<?php echo esc_attr($term->name); ?>">
                                    <?php echo wp_get_attachment_image($gallery); ?>
                                    </a>
                                </div>
                                <div class="title">
                                    <h4>
                                        <a href="<?php echo esc_attr(get_term_link($term, 'hb_room_type')); ?>" class="media-image"><?php echo esc_attr($term->name); ?></a>
                                    </h4>
                                </div>
                                <div class="price">
                                    <span></span>
                                </div>
                            </div>
                    <?php endforeach; ?>
                </div>
                <?php if( !isset($instance['navigation']) || $instance['navigation'] ): ?>
                    <div class="navigation">
                        <div class="prev"><i class="fa fa-angle-left"></i></div>
                        <div class="next"><i class="fa fa-angle-right"></i></div>
                    </div>
                <?php endif; ?>
                <?php if( !isset($instance['pagination']) || $instance['pagination'] ): ?>
                    <div class="pagination"></div>
                <?php endif; ?>
            </div>
            <script type="text/javascript">
                (function($){
                    "use strict";
                    $(document).ready(function(){
                        $('#<?php echo $args['widget_id'] ?> .hb_room_carousel').carouFredSel({
                            items: {
                                height: 'auto',
                                visible: {
                                    min: 1,
                                    max: 4
                                }
                            },
                            direction               : "left",
                            align                   : "center",
                            width                   : '100%',
                            prev: {
                                button: '#<?php echo $args['widget_id'] ?> .navigation .prev'
                            },
                            next: {
                                button: '#<?php echo $args['widget_id'] ?> .navigation .next'
                            },
                            pagination: '#<?php echo $args['widget_id']; ?> > .pagination',
                            scroll: {
                                items               : 1,
                                easing              : "swing",
                                duration            : 1000,
                                pauseOnHover        : true
                            }
                        });
                    });
                })(jQuery);
            </script>
    <?php
        endif;
        echo $args['after_widget'];
    }

    /**
     * Widget options
     * @param $instance
     */
    public function form( $instance )
    {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Rooms Carousel Slider', 'tp-hotel-booking' );
        $rooms = ! empty( $instance['rooms'] ) ? $instance['rooms'] : 10;
        $number = ! empty( $instance['number'] ) ? $instance['number'] : 4;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'rooms' ); ?>"><?php _e( 'Number of rooms to show:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'rooms' ); ?>" name="<?php echo $this->get_field_name( 'rooms' ); ?>" type="number" value="<?php echo esc_attr( $rooms ); ?>" min="1">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of items:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>" min="1">
        </p>
        <p>
            <label><?php _e( 'Price:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'price' ); ?>1" name="<?php echo $this->get_field_name( 'price' ); ?>" type="radio" value="1"<?php echo (!isset($instance['price']) || $instance['price']) ? 'checked' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'price' ); ?>1"><?php _e('Yes', 'tp-hotel-booking') ?></label>
            <input id="<?php echo $this->get_field_id( 'price' ); ?>0" name="<?php echo $this->get_field_name( 'price' ); ?>" type="radio" value="0"<?php echo (isset($instance['price']) && !$instance['price']) ? 'checked' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'price' ); ?>0"><?php _e('No', 'tp-hotel-booking') ?></label>
        </p>
        <p>
            <label><?php _e( 'Navigation:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'nav' ); ?>1" name="<?php echo $this->get_field_name( 'nav' ); ?>" type="radio" value="1"<?php echo (!isset($instance['nav']) || $instance['nav']) ? 'checked' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'nav' ); ?>1"><?php _e('Yes', 'tp-hotel-booking') ?></label>
            <input id="<?php echo $this->get_field_id( 'nav' ); ?>0" name="<?php echo $this->get_field_name( 'nav' ); ?>" type="radio" value="0"<?php echo (isset($instance['nav']) && !$instance['nav']) ? 'checked' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'nav' ); ?>0"><?php _e('No', 'tp-hotel-booking') ?></label>
        </p>
        <p>
            <label><?php _e( 'Pagination:' ); ?></label>
            <!--yes-->
            <input id="<?php echo $this->get_field_id( 'pagination' ); ?>1" name="<?php echo $this->get_field_name( 'pagination' ); ?>" type="radio" value="1"<?php echo (!isset($instance['pagination']) || $instance['pagination']) ? 'checked' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'pagination' ); ?>1"><?php _e('Yes', 'tp-hotel-booking') ?></label>
            <!--no-->
            <input id="<?php echo $this->get_field_id( 'pagination' ); ?>0" name="<?php echo $this->get_field_name( 'pagination' ); ?>" type="radio" value="0"<?php echo (isset($instance['pagination']) && !$instance['pagination']) ? 'checked' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'pagination' ); ?>0"><?php _e('No', 'tp-hotel-booking') ?></label>
        </p>
        <?php
    }

    /**
     * Handle update
     *
     * @param $new_instance
     * @param $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        // title
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        // rooms
        $instance['rooms'] = ( ! empty( $new_instance['rooms'] ) ) ? strip_tags( $new_instance['rooms'] ) : 10;

        // number
        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : 4;

        // price
        $instance['price'] = ( isset( $new_instance['price'] ) ) ? strip_tags( $new_instance['price'] ) : 1;

        // nav
        $instance['nav'] = ( isset( $new_instance['nav'] ) ) ? strip_tags( $new_instance['nav'] ) : 1;

        // pagination
        $instance['pagination'] = ( isset( $new_instance['pagination'] ) ) ? strip_tags( $new_instance['pagination'] ) : 1;
        return $instance;
    }
}