;(function($){
    $(document).ready(function(){
        $('.bulkactions').append( '<button type="button" class="button button-primary hb-update-ordering">Update Ordering</button>' );
        $(document).on('click', '.hb-update-ordering', function(){
           $(this.form).append('<input type="hidden" name="action" value="hb-update-ordering" />').submit();
        });

    });
})(jQuery);
