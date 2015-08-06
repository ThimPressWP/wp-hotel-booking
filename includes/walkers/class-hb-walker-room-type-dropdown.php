<?php
class HB_Walker_Room_Type_Dropdown extends Walker_CategoryDropdown {
    function start_el(&$output, $category, $depth, $args) {

        echo 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY';
        $pad = str_repeat('&nbsp;', $depth * 3);

        $cat_name = apply_filters('list_cats', $category->name, $category);
        $output .= "\t<option data-max-adults=\"".get_option('hb_taxonomy_capacity_'.$category->xxx)."\" class=\"level-$depth\" value=\"".$category->slug."\"";
        if ( $category->term_id == $args['selected'] )
            $output .= ' selected="selected"';
        $output .= '>';
        $output .= $pad.$cat_name;
        if ( $args['show_count'] )
            $output .= '&nbsp;&nbsp;('. $category->count .')';
        if ( $args['show_last_update'] ) {
            $format = 'Y-m-d';
            $output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
        }
        $output .= "</option>\n";
    }
}