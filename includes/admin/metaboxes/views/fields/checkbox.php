<?php

/**
 * Admin View: Admin meta box checkbox field.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
$field = wp_parse_args(
	$field,
	array(
		'id'     => '',
		'name'   => '',
		'std'    => '',
		'attr'   => '',
		'filter' => null,
	)
);

$field_attr = '';
if ( $field['attr'] ) {
	if ( is_array( $field['attr'] ) ) {
		$field_attr = join( ' ', $field['attr'] );
	} else {
		$field_attr = $field['attr'];
	}
}

$value = $field['std'];
if ( is_callable( $field['filter'] ) ) {
	$value = call_user_func_array( $field['filter'], array( $value ) );
}
?>

<input type="hidden" name="<?php echo $field['name']; ?>" value="0" />
<input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo $field_attr; ?><?php checked( $value, 1 ); ?>/>
