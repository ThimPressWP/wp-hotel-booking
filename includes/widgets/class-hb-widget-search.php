<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class HB_Widget_Search
 *
 * Display form for search rooms
 * @extends WP_Widget
 */
class HB_Widget_Search extends WP_Widget{
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'hb_widget_search',
            __( 'HB Search Rooms', 'tp-hotel-booking' ),
            array( 'description' => __( "Display the form for search rooms.", 'tp-hotel-booking' ) )
        );
    }

    /**
     * Display the search form in widget
     *
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        $search = hb_get_page_permalink( 'search' );
        echo do_shortcode('[hotel_booking search_page="' . $search . '"]');
        echo $args['after_widget'];
    }

    /**
     * Widget options
     * @param $instance
     */
    function form( $instance ){
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search your room', 'tp-hotel-booking' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
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
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}