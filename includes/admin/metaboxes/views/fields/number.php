<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$field = wp_parse_args(
    $field,
    array(
        'id'    => '',
        'name'  => '',
        'std'   => '',
        'step'  => '',
        'min'   => '',
        'max'   => '',
        'placeholder' => '',
        'attr'          => '',
        'filter'    => false
    )
);
$field_attr = '';
if( $field['attr'] ){
    if( is_array( $field['attr'] ) ){
        $field_attr = join( " ", $field['attr'] );
    }else{
        $field_attr = $field['attr'];
    }
}
$value = $field['std'];
if( is_callable( $field['filter'] ) ){
    $value = call_user_func_array( $field['filter'], array( $value ) );
}

printf(
    '<input type="number" name="%s" id="%s" value="%s" step="%s" min="%s" max="%s" placeholder="%s" %s />',
    $field['name'],
    $field['id'],
    $value,
    $field['step'],
    $field['min'],
    $field['max'],
    $field['placeholder'],
    $field_attr
);