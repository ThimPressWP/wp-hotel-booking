<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field      = wp_parse_args(
	$field,
	array(
		'id'          => '',
		'name'        => '',
		'std'         => '',
		'placeholder' => '',
		'attr'        => '',
		'filter'      => null,
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

printf(
	'<input class="regular-text" type="text" name="%s" id="%s" value="%s" placeholder="%s" %s />',
	esc_attr( $field['name'] ),
	esc_attr( $field['id'] ),
	esc_attr( $value ),
	esc_attr( $field['placeholder'] ),
	$field_attr
);
