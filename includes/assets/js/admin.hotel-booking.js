;(function($){
    var $doc = $(document);
    function init_pricing_plan( plan ){
        $(plan).find('.datepicker').datepicker({
            onSelect: function(date){

            }
        });
    }
    function _ready(){
        $doc.on('click', '.hb-pricing-controls a', function(e){
            var $button = $(this),
                $table = $button.closest('.hb-pricing-table'),
                action = $button.data('action');
            e.preventDefault();

            switch( action ){
                case 'clone':
                    var $cloned = $(wp.template('hb-pricing-table')({})),
                        $inputs = $cloned.find('.hb-pricing-price');
                    $cloned.hide().css("background-color", "#FF0000").css("transition", "background-color 0.5s");
                    init_pricing_plan( $cloned );
                    $table.find('.hb-pricing-price').each(function(i){
                        $inputs.eq(i).val(this.value);
                    });
                    if( $table.hasClass('regular-price') ) {
                        $cloned.removeClass('regular-price')
                        $('.hb-pricing-table-title > span', $cloned).html('Date Range');
                        $('#hb-pricing-plan-list').append($cloned);
                    }else{
                        $cloned.insertAfter($table);
                    }
                    $cloned.fadeTo(350, 0.8).delay(1000).fadeTo(250, 1, function(){$(this).css("background-color", "");});
                    break;
                case 'remove':
                    if( confirm( hotel_booking_l18n.confirm_remove_pricing_table ) ) {
                        $table.remove();
                    }
                    break;
            }
        });

        $('#tp_hotel_booking_pricing #hb-room-types').change(function(){
            var location = window.location.href;
            location = location.replace(/[&]?hb-room-type=[0-9]+/, '');
            if( this.value != 0 ) location += '&hb-room-type='+this.value;
            window.location.href = location;
        });

        $('form[name="pricing-table-form"]').submit(function(){
            $('.hb-pricing-table').each(function(i){
                var $table = $(this),
                    $start = $table.find('input[name^="date-start"]'),
                    $end = $table.find('input[name^="date-end"]');
                $table.find('input[type="text"]').each(function(){
                    var $input = $(this),
                        name = $input.attr('name');
                    name = name.replace(/__INDEX__/, i - 1000);
                    $input.attr('name', name);
                })
            })
        });

        $('.hb-pricing-table').each(function(){
            init_pricing_plan(this);
        })

        $.datepicker.setDefaults({ dateFormat: 'mm/dd/yy'});
        $(".datetime-picker-metabox").datepicker({
            minDate: 0,
            maxDate: "+365D",
            numberOfMonths: 2,
            onSelect: function(selected) {
                var date = jQuery(this).datepicker('getDate');
                if(date){
                    date.setDate(date.getDate() + 1);
                }
                // $("#check_out_date").datepicker("option","minDate", date)
            }
        });
        $("#datepickerImage").click(function() {
            $("#txtFromDate").datepicker("show");
        });
        // $("#datepickerImage1").click(function() {
        //     $("#txtToDate").datepicker("show");
        // });
    }

    $doc.ready( _ready );
})(jQuery);