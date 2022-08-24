<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$field = wp_parse_args(
    $field,
    array(
        'id'            => '',
        'name'          => '',
        'std'           => '',
        'placeholder'   => '',
        'editor'        => false,
    )
);

if ( $field['editor'] ) {
    wp_editor($field['std'], $field['name'] );
} else {
    printf(
        '<textarea name="%s" id="%s" placeholder="%s">%s</textarea>',
        esc_attr( $field['name'] ),
        esc_attr( $field['id'] ),
        esc_attr( $field['placeholder'] ),
        esc_textarea( $field['std'] )
    );
}