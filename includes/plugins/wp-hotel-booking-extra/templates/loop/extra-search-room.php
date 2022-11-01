<?php
/**
 * The template for displaying extra package in search room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-extra/loop/extra-search-room.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Extra/Templates
 * @version 1.9.7.4
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

// HB_Room_Extra instead of HB_Room
$room_extra = HB_Room_Extra::instance( $post_id );
$room_extra = $room_extra->get_extra();

if ( $room_extra ) { ?>
	<?php if ( ! get_option( 'tp_hotel_booking_custom_process' ) || is_singular( 'hb_room' ) ) { ?>
		<div class="hb_addition_package_extra">
			<div class="hb_addition_package_title">
				<h5 class="hb_addition_package_title_toggle">
					<a href="javascript:void(0)" class="hb_package_toggle">
						<?php esc_html_e( 'Optional Extras', 'wp-hotel-booking' ); ?>
					</a>
				</h5>
			</div>
			<div class="hb_addition_packages">
				<ul class="hb_addition_packages_ul">
					<?php foreach ( $room_extra as $key => $extra ) { ?>
						<li data-price="<?php echo esc_attr( $extra->amount_singular ); ?>">
							<div class="hb_extra_optional_right">
								<input type="<?php echo $extra->required ? 'hidden' : 'checkbox'; ?>"
									   name="hb_optional_quantity_selected[<?php echo esc_attr( $extra->ID ); ?>]"
									   class="hb_optional_quantity_selected"
									   id="<?php echo esc_attr( 'hb-ex-room-' . $post_id . '-' . $key ); ?>" <?php echo $extra->required ? 'checked="checked" ' : ''; ?>
									   data-id = "<?php echo esc_attr( $extra->ID ); ?>"
								/>
							</div>
							<div class="hb_extra_optional_left">
								<div class="hb_extra_title">
									<div class="hb_package_title">
										<label for="<?php echo esc_attr( 'hb-ex-room-' . $post_id . '-' . $key ); ?>"><?php printf( '%s', $extra->title ); ?></label>
									</div>
									<p>
										<?php
											remove_all_filters('the_content');
											$description = apply_filters( 'the_content', $extra->description );
											echo str_replace( ']]>', ']]&gt;', $description );
										?>
									</p>
								</div>
								<div class="hb_extra_detail_price">
									<?php if ( $extra->respondent === 'number' ) { ?>
										<input type="number" step="1" min="1"
											   name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
											   value="1"
											   class="hb_optional_quantity"
										/>
									<?php } else { ?>
										<input type="hidden" step="1" min="1"
											   name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
											   value="1" />
									<?php } ?>
									<label>
										<strong><?php echo $extra->price; ?></strong>
										<small><?php printf( '/ %s', $extra->respondent_name ? $extra->respondent_name : __( 'Package', 'wp-hotel-booking' ) ); ?></small>
									</label>
								</div>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	<?php } ?>
	<?php
}
