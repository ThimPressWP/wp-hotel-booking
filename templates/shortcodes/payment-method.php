<?php
$payment_gateways = hb_get_payment_gateways( array( 'enable' => true ) );
?>
<div class="hb-payment-form">
    <div class="hb-col-padding hb-col-border">
    <h4><?php _e( 'Payment Method', 'tp-hotel-booking' ); ?></h4>
    <ul class="hb-payment-methods">
        <?php foreach( $payment_gateways as $gateway ){?>
            <li>
                <label>
                    <input type="radio" name="hb-payment-method" value="<?php echo $gateway->slug; ?>" />
                    <?php echo $gateway->title; ?>
                </label>
                <?php if( has_action( 'hb_payment_gateway_form_' . $gateway->slug ) ){ ?>
                    <div class="hb-payment-method-form <?php echo $gateway->slug; ?>">
                        <?php do_action( 'hb_payment_gateway_form_' . $gateway->slug ); ?>
                    </div>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    </div>
</div>