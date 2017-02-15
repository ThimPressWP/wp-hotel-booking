<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 10:34:16
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 16:42:42
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

?>

<table class="row footer text-center" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <th class="width-30 columns first">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th>
                        <h3><?php printf( '%s', $options->get( 'hotel_name' ) ? $options->get( 'hotel_name' ) : get_bloginfo( 'name' ) ) ?></h3>
                    </th>
                </tr>
            </table>
        </th>
        <th class="width-30 columns">
            <table cellspacing="0" cellpadding="0">
				<?php $phone = $options->get( 'hotel_phone_number' ); ?>
				<?php if ( $phone ) : ?>
                    <tr>
                        <th>
                            <p><?php printf( __( 'Phone number %s', 'wp-hotel-booking' ), $phone ) ?></p>
                        </th>
                    </tr>
				<?php endif; ?>
                <tr>
                    <th>
                        <p><?php printf( __( 'Admin email at %s', 'wp-hotel-booking' ), $options->get( 'hotel_email_address' ) ? $options->get( 'hotel_email_address' ) : get_option( 'admin_email' ) ) ?></p>
                    </th>
                </tr>
            </table>
        </th>
        <th class="width-30 columns last">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th>
                        <p><?php printf( __( 'Country %s', 'wp-hotel-booking' ), $options->get( 'hotel_country' ) ) ?></p>
                        <p><?php printf( __( 'Address %s', 'wp-hotel-booking' ), $options->get( 'hotel_address' ) ) ?></p>
                        <p><?php printf( __( 'City %s', 'wp-hotel-booking' ), $options->get( 'hotel_city' ) ) ?></p>
                        <p><?php printf( __( 'State %s', 'wp-hotel-booking' ), $options->get( 'hotel_state' ) ) ?></p>
                    </th>
                </tr>
            </table>
        </th>
    </tr>
    </tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</table>
</body>
</html>