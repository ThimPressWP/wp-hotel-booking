;(function($){
    function parseJSON(data){
        if( ! $.isPlainObject(data) ){
            var m = data.match(/<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/);
            try {
                if (m) {
                    data = $.parseJSON(m[1]);
                } else {
                    data = $.parseJSON(data);
                }
            }catch(e){
                console.log(e);
                data = {};
            }
        }
        return data;
    }
    $(document).ready(function(){
        $('.bulkactions').append( '<button type="button" class="button button-primary hb-update-ordering">Update</button>' );
        $(document).on('click', '.hb-update-ordering', function(){
           $(this.form).append('<input type="hidden" name="action" value="hb-update-taxonomy" />').submit();
        }).on('click', '.hb-taxonomy-thumbnail-selector', function(){
            var $holder = $(this);
            if( $holder.hasClass('has-attachment') ){
                $holder
                    .removeClass('has-attachment')
                    .find('img').remove();
                $holder.find('input[type="hidden"]')
                    .val('0');
            }else {
                mediaSelector.open({
                    multiple: false,
                    onSelect: function (source) {
                        //alert(JSON.stringify(source))
                        $holder
                            .addClass('has-attachment')
                            .append('<img src="' + source.sizes.thumbnail.url + '" />')
                            .find('input[type="hidden"]').val(source.id);
                    }
                });
            }
        });

        $(document).on('click', '.hb-edit-room-gallery', function(e){
            e.preventDefault();
            var $link = $(this),
                $tr = $link.closest('tr'),
                gallery_id = $tr.attr('id').replace(/tag-/, '');
                $gallery = $('#room-gallery-'+gallery_id);
            $('.room-gallery').fadeOut();
            if( $gallery.length == 0 ) {

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'html',
                    data:{
                        action: 'hotel_booking_load_room_type_galley',
                        term_id: gallery_id
                    },
                    success: function(response){
                        response = parseJSON( response );
                        $gallery = $(wp.template('room-type-gallery')({
                            id: gallery_id,
                            colspan: $tr.children().length,
                            gallery: response
                        }));
                        $gallery
                            .fadeOut()
                            .insertAfter($tr);
                        $gallery.fadeIn(function(){
                            $('ul', this).sortable({
                                items: '.attachment:not(.add-new)'
                            });
                        });
                    }
                });
            }else{
                $gallery.fadeIn();
            }

        }).on('click', '.attachment.add-new', function(){
            var $button = $(this),
                gallery_id = $button.closest('tr').attr('id').replace(/room-gallery-/, '');
            mediaSelector.open({
                multiple: true,
                onSelect: function (source) {
                    //alert(JSON.stringify(source))
                    _.each(source, function(attachment){
                        var $attachment = $(wp.template('room-type-attachment')({gallery_id: gallery_id,src: attachment.sizes.thumbnail.url, id: attachment.id}));
                        $attachment.insertBefore($button);
                    })
                    return;
                    $holder
                        .addClass('has-attachment')
                        .append('<img src="' + source.sizes.thumbnail.url + '" />')
                        .find('input[type="hidden"]').val(source.id);
                }
            });
        }).on('click', '.attachment .dashicons-trash', function(){
            $(this).parent().remove();
        });
        $('.hb-room-gallery > ul').sortable();
    });
})(jQuery);
