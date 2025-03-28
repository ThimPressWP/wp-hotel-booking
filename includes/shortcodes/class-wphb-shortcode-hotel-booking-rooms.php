<?php
/**
 * WP Hotel Booking list rooms shortcode.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes/Shortcode
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Shortcode_Hotel_Booking_Rooms extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_rooms';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		/* remove action */
		remove_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 );
		ob_start();
	?>

		<?php
			/**
			 * @see ArchiveRoomTemplate
			 * wphb/list-rooms/layout hook
			 */
			do_action( 'wphb/list-rooms/layout', $atts );
		?>

		<?php /* readd action */
			add_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 ); 
		?>

	<?php return ob_get_clean(); }
}

new WPHB_Shortcode_Hotel_Booking_Rooms();