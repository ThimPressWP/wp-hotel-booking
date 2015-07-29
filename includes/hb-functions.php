<?php
function hb_get_room_types( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'taxonomy'      => 'hb_room_type',
            'hide_empty'    => 0,
            'map_fields'    => null
        )
    );
    $terms = (array) get_terms( "hb_room_type", $args );
    if( is_array( $args['map_fields' ] ) ){
        foreach( $terms as $term ){
            $type = new stdClass();
            foreach( $args['map_fields'] as $from => $to ){
                if( ! empty( $term->{$from} ) ){
                    $type->{$to} = $term->{$from};
                }else{
                    $type->{$to} = null;
                }
            }
            $types[] = $type;
        }
    }else{
        $types = $terms;
    }
    return $types;
}

function hb_get_room_capacities( $args = array() ){
    $args = wp_parse_args(
        $args,
        array(
            'taxonomy'      => 'hb_room_capacity',
            'hide_empty'    => 0,
            'map_fields'    => null
        )
    );
    $terms = (array) get_terms( "hb_room_capacity", $args );
    if( is_array( $args['map_fields' ] ) ){
        foreach( $terms as $term ){
            $type = new stdClass();
            foreach( $args['map_fields'] as $from => $to ){
                if( ! empty( $term->{$from} ) ){
                    $type->{$to} = $term->{$from};
                }else{
                    $type->{$to} = null;
                }
            }
            $types[] = $type;
        }
    }else{
        $types = $terms;
    }
    return $types;
}