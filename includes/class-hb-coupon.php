<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! defined( 'TP_HOTEL_COUPON' ) ) {
    define( 'TP_HOTEL_COUPON', true );
}
/**
 * Class HB_Coupon
 */
class HB_Coupon{
    /**
     * @var array
     */
    static protected $_instance = array();

    /**
     * @var bool
     */
    public $post = false;

    /**
     * @var bool
     */
    protected $_settings = array();

    /**
     * @param $post
     */
    function __construct( $post ){
        if( is_numeric( $post ) ) {
            $this->post = get_post( $post );
        }elseif( $post instanceof WP_Post || ( is_object( $post ) && ! ( $post instanceof HB_Coupon ) ) ){
            $this->post = $post;
        }elseif( $post instanceof HB_Coupon ){
            $this->post = $post->post;
        }
        $this->_load_settings();

        add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
    }

    private function _load_settings(){
        if( ! empty( $this->post->ID ) ){
            if( $metas = get_post_meta( $this->post->ID ) ){
                foreach( $metas as $k => $v ){
                    $k = str_replace( '_hb_', '', $k );
                    $this->_settings[ $k ] = $v[0];
                }
            }
        }
    }

    function __get( $prop ){
        $return = false;
        switch( $prop ){
            case 'discount_value':
                $return = $this->get_discount_value();
                break;
            case 'coupon_code':
                $return = $this->post->post_title;
                break;
            default:
                if( ! empty( $this->post->{$prop} ) ){
                    $return = $this->post->{$prop};
                }
        }
        return $return;
    }

    function get_discount_value(){
        remove_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

        $discount = 0;
        switch( $this->_settings['coupon_discount_type'] ){
            case 'percent_cart':
                $cart = HB_Cart::instance();
                $cart_sub_total = $cart->get_sub_total();
                $discount = $cart_sub_total * $this->_settings['coupon_discount_value'] / 100;
                break;
            case 'fixed_cart':
                $discount = $this->_settings['coupon_discount_value'];
                break;
        }
        add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );

        return $discount;
    }

    function apply_sub_total_discount( $sub_total ){
        $discount = $this->get_discount_value();
        return $sub_total - $discount;
    }

    function get_cart_sub_total(){
        remove_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
        $cart = HB_Cart::instance();
        $cart_sub_total = $cart->get_sub_total();
        add_filter( 'hb_cart_sub_total', array( $this, 'apply_sub_total_discount' ), 999 );
        return $cart_sub_total;
    }

    function validate(){
        $return = array(
            'is_valid'      => true
        );
        if( ! empty( $this->_settings['minimum_spend' ] ) && ( $minimum_spend = intval( $this->_settings['minimum_spend'] ) > 0 ) ){
            $return['is_valid'] = $this->get_cart_sub_total() >= $minimum_spend;
            if( ! $return['is_valid'] ) {
                $return['message'] = sprintf(__('The minimum spend for this coupon is %s.', 'tp-hotel-booking'), $minimum_spend);
            }
        }

        if( $return['is_valid'] &&  ! empty( $this->_settings['maximum_spend' ] ) && ( $maximum_spend = intval( $this->_settings['maximum_spend'] ) > 0 ) ){
            $return['is_valid'] = $this->get_cart_sub_total() <= $maximum_spend;
            if( ! $return['is_valid'] ) {
                $return['message'] = sprintf(__('The maximum spend for this coupon is %s.', 'tp-hotel-booking'), $maximum_spend);
            }
        }

        if( $return['is_valid'] &&  ! empty( $this->_settings['limit_per_coupon' ] ) && ( $limit_per_coupon = intval( $this->_settings['limit_per_coupon'] ) ) > 0 ){
            $usage_count = ! empty( $this->_settings['usage_count'] ) ? intval( $this->_settings['usage_count'] ) : 0;
            $return['is_valid'] = $limit_per_coupon > $usage_count;
            if( ! $return['is_valid'] ) {
                $return['message'] = __('Coupon usage limit has been reached.', 'tp-hotel-booking');
            }
        }

        return $return;
    }

    /**
     * Get unique instance of HB_Room
     *
     * @param $coupon
     * @return mixed
     */
    static function instance( $coupon ){
        $post = $coupon;
        if( $coupon instanceof WP_Post ){
            $id = $coupon->ID;
        }elseif( is_object( $coupon ) && isset( $coupon->ID ) ){
            $id = $coupon->ID;
        }elseif( $coupon instanceof HB_Coupon ) {
            $id = $coupon->post->ID;
        }else{
            $id = $coupon;
        }
        if( empty( self::$_instance[ $id ] ) ){
            self::$_instance[ $id ] = new self( $post );
        }
        return self::$_instance[ $id ];
    }
}

