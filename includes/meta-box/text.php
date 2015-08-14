<?php
$field = wp_parse_args(
    $field,
    array(
        'id'            => '',
        'name'          => '',
        'std'           => '',
        'placeholder'   => '',
        'attr'          => ''
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
printf(
    '<input class="regular-text" type="text" name="%s" id="%s" value="%s" placeholder="%s" %s />',
    $field['name'],
    $field['id'],
    $field['std'],
    $field['placeholder'],
    $field_attr
);