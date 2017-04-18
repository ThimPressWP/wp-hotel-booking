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
            __( 'HB Search Rooms', 'wp-hotel-booking' ),
            array( 'description' => __( "Display the form for search rooms.", 'wp-hotel-booking' ) )
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
        echo sprintf( '%s', $args['before_widget'] );
        if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
            echo sprintf( '%s', $args['before_title'] . $title . $args['after_title'] );
        }

        // check show title search form
        $show_title = 'true';
        if( isset($instance['show_title']) )
            $show_title = $instance['show_title'];
        // check show label search form
        $show_label = 'true';
        if( isset($instance['show_label']) ) {
            $show_label = $instance['show_label'];
        }
        echo do_shortcode('[hotel_booking show_title="'.$show_title.'" show_label="'.$show_label.'"]');
        echo sprintf( '%s', $args['after_widget'] );
    }

    /**
     * Widget options
     * @param $instance
     */
    function form( $instance ){
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $checked_title = ( !isset($instance['show_title']) || $instance['show_title'] === 'true' ) ? 'checked' : '';
        $checked_label = ( !isset($instance['show_label']) || $instance['show_label'] === 'true' ) ? 'checked' : '';
        ?>
        <p>
            <?php $title_id = $this->get_field_id( 'title' ); ?>
            <label for="<?php echo esc_attr($title_id); ?>"><?php _e( 'Title:', 'wp-hotel-booking' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($title_id); ?>" name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <!--show title, label form-->
        <p>
            <?php $title_id = $this->get_field_id( 'show_title' ); ?>
            <input type="checkbox" id="<?php echo esc_attr($title_id); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_title' )); ?>" value="true"<?php printf( '%s', $checked_title ); ?>>
            <label for="<?php echo esc_attr($title_id); ?>"><?php _e( 'Show title search form', 'wp-hotel-booking' ) ?></label>
        </p>
        <p>
            <?php $label_id = $this->get_field_id( 'show_label' ); ?>
            <input type="checkbox" id="<?php echo esc_attr($label_id); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_label' )); ?>" value="true"<?php printf( '%s', $checked_label ); ?>>
            <label for="<?php echo esc_attr($label_id); ?>"><?php _e( 'Show label search form', 'wp-hotel-booking' ) ?></label>
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

        // show title, label form
        $instance['show_title'] = ( isset( $new_instance['show_title'] ) ) ? strip_tags( $new_instance['show_title'] ) : 'false';
        $instance['show_label'] = ( isset( $new_instance['show_label'] ) ) ? strip_tags( $new_instance['show_label'] ) : 'false';
        return $instance;
    }
}