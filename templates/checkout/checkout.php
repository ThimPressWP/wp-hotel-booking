<?php
/**
 * The template for displaying checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/checkout.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9.7.5
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

global $hb_settings;
$settings = hb_settings();
$cart     = WP_Hotel_Booking::instance()->cart;
do_action( 'hotel_booking_before_checkout_form' );
// Get rooms and calculate totals
$rooms = $cart->get_rooms();
?>
<div id="hotel-booking-payment" class="hb-checkout-modern">
	<form name="hb-payment-form" id="hb-payment-form" method="post" action="
	<?php
	echo isset( $search_page ) ? $search_page : '';
	?>
	">
		<div class="hb-checkout-wrapper">
			<!-- Left Column: Billing Details & Additional Info -->
			<div class="hb-checkout-left-column">

				<?php
				if (
					! is_user_logged_in() && ! hb_settings()->get( 'guest_checkout' ) && get_option(
						'users_can_register'
					)
				) :
					?>
				<div class="hb-checkout-login-notice">
					<?php
						printf(
							__(
								'You have to <strong><a href="%1$s">login</a></strong> or <strong><a href="%2$s">register</a></strong> to checkout.',
								'sailing'
							),
							wp_login_url( hb_get_checkout_url() ),
							wp_registration_url()
						)
					?>
				</div>
					<?php
				else :
					?>

				<!-- Coupon Section -->
				<div class="hb-coupon-toggle-wrapper">
					<div class="hb-coupon-toggle">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
								stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							<path d="M12 16V12" stroke="#007AFF" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
							<path d="M12 8H12.01" stroke="#007AFF" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
						<?php
							_e( 'Have a coupon?', 'sailing' );
						?>
						<a href="#" class="thim-hb-show-coupon-form">
							<?php
							_e( 'Click here to enter your code', 'sailing' );
							?>
						</a>
					</div>
					<div class="thim-hb-coupon-form-wrapper" style="display: none;">
						<?php
						if ( defined( 'TP_HOTEL_COUPON' ) && TP_HOTEL_COUPON && $settings->get( 'enable_coupon' ) ) {
							if ( $coupon = WP_Hotel_Booking::instance()->cart->coupon ) {
								$coupon = HB_Coupon::instance( $coupon )->coupon_code;
							} else {
								$coupon = '';
							}
							?>
						<div class="hb_coupon">
							<div colspan="9" class="hb-align-left">
								<input type="text" name="hb-coupon-code" value="
									<?php
									echo esc_attr( $coupon );
									?>
										" placeholder="
									<?php
										_e( 'Coupon', 'wp-hotel-booking' );
									?>
										" />
								<button type="button" id="hb-apply-coupon">
									<?php
									_e( 'Apply', 'wp-hotel-booking' );
									?>
								</button>
							</div>
						</div>
							<?php
						}
						?>
					</div>
				</div>

				<!-- Billing Detail -->
					<?php
					hb_get_template( 'checkout/customer.php', array( 'customer' => $customer ) );
					?>

				<!-- Additional Information -->
					<?php
					hb_get_template( 'checkout/addition-information.php' );
					?>

					<?php
				endif;
				?>
			</div>

			<!-- Right Column: Order Summary -->
			<div class="hb-checkout-right-column">
				<div class="hb-order-summary">
					<h3 class="hb-order-summary-title">
						<?php
						_e( 'Your order', 'sailing' );
						?>
					</h3>

					<div class="hb-order-summary-content">
						<!-- Product Header -->
						<div class="hb-order-summary-header">
							<span class="hb-order-product-label">
								<?php
								_e( 'Product', 'sailing' );
								?>
							</span>
							<span class="hb-order-subtotal-label">
								<?php
								_e( 'Subtotal', 'sailing' );
								?>
							</span>
						</div>

						<!-- Room Items -->
						<?php
						if ( $rooms ) :
							?>
							<?php
							foreach ( $rooms as $cart_id => $room ) :
								?>
								<?php
								if ( ( $num_of_rooms = (int) $room->get_data( 'quantity' ) ) == 0 ) {
									continue;
								}
								$cart_extra     = $cart->get_extra_packages( $cart_id );
								$check_in_date  = $room->get_data( 'check_in_date' );
								$check_out_date = $room->get_data( 'check_out_date' );
								$nights_count   = hb_count_nights_two_dates( $check_out_date, $check_in_date );
								$adult_qty      = (int) $room->get_data( 'adult_qty' );
								$child_qty      = (int) $room->get_data( 'child_qty' );
								?>

						<div class="hb-order-item">
							<div class="hb-order-item-details">
								<h6 class="hb-order-item-title">
									<?php
											echo esc_html( $room->name );
									?>
									<span class="hb-order-item-qty">×
										<?php
											echo esc_html( $num_of_rooms );
										?>
									</span>
								</h6>
								<div class="hb-order-item-meta">
									<p class="hb-order-item-date">
										<span class="hb-meta-label">
											<?php
												_e( 'Date:', 'sailing' );
											?>
										</span>
										<?php
												$date_format = hb_get_date_format();
												echo date_i18n( $date_format, strtotime( $check_in_date ) );
										?>
										-
										<?php
												echo date_i18n( $date_format, strtotime( $check_out_date ) );
										?>
									</p>
									<p class="hb-order-item-guests">
										<span class="hb-meta-label">
											<?php
												_e( 'Details:', 'sailing' );
											?>
										</span>
										<?php
												echo esc_html( sprintf( __( 'Room: %d', 'sailing' ), $num_of_rooms ) );
										?>
										;
										<?php
												echo esc_html( sprintf( __( 'Adults: %d', 'sailing' ), $adult_qty ) );
										?>
										<?php
										if ( $child_qty > 0 ) :
											?>
										;
											<?php
											echo esc_html(
												sprintf( __( 'Children: %d', 'sailing' ), $child_qty )
											);
											?>
											<?php
												endif;
										?>
									</p>

									<!-- Extra Services -->
									<?php
									if ( defined( 'WPHB_EXTRA_FILE' ) && $cart_extra ) :
										?>
										<?php
										$cart_contents  = WP_Hotel_Booking::instance()->cart->cart_contents;
										$extra_packages = array();

										if ( $cart_contents ) {
											foreach ( $cart_contents as $cart_item_id => $cart_item ) {
												if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
													$extra            = HB_Extra_Package::instance(
														$cart_item->product_id
													);
													$extra_packages[] = array(
														'package_title' => $extra->title,
														'package_price' => hb_format_price(
															$extra->amount_singular_exclude_tax
														),
														'package_quantity' => $cart_item->quantity,
													);
												}
											}
										}

										if ( ! empty( $extra_packages ) ) :
											?>
									<div class="hb-order-extra-services">
										<span class="hb-meta-label">
											<?php
												_e( 'Extra services:', 'sailing' );
											?>
										</span>
										<ul class="hb-extra-services-list">
											<?php
											foreach ( $extra_packages as $extra_package ) :
												?>
											<li>
												<?php
													echo esc_html(
														$extra_package['package_title']
													);
												?>
												(
												<?php
													echo esc_html(
														$extra_package['package_price']
													);
												?>
												×
												<?php
													echo esc_html(
														$extra_package['package_quantity']
													);
												?>
												)
											</li>
												<?php
													endforeach;
											?>
										</ul>
									</div>
											<?php
										endif;
										?>
										<?php
											endif;
									?>
								</div>
							</div>
							<div class="hb-order-item-price">
								<?php
										echo hb_format_price( $room->total );
								?>
							</div>
						</div>

								<?php
							endforeach;
							?>
							<?php
						endif;
						if ( defined( 'TP_HOTEL_COUPON' ) && TP_HOTEL_COUPON && $settings->get( 'enable_coupon' ) ) {
							if ( $coupon = WP_Hotel_Booking::instance()->cart->coupon ) {
								$coupon = HB_Coupon::instance( $coupon );
								?>
						<div class="hb_coupon">
							<div class="hb_coupon_remove" colspan="9">
								<p class="hb-remove-coupon" align="right">
									<a href="" id="hb-remove-coupon"><i class="fa fa-times"></i></a>
								</p>
								<span class="hb-remove-coupon_code">
									<?php
										printf(
											__( 'Coupon applied: %s', 'wp-hotel-booking' ),
											$coupon->coupon_code
										);
									?>
								</span>
								<span class="hb-align-right">
									-
									<?php
											echo hb_format_price( $coupon->discount_value );
									?>
								</span>
							</div>
						</div>
								<?php
							}
						}
						?>
						<!-- Subtotal -->
						<div class="hb-order-summary-row hb-order-subtotal">
							<span class="hb-order-label">
								<?php
								_e( 'Sub total', 'sailing' );
								?>
							</span>
							<span class="hb-order-value hb_sub_total_value">
								<?php
								echo hb_format_price( $cart->sub_total );
								?>
							</span>
						</div>

						<!-- Tax -->
						<?php
						if ( $tax = hb_get_tax_settings() ) :
							?>
						<div class="hb-order-summary-row hb-order-tax">
							<span class="hb-order-label">
								<?php
									_e( 'Tax', 'sailing' );
								?>
								<?php
								if ( $tax < 0 ) :
									?>
								<small>(
									<?php
									_e( 'included', 'sailing' );
									?>
									)</small>
									<?php
									endif;
								?>
							</span>
							<span class="hb-order-value">
								<?php
								echo apply_filters(
									'hotel_booking_cart_tax_display',
									hb_format_price( $cart->total - $cart->sub_total )
								);
								?>
							</span>
						</div>
							<?php
						endif;
						?>
						<?php
						do_action( 'hotel_booking_cart_before_grand_total', $cart );
						?>
						<!-- Total -->
						<div class="hb-order-summary-row hb-order-total">
							<span class="hb-order-label">
								<?php
								_e( 'Total', 'sailing' );
								?>
							</span>
							<span class="hb-order-value hb_grand_total_value">
								<?php
								echo hb_format_price( $cart->total );
								?>
							</span>
						</div>
					</div>

					<!-- Payment Methods -->
					<?php
					if ( ! is_user_logged_in() && ! hb_settings()->get( 'guest_checkout' ) ) {
						?>
						<?php printf( __( 'You have to <strong><a href="%1$s">login</a></strong> or <strong><a href="%2$s">register</a></strong> to checkout.', 'wp-hotel-booking' ), wp_login_url( hb_get_checkout_url() ), wp_registration_url() ); ?>
					<?php } else { ?>
						<?php
						hb_get_template( 'checkout/payment-method.php', array( 'customer' => $customer ) );
						?>
						<?php
						wp_nonce_field( 'hb_customer_place_order', 'hb_customer_place_order_field' );
						?>

					<input type="hidden" name="hotel-booking" value="place_order" />
					<input type="hidden" name="action" value="hotel_booking_place_order" />
					<input type="hidden" name="total_advance" value="
						<?php
						echo esc_attr( $cart->advance_payment ? $cart->advance_payment : $cart->total );
						?>
						" />
					<input type="hidden" name="total_price" value="
						<?php
						echo esc_attr( $cart->total );
						?>
						" />
					<input type="hidden" name="currency" value="
						<?php
						echo esc_attr( hb_get_currency() )
						?>
						">

					<!-- Terms & Conditions -->
						<?php
						if ( $tos_page_id = hb_get_page_id( 'terms' ) ) :
							?>
					<div class="hb-order-terms">
						<label>
							<input type="checkbox" name="tos" value="1" />
							<?php
									printf(
										__( 'I agree with ', 'sailing' ) . '<a href="%s" target="_blank">%s</a>',
										get_permalink( $tos_page_id ),
										get_the_title( $tos_page_id )
									);
							?>
						</label>
					</div>
							<?php
						endif;
						?>

					<!-- Place Order Button -->
					<div class="hb-order-place">
						<button type="submit" class="hb-btn hb-btn-place-order">
							<?php
							_e( 'Place order', 'sailing' );
							?>
						</button>
					</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</form>
</div>

<?php
do_action( 'hotel_booking_after_checkout_form' ); ?>