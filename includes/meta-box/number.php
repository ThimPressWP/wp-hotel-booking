<?php
$field = wp_parse_args(
    $field,
    array(
        'id'    => '',
        'name'  => '',
        'std'   => '',
        'step'  => '',
        'min'   => '',
        'max'   => '',
        'placeholder' => ''
    )
);
printf(
    '<input type="number" name="%s" id="%s" value="%s" step="%s" min="%s" max="%s" placeholder="%s"  />',
    $field['name'],
    $field['id'],
    $field['std'],
    $field['step'],
    $field['min'],
    $field['max'],
    $field['placeholder']
);