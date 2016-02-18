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
            dateFormat      : hotel_booking_l18n.date_time_format,
            monthNames      : hotel_booking_l18n.monthNames,
            monthNamesShort : hotel_booking_l18n.monthNamesShort,
            dayNames        : hotel_booking_l18n.dayNames,
            dayNamesShort   : hotel_booking_l18n.dayNamesShort,
            dayNamesMin     : hotel_booking_l18n.dayNamesMin,
            onSelect: function( date ){
                var _self = $( this ),
                    _date = _self.datepicker( 'getDate' ),
                    _timestamp = new Date( _date ).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 ),
                    name = _self.attr( 'name' );
                var hidden_name = false;
                if ( name.indexOf( 'date-start' ) === 0 ) {
                    hidden_name = name.replace( 'date-start', 'date-start-timestamp' );
                } else if( name.indexOf( 'date-end' ) === 0 ) {
                    hidden_name = name.replace( 'date-end', 'date-end-timestamp' );
                }
                if ( hidden_name ) {
                    $(plan).find( 'input[name="'+hidden_name+'"]' ).val( _timestamp );
                }
            }
        });
        // $(plan).find('.datepicker').datepicker('disable');
    }

    function _ready(){
        $doc.on('click', '.hb-pricing-controls a', function(e){
            var $button = $(this),
                $table = $button.closest('.hb-pricing-table'),
                action = $button.data('action');
            e.preventDefault();

            switch( action ){
                case 'clone':
                    var clone_allow = false;
                    if( $('.hb-pricing-table').length < 1 )
                    {
                        clone_allow = true;
                    }
                    var $cloned = $( wp.template('hb-pricing-table')({ clone: clone_allow }) ),
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
                        $('input', $table).datepicker("enable");
                        $button.removeClass('dashicons-edit').addClass('dashicons-yes');
                        $('.hb-pricing-table .dashicons-yes').not($button).trigger('click')
                    }else{
                        $('input', $table).attr('readonly', 'readonly');
                        $('input', $table).datepicker("disable");
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
            return false;
        });

        $('#tp_hotel_booking_pricing #hb-room-select').change(function(){
            var location = window.location.href;
            location = location.replace(/[&]?hb-room=[0-9]+/, '');
            if( this.value != 0 ) location += '&hb-room='+this.value;
            window.location.href = location;
        });

        $('form[name="pricing-table-form"]').submit(function(){
            var can_submit = true;
            $('.hb-pricing-table').each(function(i){
                var $table = $(this),
                    $start = $table.find('input[name^="date-start"]'),
                    $end = $table.find('input[name^="date-end"]');
                if(! $table.hasClass( 'regular-price')) {
                    if ( ! isDate( $start.datepicker( 'getDate' ) ) ) {
                        alert(hotel_booking_l18n.empty_pricing_plan_start_date );
                        $start.focus();
                        can_submit = false;
                    } else if ( ! isDate( $end.datepicker( 'getDate' ) ) ) {
                        alert(hotel_booking_l18n.empty_pricing_plan_start_date);
                        $end.focus();
                        can_submit = false;
                    }

                    if ( ! can_submit ) return false;
                }
                $table.find('input[type="text"], input[type="hidden"]').each(function(){
                    var $input = $(this),
                        name = $input.attr('name');
                    name = name.replace(/__INDEX__/, i - 1000);
                    $input.attr('name', name);
                });
            });
            return can_submit;
        });

        $('.hb-pricing-table').each(function(){
            init_pricing_plan(this);
        });

        var $tabClicked = $('.hb-admin-sub-tab li a').click(function(e){
            e.preventDefault();
            var id = $(this).attr('href'),
                $div = $(id),
                $parent = $(this).parent();
            $parent.addClass('current').siblings().removeClass('current');

            $div.show().css('opacity', 1).siblings('.hb-sub-tab-content').hide();

            history.pushState({}, '', window.location.href.replace(/#?.*/, '') + id);

            return false;
        }).filter('[href*="'+window.location.hash+'"]').trigger('click');

        // $.datepicker.setDefaults({ dateFormat: hotel_booking_l18n.date_time_format });
        // $.datepicker.setDefaults({ dateFormat: 'mm/dd/yy' });
        $(".datetime-picker-metabox").datepicker({
            dateFormat      : hotel_booking_l18n.date_time_format,
            monthNames      : hotel_booking_l18n.monthNames,
            monthNamesShort : hotel_booking_l18n.monthNamesShort,
            dayNames        : hotel_booking_l18n.dayNames,
            dayNamesShort   : hotel_booking_l18n.dayNamesShort,
            dayNamesMin     : hotel_booking_l18n.dayNamesMin,
            minDate         : 0,
            maxDate         : '+365D',
            numberOfMonths  : 2,
            onSelect: function(selected) {
                var _self = $(this),
                    name = _self.attr('name'),
                    date = jQuery(this).datepicker('getDate'),
                    timestamp = new Date( date ).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                if( date ){
                    date.setDate(date.getDate() + 1);
                }
                $( 'input[name="'+name+'_timestamp"]' ).val( timestamp );
                // $("#check_out_date").datepicker("option","minDate", date)
            }
        });
        $("#datepickerImage").click(function() {
            $("#txtFromDate").datepicker("show");
        });

        $('.hb-add-new-plan').click(function(){
            if( $('.hb-pricing-table').length >= 2 )
                return;
            var $plan = $(wp.template('hb-pricing-table')({}));
            $('#hb-pricing-plan-list').prepend($plan);
            init_pricing_plan( $plan );
            $plan.css('opacity', 0).css("background-color", "#00A0D2").css("transition", "background-color 0.5s");
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
            dateFormat      : hotel_booking_l18n.date_time_format,
            monthNames      : hotel_booking_l18n.monthNames,
            monthNamesShort : hotel_booking_l18n.monthNamesShort,
            dayNames        : hotel_booking_l18n.dayNames,
            dayNamesShort   : hotel_booking_l18n.dayNamesShort,
            dayNamesMin     : hotel_booking_l18n.dayNamesMin,
            onSelect: function(){
                var _self = $(this),
                    date = _self.datepicker( 'getDate' ),
                    timestamp = new Date( date ).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                _self.parent().find('input[name="date-from-timestamp"]').val( timestamp );
                $('#hb-booking-date-to').datepicker('option','minDate', date)
            }
        });
        $('#hb-booking-date-to').datepicker({
            dateFormat      : hotel_booking_l18n.date_time_format,
            monthNames      : hotel_booking_l18n.monthNames,
            monthNamesShort : hotel_booking_l18n.monthNamesShort,
            dayNames        : hotel_booking_l18n.dayNames,
            dayNamesShort   : hotel_booking_l18n.dayNamesShort,
            dayNamesMin     : hotel_booking_l18n.dayNamesMin,
            onSelect: function(){
                var _self = $(this),
                    date = _self.datepicker( 'getDate' ),
                    timestamp = new Date( date ).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
                _self.parent().find('input[name="date-from-timestamp"]').val( timestamp );
                $('#hb-booking-date-from').datepicker('option', 'maxDate', date)
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

        $('#gallery_settings').on('click', '.attachment.add-new', function(event){
            event.preventDefault();
            var fileFrame = wp.media.frames.file_frame = wp.media({
                multiple : true
            });
            var self = $(this);
            fileFrame.on('select', function() {
                var attachments = fileFrame.state().get('selection').toJSON();
                var html = '';

                for( var i = 0; i < attachments.length; i++ )
                {
                    var attachment = attachments[i];
                    var url = attachment.url.replace( hotel_settings.upload_base_url, '' );
                    html += '<li class="attachment">';
                        html += '<div class="attachment-preview">';
                        html +=     '<div class="thumbnail">';
                        html +=         '<div class="centered">'
                                            html += '<img src="'+attachment.url+'"/>';
                                            html += '<input type="hidden" name="_hb_gallery[]" value="'+attachment.id+'" />'
                        html +=         '</div>';
                        html +=     '</div>';
                        html += '</div>';
                        html += '<a class="dashicons dashicons-trash" title="Remove this image"></a>';
                    html += '</li>';
                }
                self.before(html);
            });
            fileFrame.open();
        })
        .on('click', '.attachment .dashicons-trash', function(event){
            event.preventDefault();
            $(this).parent().remove();
        });

        $('form[name="hb-admin-settings-form"] select').select2();

        $('input[name="tp_hotel_booking_email_new_booking_enable"]').on('change _change', function(){
            var $siblings = $(this).closest('tr').siblings('.' + $(this).attr('name'));
            if( this.checked ){
                $siblings.show();
            } else {
                $siblings.hide();
            }
        }).trigger('change');
        $('#gallery_settings ul').sortable();

        /**
         * other settings tabs
         */
        $('.tp_hotel_booking_tabs_settings li a:first').addClass('active');
        $('.tp_hotel_booking_setting_fields:first').addClass('active');
        $(document).on('click', '.tp_hotel_booking_tabs_settings li a', function(e){
            e.preventDefault();
            var self = $(this),
                tab = self.attr( 'href' ),
                tab_content = $(tab);

            if( typeof tab === 'undefined' )
                return;

            $('.tp_hotel_booking_setting_fields').removeClass('active');
            $('.tp_hotel_booking_tabs_settings li a').removeClass('active');
            self.addClass('active');

            if( tab_content.length === 1 )
                tab_content.addClass( 'active' );

        });
    }

    $doc.ready( _ready );
})(jQuery);