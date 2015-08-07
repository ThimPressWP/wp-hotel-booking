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
printf('<input type="text" class="datetime-picker-metabox" id="%s" name="%s" value="%s" />',
		$field['id'],
		$field['name'],
		$field['std']
	);