<?php
/**
 * WP Hotel Booking admin tools class.
 *
 * @class       WPHB_Admin_Tools
 * @version     1.9.7.4
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Tools' ) ) {
	/**
	 * Class WPHB_Admin_Tools.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Tools {
		/**
		 * Get admin setting page tabs.
		 *
		 * @since 2.0
		 *
		 * @return array
		 */
		public static function get_tools_pages() {
			$tabs = array();

			$tabs[] = include_once 'tools/class-wphb-tool-override-templates.php';
			$tabs[] = include_once 'tools/class-wphb-tool-updates.php';

			return apply_filters( 'wphb/admin/tool-page-tabs', $tabs );
		}

		/**
		 * Output tools page.
		 *
		 * @since 2.0
		 */
		public static function output() {
			self::get_tools_pages();
			$tabs         = wphb_get_admin_tools_tabs();
			$selected_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';

			if ( ! array_key_exists( $selected_tab, $tabs ) ) {
				$tab_keys     = array_keys( $tabs );
				$selected_tab = reset( $tab_keys );
			} ?>

			<div class="wrap">
				<h2 class="nav-tab-wrapper">
					<?php if ( $tabs ) { ?>
						<?php foreach ( $tabs as $slug => $title ) { ?>
							<a class="nav-tab<?php echo esc_attr( sprintf( '%s', $selected_tab == $slug ? ' nav-tab-active' : '' ) ); ?>"
							   href="?page=wphb-tools&tab=<?php echo esc_attr( $slug ); ?>">
								<?php echo esc_html( $title ); ?>
							</a>
						<?php } ?>
					<?php } ?>
				</h2>

				<?php do_action( 'wphb/admin/tools-tab-before', $selected_tab ); ?>
				<?php do_action( 'wphb/admin/tools-tab-' . $selected_tab ); ?>
				<?php wp_nonce_field( 'wphb_admin_settings_tab_' . $selected_tab, 'wphb_admin_settings_tab_' . $selected_tab . '_field' ); ?>
				<?php do_action( 'wphb/admin/tools-tab-after', $selected_tab ); ?>
			</div>
			<?php
		}
	}
}
