<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$payment_gateways = hb_get_payment_gateways( array( 'enable' => true ) );
?>
<div class="hb-payment-form">
    <div class="hb-col-padding hb-col-border">
        <h4><?php _e( 'Payment Method', 'wp-hotel-booking' ); ?></h4>
        <ul class="hb-payment-methods">
			<?php $i = 0; ?>
			<?php foreach ( $payment_gateways as $gateway ) : ?>
                <li>
                    <label>
                        <input type="radio" name="hb-payment-method" value="<?php echo esc_attr( $gateway->slug ); ?>"<?php echo ( $i === 0 ) ? ' checked' : '' ?>/>
						<?php echo esc_html( $gateway->title ); ?>
                    </label>
					<?php if ( has_action( 'hb_payment_gateway_form_' . $gateway->slug ) ) { ?>
                        <div class="hb-payment-method-form <?php echo esc_attr( $gateway->slug ); ?>">
							<?php do_action( 'hb_payment_gateway_form_' . $gateway->slug ); ?>
                        </div>
					<?php } ?>
                </li>
				<?php $i ++; endforeach; ?>
        </ul>
    </div>
</div>
