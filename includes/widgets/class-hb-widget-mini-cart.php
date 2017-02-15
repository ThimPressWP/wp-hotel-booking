<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class HB_Widget_Cart
 *
 * Display form for search rooms
 * @extends WP_Widget
 */
class HB_Widget_Mini_Cart extends WP_Widget{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'hb_widget_cart', // widget base id
            __( 'HB Widget Mini Cart', 'wp-hotel-booking' ), // name of widget
            array( 'description' => __( "Display order of customer", 'wp-hotel-booking' ) ) // description widget
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
        echo sprintf( '%s', $args['before_widget'] );
        $html = array();
        if( $instance )
        {
            $html[] = '[hotel_booking_mini_cart';
            foreach ($instance as $att => $param) {
                if( is_array($param) )
                    continue;
                $html[] = $att.'="'.$param.'"';
            }
            $html[] = '][/hotel_booking_mini_cart]';
        }
        echo do_shortcode( implode(' ', $html) );
        echo sprintf( '%s', $args['after_widget'] );
    }

    /**
     * Widget options
     * @param $instance
     */
    public function form( $instance )
    {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $number = ! empty( $instance['number'] ) ? $instance['number'] : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wp-hotel-booking' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
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

        return $instance;
    }

}