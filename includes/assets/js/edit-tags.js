;(function($){
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


    });
})(jQuery);
