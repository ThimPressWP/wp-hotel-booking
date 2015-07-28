<?php

/**
 * Class HB_Meta_Box
 */
class HB_Meta_Box{

    /**
     * @var object
     */
    protected static $_meta_boxes = array();

    /**
     * @var array
     */
    protected $_args = array();

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * Construction
     *
     * @param array
     * @param array
     */
    function __construct( $args = array(), $fields = array() ){
        $this->_args    = $args;
        $this->_fields  = $fields;

        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

    }

    function add_meta_box(){
        $meta_box_id    = $this->_args['id'];
        $meta_box_title = $this->_args['title'];
        $callback       = ! empty( $this->_args['callback'] ) ? $this->_args['callback'] : array( $this, 'render' );
        $post_types     = ! empty( $this->_args['post_type'] ) ? $this->_args['post_type'] : 'post';

        if( is_string( $post_types ) ){
            $post_types = explode( ',', $post_types );
        }

        foreach( $post_types as $post_type ) {
            add_meta_box(
                $meta_box_id,
                $meta_box_title,
                $callback,
                $post_type
            );
        }
    }

    function add_field( $field ){
        $this->_fields[] = (array)$field;
        return $this;
    }

    function get_fields(){
        return $this->_fields;
    }

    function has_post_meta( $object_id, $meta_key ){
        $meta_type = 'post';
        $meta_cache = wp_cache_get($object_id, $meta_type . '_meta');

        if ( !$meta_cache ) {
            $meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
            $meta_cache = $meta_cache[$object_id];
        }
        return array_key_exists( $meta_key, $meta_cache );
    }
    function render( $post ){
        if( $fields = $this->_fields ){
            echo '<ul class="hb-form-table">';
            foreach( $fields as $field ){
                echo '<li class="hb-form-field">';
                echo '<label class="hb-form-field-label">' . $field['label'] . '</label>';
                if( $this->has_post_meta( $post->ID, $field['name'] ) ) {
                    $field['std'] = get_post_meta( $post->ID, $field['name'], true );
                }
                if( empty( $field['id'] ) ){
                    $field['id'] = sanitize_title( $field['name'] );
                }
                echo '<div class="hb-form-field-input">';
                echo '<div class="hb-form-field-input-inner">';
                $tmpl = TP_Hotel_Booking::instance()->locate( "includes/meta-box/{$field['type']}.php" );
                require $tmpl;
                if( ! empty( $field['desc'] ) ){
                    printf( '<p class="description">%s</p>', $field['desc'] );
                }
                echo '</div>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        }
        wp_nonce_field( $this->get_nonce_field_action(), $this->get_nonce_field_name() );
    }

    function get_nonce_field_name(){
        return 'meta_box_' . $this->_args['name'];
    }

    function get_nonce_field_action(){
        return 'update_meta_box_' . $this->_args['name'];
    }

    function update( $post_id ){
        if ( ! isset( $_POST[ $this->get_nonce_field_name() ] ) || ! wp_verify_nonce( $_POST[ $this->get_nonce_field_name() ], $this->get_nonce_field_action() ) ) return;
        if( ! $this->_fields ) return;

        foreach( $this->_fields as $field ){
            update_post_meta( $post_id, $field['name'], $_POST[ $field['name'] ] );
        }

    }
    static function update_meta_boxes( $post_id ){
        if( 'post' != strtolower( $_SERVER['REQUEST_METHOD'] ) ) return;
        if( ! ( $meta_boxes = self::$_meta_boxes ) ) return;

        foreach( $meta_boxes as $meta_box ){
            $meta_box->update( $post_id );
        }
    }
    static function instance( $id, $args, $fields ){
        if( empty( self::$_meta_boxes[ $id ] ) ){
            if( empty( $args['id'] ) ) $args['id'] = $id;
            self::$_meta_boxes[ $id ] = new self( $args, $fields );
        }
        return self::$_meta_boxes[ $id ];
    }
}

add_action( 'save_post', array( 'HB_Meta_Box', 'update_meta_boxes' ) );