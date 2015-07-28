<?php
$field = wp_parse_args(
    $field,
    array(
        'id'            => '',
        'name'          => '',
        'std'           => '',
        'placeholder'   => ''
    )
);
printf(
    '<input type="text" name="%s" id="%s" value="%s" placeholder="%s"  />',
    $field['name'],
    $field['id'],
    $field['std'],
    $field['placeholder']
);