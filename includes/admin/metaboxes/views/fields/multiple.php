<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

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
$value = $field['std'];
if ( is_callable( $field['filter'] ) ) {
	$value = call_user_func_array( $field['filter'], array( $value ) );
}

$name = $field['name'] . '[]';

$field_attr = '';
if ( $field['attr'] ) {
	if ( is_array( $field['attr'] ) ) {
		$field_attr = join( " ", $field['attr'] );
	} else {
		$field_attr = $field['attr'];
	}
}
?>
<select name="<?php echo esc_attr( $name ); ?>" multiple="multiple" <?php printf( '%s', $field_attr ) ?>>
	<?php if ( ! empty( $field['options'] ) ) {
		foreach ( $field['options'] as $k => $option ) { ?>
			<?php
			if ( ! is_object( $option ) && ! is_array( $option ) ) {
				$option = array(
					'value' => $k,
					'text'  => $option
				);
			} else {
				$option = wp_parse_args( (array) $option, array( 'value' => '', 'text' => '' ) );
			}
			?>
			<?php if ( ! ( $value ) ) {
				?>
                <option value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['text'] ); ?></option>
			<?php } else {
				?>
                <option value="<?php echo esc_attr( $option['value'] ); ?>"<?php echo is_array( $value ) && in_array( $option['value'], $value ) ? ' selected' : '' ?>><?php echo esc_html( $option['text'] ); ?></option>
			<?php } ?>

		<?php }
	} ?>
</select>
