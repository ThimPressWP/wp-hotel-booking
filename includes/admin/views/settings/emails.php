<?php
/**
 * Template for displaying email settings
 *
 * @since 0.9.1
 */
$settings = hb_settings();

// put to filter later
$email_options = apply_filters(
    'hb_email_settings',
    array(
        'general'       => __( 'Email Options', 'tp-hotel-booking' ),
        'new_booking'   => __( 'New Booking', 'tp-hotel-booking' )
    )
);
?>
<div class="hb-payment-gateways">
    <?php if( $count = sizeof( $email_options ) ):?>
        <?php $i = 0; ?>
        <ul class="hb-admin-sub-tab subsubsub">
            <?php foreach( $email_options as $slug => $name ){ ?>
                <li<?php echo $i++ == 0 ? ' class="current"' : ''; ?>>
                    <a href="#hb-email-<?php echo $slug; ?>-settings"><?php echo $name; ?></a>
                </li>
                <?php echo $i < $count ? '&nbsp;|&nbsp;' : ''; ?>
            <?php } ?>
        </ul>
        <div class="clearfix"></div>
        <?php $i = 0; foreach( $email_options as $slug => $name ){?>
            <div id="hb-email-<?php echo $slug; ?>-settings" class="hb-sub-tab-content hb-email-settings<?php echo $i++ == 0 ? ' current' : ''; ?>">
                <?php do_action( 'hb_email_' . $slug . '_settings' ); ?>
            </div>
        <?php } ?>
    <?php endif; ?>
</div>
</table>