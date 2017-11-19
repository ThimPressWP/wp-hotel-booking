<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_Shortcode_Hotel_Booking_Mini_Cart extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_mini_cart';

	public function __construct() {
		parent::__construct();
//        add_action( 'wp_footer', array( $this, 'mini_cart' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'utils' ) );
	}

	function add_shortcode( $atts, $content = null ) {
		?>
        <div id="hotel_booking_mini_cart_<?php echo uniqid() ?>" class="hotel_booking_mini_cart">
			<?php if ( isset( $atts['title'] ) && $atts['title'] ): ?>

                <h3><?php echo esc_html( $atts['title'] ); ?></h3>

			<?php endif; ?>

			<?php
			$cart_content = WP_Hotel_Booking::instance()->cart->cart_contents;
			if ( ! empty( $cart_content ) ): ?>

				<?php hb_get_template( 'cart/mini_cart.php' ); ?>

			<?php else: ?>

                <p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty.', 'wp-hotel-booking' ) ?></p>

			<?php endif; ?>
        </div>
		<?php
	}

	function mini_cart() {
		echo hb_get_template_content( 'cart/mini_cart_layout.php' );
	}

	function utils() {
		wp_enqueue_script( 'wp-util' );
	}

}

new WPHB_Shortcode_Hotel_Booking_Mini_Cart();
