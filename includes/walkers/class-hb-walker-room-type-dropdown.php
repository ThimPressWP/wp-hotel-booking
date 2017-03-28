<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WPHB_Walker_Room_Type_Dropdown extends Walker_CategoryDropdown {
    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
        $pad = str_repeat('&nbsp;', $depth * 3);
        $adults = get_term_meta( $category->xxx, 'hb_max_number_of_adults', true );
        if ( ! $adults ) {
            $adults = get_option('hb_taxonomy_capacity_'.$category->xxx);
        }

        $cat_name = apply_filters('list_cats', $category->name, $category);
        $output .= "\t<option data-max-adults=\"". $adults ."\" class=\"level-$depth\" value=\"".$category->slug."\"";
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