add_action( 'hotel_booking_before_cart_total', 'hotel_booking_before_cart_total' );
if ( ! function_exists( 'hotel_booking_before_cart_total' ) ) {
    function hotel_booking_before_cart_total() {
        $settings = hb_settings();
        if( defined( 'TP_HOTEL_COUPON' ) && TP_HOTEL_COUPON && $settings->get( 'enable_coupon' ) ) { ?>
            <?php
            // if( $coupon = get_transient( 'hb_user_coupon_' . session_id() ) ) {
            if( $coupon = TP_Hotel_Booking::instance()->cart->coupon ) {
                $coupon = HB_Coupon::instance( $coupon );
                ?>
                <tr class="hb_coupon">
                    <td class="hb_coupon_remove" colspan="8">
                        <p class="hb-remove-coupon" align="right">
                            <a href="" id="hb-remove-coupon"><i class="fa fa-times"></i></a>
                        </p>
                        <span class="hb-remove-coupon_code"><?php printf( __( 'Coupon applied: %s', 'tp-hotel-booking' ), $coupon->coupon_code ); ?></span>
                        <span class="hb-align-right">
                            -<?php echo hb_format_price( $coupon->discount_value ); ?>
                        </span>
                    </td>
                </tr>
            <?php } else { ?>
                <tr class="hb_coupon">
                    <td colspan="8" class="hb-align-center" >
                        <input type="text" name="hb-coupon-code" value="" placeholder="<?php _e( 'Coupon', 'tp-hotel-booking' ); ?>" style="width: 150px; vertical-align: top;" />
                        <button type="button" id="hb-apply-coupon"><?php _e( 'Apply Coupon', 'tp-hotel-booking' ); ?></button>
                    </td>
                </tr>
            <?php } ?>
        <?php }
    }
}

add_action( 'init', 'register_post_types_coupon' );

if ( ! function_exists( 'register_post_types_coupon' ) ) {

    function register_post_types_coupon() {
        /**
         * Register custom post type for booking
         */
        $args = array(
            'labels'             => array(
                'name'               => _x( 'Coupons', 'Coupons', 'tp-hotel-coupon' ),
                'singular_name'      => _x( 'Coupon', 'Coupon', 'tp-hotel-coupon' ),
                'menu_name'          => __( 'Coupons', 'tp-hotel-coupon' ),
                'parent_item_colon'  => __( 'Parent Item:', 'tp-hotel-coupon' ),
                'all_items'          => __( 'Coupons', 'tp-hotel-coupon' ),
                'view_item'          => __( 'View Coupon', 'tp-hotel-coupon' ),
                'add_new_item'       => __( 'Add New Coupon', 'tp-hotel-coupon' ),
                'add_new'            => __( 'Add New', 'tp-hotel-coupon' ),
                'edit_item'          => __( 'Edit Coupon', 'tp-hotel-coupon' ),
                'update_item'        => __( 'Update Coupon', 'tp-hotel-coupon' ),
                'search_items'       => __( 'Search Coupon', 'tp-hotel-coupon' ),
                'not_found'          => __( 'No coupon found', 'tp-hotel-coupon' ),
                'not_found_in_trash' => __( 'No coupon found in Trash', 'tp-hotel-coupon' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( 'title' ),
            'hierarchical'       => false
        );
        $args = apply_filters( 'hotel_booking_register_post_type_coupon_arg', $args );
        register_post_type( 'hb_coupon', $args );
    }
}