<?php
$settings = hb_settings();
$payment_gateways = hb_get_payment_gateways();
?>
<div class="hb-payment-gateways">
    <?php if( $count = sizeof( $payment_gateways ) ):?>
        <?php $i = 0;?>
    <ul class="subsubsub">
        <?php foreach( $payment_gateways as $gateway ){ ?>
        <li<?php echo $i++ == 0 ? ' class="current"' : '';?>>
            <a href="#hb-payment-gateway-<?php echo $gateway->slug;?>"><?php echo $gateway->title;?></a>
        </li>
            <?php echo $i < $count ? '&nbsp;|&nbsp;' : '';?>
        <?php }?>
    </ul>
    <?php $i = 0; foreach( $payment_gateways as $gateway ){?>
    <div id="hb-payment-gateway-<?php echo $gateway->slug;?>" class="hb-payment-gateway-settings<?php echo $i++ == 0 ? ' current' : '';?>">
        <?php do_action( 'hb_payment_gateway_settings_' . $gateway->slug );?>
    </div>
    <?php }?>
    <?php endif;?>
</div>