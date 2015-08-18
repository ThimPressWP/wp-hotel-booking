;(function($){
    var $doc = $(document);

    if( Date.prototype.compareWith == undefined ) {
        Date.prototype.compareWith = function( d ){
            if( typeof d == 'string' ){
                d = new Date( d );
            }
            var thisTime = parseInt( this.getTime() / 1000 ),
                compareTime = parseInt( d.getTime() / 1000 );
            if( thisTime > compareTime ){
                return 1;
            }else if( thisTime < compareTime ){
                return -1;
            }
            return 0;
        }
    }
    function isEmail( email ){
        return new RegExp( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$' ).test(email);
    }

    function isDate( date ){
        date = new Date( date );
        return !isNaN(date.getTime());
    }

    function create_pricing_plan(data){
        var $plan = $( wp.template('hb-pricing-table')(data) );
        return $plan;
    }

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
                    $cloned.hide().css("background-color", "#00A0D2").css("transition", "background-color 0.5s");
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
                    $cloned.fadeTo(350, 0.8).delay(1000).fadeTo(250, 1, function(){
                        $(this).css("background-color", "");
                        $('.dashicons-edit', this).trigger('click');
                    });
                    $('#hb-no-plan-message').hide();
                    break;
                case 'edit':
                    if( $button.hasClass('dashicons-edit') ){
                        $('input', $table).removeAttr('readonly');
                        $button.removeClass('dashicons-edit').addClass('dashicons-yes');
                        $('.hb-pricing-table .dashicons-yes').not($button).trigger('click')
                    }else{
                        $('input', $table).attr('readonly', 'readonly');
                        $button.removeClass('dashicons-yes').addClass('dashicons-edit');
                    }
                    break;
                case 'remove':
                    if( confirm( hotel_booking_l18n.confirm_remove_pricing_table ) ) {
                        if( $table.siblings('.hb-pricing-table').length == 0){
                            $('#hb-no-plan-message').show();
                        }
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
            var can_submit = true;
            $('.hb-pricing-table').each(function(i){
                var $table = $(this),
                    $start = $table.find('input[name^="date-start"]'),
                    $end = $table.find('input[name^="date-end"]');
                if(! $table.hasClass( 'regular-price')) {
                    if (!isDate($start.val())) {
                        alert(hotel_booking_l18n.empty_pricing_plan_start_date );
                        $start.focus();
                        can_submit = false;
                    } else if (!isDate($end.val())) {
                        alert(hotel_booking_l18n.empty_pricing_plan_start_date);
                        $end.focus();
                        can_submit = false;
                    }

                    if (!can_submit) return false;
                }
                $table.find('input[type="text"]').each(function(){
                    var $input = $(this),
                        name = $input.attr('name');
                    name = name.replace(/__INDEX__/, i - 1000);
                    $input.attr('name', name);
                })
            });
            return can_submit;
        });

        $('.hb-pricing-table').each(function(){
            init_pricing_plan(this);
        });

        var $tabClicked = $('.hb-payment-gateways .subsubsub li a').click(function(e){
            e.preventDefault();
            var id = $(this).attr('href'),
                $div = $(id),
                $parent = $(this).parent();
            $parent.addClass('current').siblings().removeClass('current');
            $div.show().css("opacity", 1).siblings('.hb-payment-gateway-settings').hide();

            history.pushState({}, '', window.location.href.replace(/#?.*/, '') + id);

            return false;
        }).filter('[href*="'+window.location.hash+'"]').trigger('click');


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

        $('.hb-add-new-plan').click(function(){
            var $plan = $(wp.template('hb-pricing-table')({}));
            $('#hb-pricing-plan-list').prepend($plan);
            init_pricing_plan( $plan );
            $plan.css("opacity", 0).css("background-color", "#00A0D2").css("transition", "background-color 0.5s");
            if( $(window).scrollTop() > $plan.offset().top - 100 ){
                $(window).scrollTop( $plan.offset().top - 100);
            }
            $plan.fadeTo(350, 0.8).delay(1000).fadeTo(250, 1, function(){
                $(this).css("background-color", "");
                $('a[data-action="edit"]', $plan).trigger('click');
            });
            $('#hb-no-plan-message').hide();

        });

        $('#hb-booking-date-from').datepicker({
            onSelect: function(){
                var date = jQuery(this).datepicker('getDate');

                $("#hb-booking-date-to").datepicker("option","minDate", date)
            }
        });
        $('#hb-booking-date-to').datepicker({
            onSelect: function(){
                var date = jQuery(this).datepicker('getDate');
                $("#hb-booking-date-from").datepicker("option","maxDate", date)
            }
        });

        $('form#posts-filter').submit(function(){
            var counter = 0;
            $('#hb-booking-date-from, #hb-booking-date-to, select[name="filter-type"]').each(function(){
                if( $(this).val() ) counter++;
            });
            if( counter > 0 && counter < 3 ){
                alert( hotel_booking_l18n.filter_error );
                return false;
            }
        });
    }

    $doc.ready( _ready );
})(jQuery);