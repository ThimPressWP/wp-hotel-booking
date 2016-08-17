;
( function ( $ ) {
    function parseJSON( data ) {
        if ( !$.isPlainObject( data ) ) {
            var m = data.match( /<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/ );
            try {
                if ( m ) {
                    data = $.parseJSON( m[1] );
                } else {
                    data = $.parseJSON( data );
                }
            } catch ( e ) {
                console.log( e );
                data = {};
            }
        }
        return data;
    }
    $( document ).ready( function () {
        $( '.bulkactions' ).append( '<button type="button" class="button button-primary hb-update-ordering">Update</button>' );
        $( document ).on( 'click', '.hb-update-ordering', function () {
            $( this.form ).append( '<input type="hidden" name="action" value="hb-update-taxonomy" />' ).submit();
        } );
    } );
} )( jQuery );
