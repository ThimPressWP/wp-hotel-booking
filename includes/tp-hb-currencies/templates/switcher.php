<?php $tp_currencies = hb_payment_currencies(); ?>
<?php $storage = HB_SW_Curreny_Storage::instance(); ?>

<form method="POST" class="hb_form_currencies_switcher">
	<?php wp_nonce_field( 'hb_sw_currencies', 'hb_sw_currencies' ); ?>
	<select name="hb_form_currencies_switcher_select" class="hb_form_currencies_switcher_select">
		<?php foreach( $currencies as $currency ): ?>
			<?php if( array_key_exists( $currency, $tp_currencies ) ): ?>
				<option value="<?php echo esc_attr( $currency ) ?>" <?php selected( $storage->get( 'currency' ), $currency ) ?>><?php printf( '%s', $tp_currencies[ $currency ] ) ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
</form>
