<?php
/**
 * Admin View: Pricing talbe view.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$room_id = $post->ID;
if ( empty( $room_id ) ) {
	return;
}

$faqs = get_post_meta( $room_id, '_wphb_room_faq', true );
?>
<div class="form-field _hb_room_faq_meta_box">
	<label for="_wphb_faq_room"><?php _e( 'Room faqs', 'wp-hotel-booking' ); ?></label>
	<div class="_hb_room_faq_meta_box__content">
		<div class="_hb_room_faq_meta_box__fields">
			<?php if ( ! empty( $faqs ) ) : ?>
				<?php foreach ( $faqs as $key => $faq ) : ?>
					<div class="_hb_room_faq_meta_box__field">
						<div class="_hb_room_faq_title">
							<span><?php esc_attr_e( 'Title', 'wp-hotel-booking' ); ?></span>
							<input type="text" name="_hb_room_faq_title[]" value="<?php echo esc_attr( $faq[0] ); ?>">
						</div>
						<div class="_hb_room_faq_content">
							<span><?php esc_attr_e( 'Content', 'wp-hotel-booking' ); ?></span>
							<textarea name="_hb_room_faq_content_input[]"><?php echo esc_attr( $faq[1] ); ?></textarea>
						</div>
						<a href="#" class="delete"></a>
						<span class="sort"></span>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<a href="#" class="button button-primary _hb_room_faq_meta_box__add">
			<?php esc_html_e( '+ Add more', 'wp-hotel-booking' ); ?>
		</a>
	</div>
	<div class="faq_append" style="display:none">
		<div class="_hb_room_faq_meta_box__field">
			<div class="_hb_room_faq_title">
				<span><?php esc_attr_e( 'Title', 'wp-hotel-booking' ); ?></span>
				<input type="text" name="_hb_room_faq_title[]" value="">
			</div>
			<div class="_hb_room_faq_content">
				<span><?php esc_attr_e( 'Content', 'wp-hotel-booking' ); ?></span>
				<div class="detai_hb_room_faq_content">
					<textarea name="_hb_room_faq_content_input[]"></textarea>
				</div>
			</div>
			<a href="#" class="delete"></a>
			<span class="sort"></span>
		</div>
	</div>
</div>
