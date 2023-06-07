<?php
if ( ! isset( $data ) ) {
	return;
}
?>
    <div class="hb-rating-field">
        <ul class="reating-list">
			<?php
			for ( $i = 1; $i <= 5; $i ++ ) {
				?>
                <li class="list-item">
                    <div class="rating">
                        <label>
                            <input type="checkbox">
                            <span>
                                <?php
                                printf( esc_html( _n( '%s star', '%s stars', $i, 'wp-hotel-booking' ) ), $i );
                                ?>
                            </span>
                        </label>
                    </div>
                    <div class="rating-number">
						<?php echo esc_html( wp_hotel_booking_get_count_rating( $i ) ); ?>
                    </div>
                </li>
				<?php
			}
			?>
            <li class="list-item unrated">
                <div class="rating">
                    <label>
                        <input type="checkbox">
                        <span><?php esc_html_e( 'Unrated', 'wp-hotel-booking' ); ?></span>
                    </label>
                </div>
                <div class="rating-number">
					<?php echo esc_html( wp_hotel_booking_get_count_rating( 'unrated' ) ); ?>
                </div>
            </li>
        </ul>
    </div>
<?php
