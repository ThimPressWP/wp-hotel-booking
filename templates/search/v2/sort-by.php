<?php
if ( ! isset( $data ) ) {
	return;
}

$sort_options  = array(
	'date-desc'  => esc_html__( 'Default (Newest)', 'wp-hotel-booking' ),
	'date-asc'   => esc_html__( 'Oldest', 'wp-hotel-booking' ),
	'title-asc'  => esc_html__( 'A to Z', 'wp-hotel-booking' ),
	'title-desc' => esc_html__( 'Z to A', 'wp-hotel-booking' ),
);
$default_sort  = 'date-desc';
$sort_by_value = hb_get_request( 'sort_by' );
do_action( 'wphb/sort-by/wrapper/before' );
?>
	<div class="<?php echo esc_attr( apply_filters( 'wphb/filter/sort-by-wrapper', 'sort-by-wrapper' ) ); ?>">
		<?php
		do_action( 'wphb/sort-by/content/before' );
		?>
		<div class="show-number">
			<?php
			if ( isset( $data['show_number'] ) ) {
				echo esc_html( $data['show_number'] );
			}
			?>
		</div>

		<div class="sort-by">
			<span class="sort-by__label"><?php esc_html_e( 'Sort By :', 'wp-hotel-booking' ); ?></span>
			<div class="select">
				<div class="toggle">
					<?php
					if ( isset( $sort_options[ $sort_by_value ] ) ) {
						echo esc_html( $sort_options[ $sort_by_value ] );
					} else {
						echo esc_html( $sort_options[ $default_sort ] );
					}
					?>
				</div>
				<ul class="sort-by__list">
					<?php
					foreach ( $sort_options as $key => $label ) {
						$class = 'sort-by__list-item';
						if ( empty( $sort_by_value ) ) {
							if ( $key === $default_sort ) {
								$class .= ' active';
							}
						} elseif ( $key === $sort_by_value ) {
							$class .= ' active';
						}
						?>
						<li class="<?php echo esc_attr( $class ); ?>" data-value="<?php echo esc_attr( $key ); ?>">
							<?php echo esc_html( $label ); ?>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<?php
		do_action( 'wphb/sort-by/content/after' );
		?>
	</div>
<?php
do_action( 'wphb/sort-by/wrapper/after' );
