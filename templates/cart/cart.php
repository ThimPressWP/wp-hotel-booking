<?php
/**
 * The template for displaying cart page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/cart/cart.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9.7.8
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * @var $cart WPHB_Cart
 */
$cart = WP_Hotel_Booking::instance()->cart;
global $hb_settings;
$settings = hb_settings();
$rooms    = $cart->get_rooms();
// Hook before cart content
do_action( 'hotel_booking_before_cart' );
?>

<?php
if ( $cart->cart_items_count != 0 ) :
	?>
	<?php
	// Check if any room has deposit enabled
	$has_deposit = false;
	if ( $rooms ) {
		foreach ( $rooms as $room ) {
			if ( get_post_meta( $room->ID, '_hb_enable_deposit', true ) == 1 ) {
				$has_deposit = true;
				break;
			}
		}
	}
	?>
	<div id="hotel-booking-cart" class="hb-cart-modern">
		<form id="hb-cart-form" method="post">
			<div class="hb-cart-wrapper">
				<!-- Left Column: Cart Items Table -->
				<div class="hb-cart-items-column">
					<table class="hb-cart-table">
						<thead>
							<tr>
								<th class="hb-col-room" colspan="2">
									<?php
									_e( 'Room', 'wp-hotel-booking' );
									?>
								</th>
								<th class="hb-col-price">
									<?php
									_e( 'Price', 'wp-hotel-booking' );
									?>
								</th>
								<th class="hb-col-quantity">
									<?php
									_e( 'Quanlity', 'wp-hotel-booking' );
									?>
								</th>
								<?php
								if ( $has_deposit ) :
									?>
									<th class="hb-col-deposit">
										<?php
										_e( 'Deposit Payment', 'wp-hotel-booking' );
										?>
									</th>
									<?php
								endif;
								?>
								<th class="hb-col-subtotal">
									<?php
									_e( 'Sub total', 'wp-hotel-booking' );
									?>
								</th>
							</tr>
						</thead>

						<tbody>
							<?php
							if ( $rooms ) :
								?>
								<?php
								foreach ( $rooms as $cart_id => $room ) :
									?>
									<?php
									/**
									 * @var $room WPHB_Room
									 */
									$num_of_rooms = (int) $room->get_data( 'quantity' );
									if ( $num_of_rooms === 0 ) {
										continue;
									}

									// Extra packages
									$cart_extra = $cart->get_extra_packages( $cart_id );

									// Room image
									$room_image = get_the_post_thumbnail_url( $room->ID, 'medium' );
									if ( ! $room_image ) {
										$room_image = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src(
											'medium'
										) : '';
									}

									// Date calculations
									$check_in_date  = $room->get_data( 'check_in_date' );
									$check_out_date = $room->get_data( 'check_out_date' );
									$nights_count   = hb_count_nights_two_dates( $check_out_date, $check_in_date );

									// Guest info
									$adult_qty = (int) $room->get_data( 'adult_qty' );
									$child_qty = (int) $room->get_data( 'child_qty' );

									// Deposit info
									$enable_deposit = get_post_meta( $room->ID, '_hb_enable_deposit', true );
									$deposit_type   = get_post_meta( $room->ID, '_hb_deposit_type', true );
									$deposit_amount = get_post_meta( $room->ID, '_hb_deposit_amount', true );

									if ( $deposit_type === 'percent' ) {
										$deposit_display = $deposit_amount . '%';
									} elseif ( $deposit_type === 'fixed' ) {
										$deposit_display = hb_format_price( $deposit_amount );
									} else {
										$deposit_display = '';
									}

									// Calculate extra rows for rowspan
									?>
									<tr class="hb-cart-item" data-cart-id="
											<?php
											echo esc_attr( $cart_id );
											?>
									">
										<!-- Remove Button -->
										<td class="hb-col-remove">
											<a href="javascript:void(0)" class="hb-cart-item-remove hb_remove_cart_item"
												data-cart-id="
											<?php
											echo esc_attr( $cart_id );
											?>
												" title="
											<?php
											esc_attr_e( 'Remove this item', 'wp-hotel-booking' );
											?>
												">
												<span>&times;</span>
											</a>
										</td>

										<!-- Room Info -->
										<td class="hb-col-info">
											<?php
											if ( $room_image ) :
												?>
												<a href="
															<?php
															echo esc_url( get_permalink( $room->ID ) );
															?>
												" class="hb-room-thumb">
													<img src="
															<?php
															echo esc_url( $room_image );
															?>
													" alt="
															<?php
															echo esc_attr( $room->name );
															?>
													">
												</a>
												<?php
											endif;
											?>
											<div class="hb-room-details">
												<h6 class="hb-cart-item-title">
													<a href="
												<?php
												echo esc_url( get_permalink( $room->ID ) );
												?>
													">
														<?php
														echo esc_html( $room->name );
														?>
													</a>
												</h6>
												<div class="hb-cart-item-meta">
													<!-- Check-in / Check-out dates -->
													<p class="hb-cart-item-date">
														<span class="hb-meta-label">
															<?php
															esc_html_e( 'Date:', 'wp-hotel-booking' );
															?>
														</span>
														<?php
														$date_format = hb_get_date_format();
														echo date_i18n( $date_format, strtotime( $check_in_date ) );
														?>
														<span class="hb-date-separator">-</span>
														<?php
														echo date_i18n( $date_format, strtotime( $check_out_date ) );
														?>
													</p>

													<!-- Guest info -->
													<p class="hb-cart-item-guests">
														<span class="hb-meta-label">
															<?php
															esc_html_e( 'Details:', 'wp-hotel-booking' );
															?>
														</span>
														<span class="hb-guest-rooms">
															<?php
															echo esc_html(
																sprintf(
																	__( 'Room: %d', 'wp-hotel-booking' ),
																	$num_of_rooms
																)
															);
															?>
															;
														</span>
														<span class="hb-guest-adults">
															<?php
															echo esc_html(
																sprintf(
																	__( 'Adults: %d', 'wp-hotel-booking' ),
																	$adult_qty
																)
															);
															?>
														</span>
														<?php
														if ( $child_qty > 0 ) :
															?>
															<span class="hb-guest-children">
																;
																<?php
																echo esc_html(
																	sprintf(
																		__(
																			'Children: %d',
																			'wp-hotel-booking'
																		),
																		$child_qty
																	)
																);
																?>
															</span>
															<?php
														endif;
														?>
													</p>
													<?php
													// Get extra packages for this room
													if ( defined( 'WPHB_EXTRA_FILE' ) && $cart_extra ) :
														$cart_contents  = WP_Hotel_Booking::instance()->cart->cart_contents;
														$extra_packages = [];

														if ( $cart_contents ) {
															foreach ( $cart_contents as $cart_item_id => $cart_item ) {
																if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
																	// extra class
																	$extra            = HB_Extra_Package::instance(
																		$cart_item->product_id
																	);
																	$extra_packages[] = [
																		'package_title' => $extra->title,
																		'package_price' => hb_format_price(
																			$extra->amount_singular_exclude_tax
																		),
																		'package_id' => $extra->ID,
																		'cart_id' => $cart_item_id,
																		'package_quantity' => $cart_item->quantity,
																		'required' => $extra->required,
																	];
																}
															}
														}

														if ( ! empty( $extra_packages ) ) :
															?>
															<!-- Extra Packages -->
															<div class="hb-cart-extra-packages">
																<p class="hb-extra-packages-label">
																	<span class="hb-meta-label">
																		<?php
																		_e(
																			'Extra Services:',
																			'wp-hotel-booking'
																		);
																		?>
																	</span>
																</p>
																<ul class="hb-extra-packages-list ">
																	<?php
																	foreach ( $extra_packages as $extra_package ) :
																		?>
																		<li class="hb-extra-package-item ">
																			<span class="hb-extra-package-title">
																				<?php
																				echo esc_html(
																					$extra_package['package_title']
																				);
																				?>
																			</span>
																			<span class="hb-extra-package-meta">
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
																			</span>

																		</li>
																		<?php
																	endforeach;
																	?>
																</ul>
															</div>
															<?php
														endif;
													endif;
													?>
												</div>
											</div>
										</td>
										<!-- Price per night -->
										<td class="hb-col-price">
											<?php
											echo hb_format_price(
												$room->get_total(
													$room->check_in_date,
													$room->check_out_date,
													1,
													false
												) / $nights_count
											);
											?>
										</td>
										<!-- Quantity -->
										<td class="hb-col-quantity">
											<div class="hb-quantity-control">
												<?php
												echo '<input type="number" min="0" class="hb_room_number_edit" name="hotel_booking_cart[' . esc_attr( $cart_id ) . ']" value="' . esc_attr( $num_of_rooms ) . '" aria-label="' . esc_attr__( 'Room quantity', 'wp-hotel-booking' ) . '" />';
												?>
											</div>
										</td>
										<?php
										if ( $has_deposit ) :
											?>
											<!-- Deposit Payment -->
											<td class="hb-col-deposit">
												<?php
												if ( $enable_deposit == 1 && $deposit_display ) :
													?>
													<span class="hb-deposit-amount">
														<?php
														echo esc_html( $deposit_display );
														?>
													</span>
													<?php
												else :
													?>
													<span class="hb-no-deposit">—</span>
													<?php
												endif;
												?>
											</td>
											<?php
										endif;
										?>
										<!-- Subtotal -->
										<td class="hb-col-subtotal">
											<?php
											// Calculate subtotal: room total + extra packages - deposit
											$room_subtotal = $room->total;

											// Add extra packages total
											$extra_packages_total = 0;
											if ( defined( 'WPHB_EXTRA_FILE' ) && $cart_extra ) {
												$cart_contents = WP_Hotel_Booking::instance()->cart->cart_contents;
												if ( $cart_contents ) {
													foreach ( $cart_contents as $cart_item ) {
														if ( isset( $cart_item->parent_id ) && $cart_item->parent_id === $cart_id ) {
															$extra                 = HB_Extra_Package::instance( $cart_item->product_id );
															$extra_packages_total += floatval(
																$extra->amount_singular_exclude_tax
															) * intval( $cart_item->quantity );
														}
													}
												}
											}
											$room_subtotal += $extra_packages_total;

											// Subtract deposit if enabled
											if ( $enable_deposit == 1 && $deposit_amount > 0 ) {
												if ( $deposit_type === 'percent' ) {
													$deposit_value = ( $room->total + $extra_packages_total ) * ( $deposit_amount / 100 );
												} else {
													// Fixed deposit
													$deposit_value = floatval( $deposit_amount );
												}
												$room_subtotal -= $deposit_value;
											}
											?>
											<span class="hb-price-amount">
												<?php
												echo hb_format_price( $room_subtotal );
												?>
											</span>
										</td>
									</tr>
									<?php
								endforeach;
								?>
								<?php
							endif;
							?>
						</tbody>
					</table>
					<div class="hd-coupons-update-cart">
						<?php
						// Hook before cart totals (for coupons, promotions, etc.)
						// do_action('hotel_booking_before_cart_total');
						if ( defined( 'TP_HOTEL_COUPON' ) && TP_HOTEL_COUPON && $settings->get( 'enable_coupon' ) ) {
							if ( $coupon = WP_Hotel_Booking::instance()->cart->coupon ) {
								$coupon = HB_Coupon::instance( $coupon )->coupon_code;
							} else {
								$coupon = '';
							}
							?>
							<div class="hb_coupon">
								<div colspan="9" class="hb-align-left">
									<?php
									echo '<input type="text" name="hb-coupon-code" value="' . esc_attr( $coupon ) . '" placeholder="' . esc_attr__( 'Coupon', 'wp-hotel-booking' ) . '" />';
									?>
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
					<?php
					wp_nonce_field( 'hb_cart_field', 'hb_cart_field' );
					?>
				</div>

				<!-- Right Column: Cart Total -->
				<div class="hb-cart-total-column">
					<div class="hb-cart-total-box">
						<h4 class="hb-cart-total-title">
							<?php
							_e( 'Cart Total', 'wp-hotel-booking' );
							?>
						</h4>

						<div class="hb-cart-total-row">
							<span class="hb-cart-total-label">
								<?php
								_e( 'Subtotal', 'wp-hotel-booking' );
								?>
							</span>
							<span class="hb-cart-total-value hb_sub_total_value">
								<?php
								echo hb_format_price( $cart->sub_total );
								?>
							</span>
						</div>

						<?php
						// Display Extra Packages Total (if any)
						$extra_total = 0;
						if ( defined( 'WPHB_EXTRA_FILE' ) && $rooms ) {
							foreach ( $rooms as $cart_id => $room ) {
								$cart_extra = $cart->get_extra_packages( $cart_id );
								if ( $cart_extra && is_array( $cart_extra ) ) {
									foreach ( $cart_extra as $extra ) {
										$extra_total += isset( $extra->total ) ? $extra->total : 0;
									}
								}
							}
						}
						if ( $extra_total > 0 ) :
							?>
							<div class="hb-cart-total-row hb-cart-extras-total">
								<span class="hb-cart-total-label">
									<?php
									_e( 'Extra Services', 'wp-hotel-booking' );
									?>
								</span>
								<span class="hb-cart-total-value">
									<?php
									echo hb_format_price( $extra_total );
									?>
								</span>
							</div>
							<?php
						endif;
						?>

						<?php
						if ( $tax = hb_get_tax_settings() ) :
							?>
							<div class="hb-cart-total-row hb-cart-tax">
								<span class="hb-cart-total-label">
									<?php
									_e( 'Tax', 'wp-hotel-booking' );
									?>
									<?php
									if ( $tax < 0 ) :
										?>
										<small>(
											<?php
											_e( 'included', 'wp-hotel-booking' );
											?>
											)</small>
										<?php
									endif;
									?>
								</span>
								<span class="hb-cart-total-value">
									<?php
									echo apply_filters(
										'hotel_booking_cart_tax_display',
										abs( $tax * 100 ) . '%'
									);
									?>
								</span>
							</div>
							<?php
						endif;
						?>

						<?php
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
						// Hook for additional fees/discounts before grand total
						do_action( 'hotel_booking_cart_before_grand_total', $cart );
						?>
						<div class="hb-cart-total-row hb-cart-grand-total">
							<span class="hb-cart-total-label">
								<?php
								_e( 'Total', 'wp-hotel-booking' );
								?>
							</span>
							<span class="hb-cart-total-value hb_grand_total_value">
								<?php
								echo hb_format_price( $cart->total );
								?>
							</span>
						</div>

						<?php
						if ( $advance_payment = $cart->advance_payment ) :
							?>
							<div class="hb-cart-total-row hb-cart-advance">
								<span class="hb-cart-total-label">
									<?php
									_e( 'Advance Payment', 'wp-hotel-booking' );
									?>
								</span>
								<span class="hb-cart-total-value hb_advance_payment_value">
									<?php
									echo hb_format_price( $advance_payment );
									?>
								</span>
							</div>
							<?php
						endif;
						?>

						<?php
						// Hook after cart totals (for payment options, terms, etc.)
						do_action( 'hotel_booking_after_cart_total', $cart );
						?>
						<a href="
						<?php
						echo esc_url( hb_get_checkout_url() );
						?>
						" class="hb-btn hb-btn-checkout ">
							<?php
							_e( 'Proceed to Checkout', 'wp-hotel-booking' );
							?>
						</a>
					</div>
				</div>
			</div>

		</form>
	</div>
	<?php
else :
	?>
	<!-- Empty cart -->
	<div class="hb-empty-cart">
		<div class="hb-empty-cart-icon">
			<i class="fa fa-shopping-cart"></i>
		</div>
		<h3 class="hb-empty-cart-title">
			<?php
			_e( 'Your cart is empty', 'wp-hotel-booking' );
			?>
		</h3>
		<p class="hb-empty-cart-message">
			<?php
			_e( 'Looks like you haven\'t added any rooms yet. Start exploring our rooms!', 'wp-hotel-booking' );
			?>
		</p>
		<a href="
		<?php
		echo esc_url( get_post_type_archive_link( 'hb_room' ) );
		?>
		" class="hb-btn hb-btn-primary">
			<?php
			_e( 'Browse Rooms', 'wp-hotel-booking' );
			?>
		</a>
	</div>
	<?php
endif;
?>

<?php
// Hook after cart content
do_action( 'hotel_booking_after_cart' );
?>