<?php
/**
 * Template for displaying setup form of static pages while setting up WP Hotel
 *
 * @author  ThimPress
 * @package wp-hotel-booking/Admin/Views
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or exit;

if ( empty( $pages ) ) {
	return;
}

?>
<h2><?php esc_html_e( 'Pages Options', 'wp-hotel-booking' ); ?></h2>

<p><?php esc_html_e( 'The pages will display content of Wp Hotel\'s necessary pages, such as: Rooms, Checkout, Cart', 'wp-hotel-booking' ); ?></p>
<p><?php printf( esc_html__( 'If you are not sure, click <a href="%s" id="create-pages">here</a> to create pages automatically.', 'wp-hotel-booking' ), wp_nonce_url( admin_url( 'index.php?page=wphb-setup&step=pages&auto-create' ) ), 'setup-create-pages' ); ?></p>

<table class="form-field">
	<?php foreach ( $pages as $key => $page ) { ?>
		<tr>
			<th>
				<b><?php esc_html_e( $page['name'], 'wp-hotel-booking' ); ?></b>
			</th>
			<td class="hb-form-field hb-form-field-select_page">
				<div class="list-pages-wrapper">
					<?php
					$selected = hb_settings()->get( $page['id'] );
					$id       = $page['id'];
					hb_dropdown_pages(
						array(
							'show_option_none'  => esc_html__( '---Select page---', 'wp-hotel-booking' ),
							'option_none_value' => '',
							'add_new_title'     => esc_html__( '[ Add new page ]', 'wp-hotel-booking' ),
							'add_new_value'     => 'add_new_page',
							'name'              => 'settings[pages][' . $key . ']',
							'selected'          => $selected,
						)
					);
					?>
					<?php echo esc_html( _x( 'or', 'drop down pages', 'wp-hotel-booking' ) ); ?>
					<button class="button button-quick-add-page" data-id="<?php echo esc_attr($id); ?>" type="button">
						<?php esc_html_e( 'Create new', 'wp-hotel-booking' ); ?>
					</button>
				</div>
				<p class="quick-add-page-inline <?php echo esc_attr( $id ); ?> hide-if-js">
					<input type="text" placeholder="<?php esc_attr_e( 'New page title', 'wp-hotel-booking' ); ?>"/>
					<button class="button" type="button">
						<?php esc_html_e( 'Ok [Enter]', 'wp-hotel-booking' ); ?>
					</button>
					<a href=""><?php esc_html_e( 'Cancel [ESC]', 'wp-hotel-booking' ); ?></a>
				</p>
				<p class="quick-add-page-actions <?php echo esc_attr( $id ); ?> <?php echo esc_attr( ! empty( $selected ) ? '' : ' hide-if-js' ); ?>">
					<a class="edit-page" href="<?php echo get_edit_post_link( $selected ); ?>"
					target="_blank"><?php esc_html_e( 'Edit page', 'wp-hotel-booking' ); ?></a>
					&#124;
					<a class="view-page" href="<?php echo get_permalink( $selected ); ?>"
					target="_blank"><?php esc_html_e( 'View page', 'wp-hotel-booking' ); ?></a>
				</p>
			</td>
		</tr>
	<?php } ?>
</table>
