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
        $html = array();
        if( $instance )
        {
            $html[] = '[hotel_booking_slider';
            foreach ($instance as $att => $param) {
                if( is_array($param) )
                    continue;
                $html[] = $att.'="'.$param.'"';
            }
            $html[] = '][/hotel_booking_slider]';
        }
        echo do_shortcode( implode(' ', $html) );
        echo $args['after_widget'];
    }

    /**
     * Widget options
     * @param $instance
     */
    public function form( $instance )
    {
        $images_size = $this->get_image_sizes();
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $rooms = ! empty( $instance['rooms'] ) ? $instance['rooms'] : 10;
        $number = ! empty( $instance['number'] ) ? $instance['number'] : 4;
        $thumb = ! empty( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
        $text_link = ! empty( $instance['text_link'] ) ? $instance['text_link'] : '';
        $price = isset($instance['price']) ? $instance['price'] : 'min';
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
        <!-- <p>
            <label for="<?php //echo $this->get_field_id( 'image_size' ); ?>"><?php //_e( 'Select Image Size to display:' ); ?></label>
            <select name="<?php //echo $this->get_field_name( 'image_size' ); ?>">
            <?php //foreach ($images_size as $size => $args): ?>
                <option value="<?php //echo $size ?>"<?php //echo $thumb === $size ? ' selected' : '' ?>><?php //echo $args['width'] . 'x' . $args['height'] ?></option>
            <?php //endforeach; ?>
            </select>
        </p> -->
        <p>
            <label><?php _e( 'Price:' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'price' ); ?>" for="<?php echo $this->get_field_id( 'price' ); ?>">
            <?php  $listsPrice = array( '*' => __('No display', 'tp-hotel-booking'), 'min' => __('Min', 'tp-hotel-booking'), 'min_to_max' => __('Min => Max', 'tp-hotel-booking')); ?>
            <?php foreach ($listsPrice as $key => $value): ?>
                <option value="<?php echo esc_attr( $key ); ?>"<?php echo $price === $key ? ' selected' : '' ?>><?php echo $value ?></option>
            <?php endforeach;  ?>
            </select>
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
        <p>
            <label for="<?php echo $this->get_field_id( 'text_link' ); ?>"><?php _e('Text Link', 'tp-hotel-booking') ?></label>
            <input id="<?php echo $this->get_field_id( 'text_link' ); ?>" name="<?php echo $this->get_field_name( 'text_link' ); ?>" type="text" value="<?php echo esc_attr($text_link); ?>">
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

        // text_link
        $instance['text_link'] = ( isset( $new_instance['text_link'] ) ) ? strip_tags( $new_instance['text_link'] ) : '';

        // image_size
        // $instance['image_size'] = ( isset( $new_instance['image_size'] ) ) ? strip_tags( $new_instance['image_size'] ) : 'thumbnail';

        // nav
        $instance['nav'] = ( isset( $new_instance['nav'] ) ) ? strip_tags( $new_instance['nav'] ) : 1;

        // pagination
        $instance['pagination'] = ( isset( $new_instance['pagination'] ) ) ? strip_tags( $new_instance['pagination'] ) : 1;
        return $instance;
    }

    // list image size
    public function get_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array(
                                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
                        );

                }

        }

        // Get only 1 size if found
        if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }

        }

        return $sizes;
    }
}