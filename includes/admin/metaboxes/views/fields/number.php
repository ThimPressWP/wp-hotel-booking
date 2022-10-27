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
		'step'        => '',
		'min'         => '',
		'max'         => '',
		'placeholder' => '',
		'attr'        => '',
		'filter'      => false,
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
	'<input type="number" name="%s" class="%s" value="%s" step="%s" min="%s" max="%s" placeholder="%s" %s />',
	esc_attr( $field['name'] ),
	esc_attr( $field['id'] ),
	esc_attr( $value ),
	esc_attr( $field['step'] ),
	esc_attr( $field['min'] ),
	esc_attr( $field['max'] ),
	esc_attr( $field['placeholder'] ),
	$field_attr
);
