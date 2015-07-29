<select name="<?php echo $field['name'];?>">
    <?php if( ! empty( $field['options'] ) ) foreach( $field['options'] as $option ){?>
    <?php $option = wp_parse_args( (array)$option, array( 'value' => '', 'text' => '' ) );?>
    <option value="<?php echo $option['value'];?>" <?php selected( ! empty( $field['std'] ) && $field['std'] == $option['value'] ? 1 : 0, 1);?>><?php echo $option['text'];?></option>
    <?php }?>
</select>
