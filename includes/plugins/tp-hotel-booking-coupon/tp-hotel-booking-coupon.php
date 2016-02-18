<?php
/*
    Plugin Name: TP Hotel Booking Coupon
    Plugin URI: http://thimpress.com/
    Description: TP Hotel Booking Coupon
    Author: ThimPress
    Version: 1.0
    Author URI: http://thimpress.com
*/

define( 'TP_HB_COUPON_DIR', plugin_dir_path( __FILE__ ) );
define( 'TP_HB_COUPON_URI', plugin_dir_url( __FILE__ ) );
define( 'TP_HB_COUPON_VER', 1.0 );

class TP_Hotel_Booking_Coupon
{

	public $is_hotel_active = false;

	function __construct()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * load_textdomain
	 * @return null
	 */
	public function load_textdomain()
	{
		$default = WP_LANG_DIR . '/plugins/tp-hotel-booking-coupon-' . get_locale() . '.mo';
		$plugin_file = TP_HB_COUPON_DIR . '/languages/tp-hotel-booking-coupon-' . get_locale() . '.mo';
		$file = false;
		if ( file_exists( $default ) ) {
			$file = $default;
		} else {
			$file = $plugin_file;
		}
		if ( $file ) {
			load_textdomain( 'tp-hotel-booking-coupon', $file );
		}
	}

	/**
	 * plugin loaded
	 * @return null
	 */
	function plugins_loaded()
	{
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if( class_exists( 'TP_Hotel_Booking' ) && is_plugin_active( 'tp-hotel-booking/tp-hotel-booking.php' ) )
		{
			$this->is_hotel_active = true;
		}

		if( ! $this->is_hotel_active ) {
			add_action( 'admin_notices', array( $this, 'add_notices' ) );
		}
		else
		{
			if( $this->is_hotel_active && ! class_exists( 'HB_Coupon' ) )
			{
				require_once TP_HB_COUPON_DIR . '/inc/class-hb-coupon.php';
				add_action( 'hotel_booking_admin_setting_general', array( $this, 'admin_settings' ) );
				add_action( 'hotel_booking_before_cart_total', array( $this, 'hotel_booking_before_cart_total' ) );
			}
		}
		/**
		 * text-domain
		 */
		$this->load_textdomain();
	}

	/**
	 * notice messages
	 */
	function add_notices()
	{
		?>
			<div class="error">
				<p><?php _e( 'The <strong>TP Hotel Booking</strong> is not installed and/or activated. Please install and/or activate before you can using <strong>TP Hotel Booking Coupon</strong> add-on.'); ?></p>
			</div>
		<?php
	}

	function admin_settings(){
		$settings = hb_settings();
	?>
		    <tr>
		        <th><?php _e( 'Enable Coupon', 'tp-hotel-booking' ); ?></th>
		        <td>
		            <input type="hidden" name="<?php echo $settings->get_field_name('enable_coupon'); ?>" value="0" />
		            <input type="checkbox" name="<?php echo $settings->get_field_name('enable_coupon'); ?>" <?php checked( $settings->get('enable_coupon') ? 1 : 0, 1 ); ?> value="1" />
		        </td>
		    </tr>
	<?php
	}

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

$Coupon = new TP_Hotel_Booking_Coupon();
