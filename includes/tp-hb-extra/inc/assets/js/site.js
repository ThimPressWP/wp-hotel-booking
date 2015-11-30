(function($){

	TPHB_Extra_Site = {

		init: function()
		{
			// toggle extra field optional
			this.toggle_extra();
			// toggle input number when checked checkbox and process price
			this.toggleCheckbox();
			// remove package cart
			this.removePackage();

		},

		parseJSON: function(data){
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
	    },

		toggle_extra: function()
		{

			$(document).on( 'click', '.hb_package_toggle', function( e ){
				e.preventDefault();

				var _self = $(this),
					parent = _self.parents( '.hb_addition_package_extra' );
					toggle = parent.find( '.hb_addition_packages' );

				_self.toggleClass('active');
				toggle.toggleClass('active');

				if( toggle.hasClass('active') )
					toggle.slideDown();
				else
					toggle.slideUp();
			});
		},

		toggleCheckbox: function()
		{
			$(document).on( 'change', '.hb_optional_quantity_selected', function( e ){
				e.preventDefault();
				var _self = $(this),
					parent = _self.parents( 'li:first' ),
					inputQuantity = parent.find( '.hb_optional_quantity' );

				if( _self.is(':checked') )
				{
					inputQuantity.attr('readonly', true);
				}
				else
				{
					if( ! inputQuantity.hasClass( 'tp_hb_readonly' ) )
						inputQuantity.removeAttr('readonly');
				}
			});
		},

		removePackage: function()
		{
			$(document).on( 'click', '.hb_package_remove', function(e){
				e.preventDefault();
				var _self = $(this),
					package_id = _self.attr( 'data-package' ),
					_parents = _self.parents('.hb_mini_cart_item:first'),
					room_id = _parents.attr( 'data-id' ),
					time_key = _parents.attr( 'data-search-key' );
				$.ajax({
					url: hotel_settings.ajax,
					method: 'POST',
					data: {
						action: 'tp_hotel_booking_remove_package',
						room_id: room_id,
						package_id: package_id,
						time_key: time_key
					},
					dataType: 'html',
					beforeSend: function(){

					}
				}).done( function( res ){
					res = TPHB_Extra_Site.parseJSON(res);
					if( typeof res.status !== 'undefined' && res.status == 'success' )
					{
						HB_Booking_Cart.hb_add_to_cart_callback( res, function(){
							var cart_table = $('#hotel-booking-payment, #hotel-booking-cart');

				            for( var i = 0; i < cart_table.length; i++ )
				            {
				                var _table = $(cart_table[i]);
				                var tr = _table.find('table').find('.hb_checkout_item');
				                for ( var y = 0; y < tr.length; y++ )
				                {
				                    var _tr = $(tr[y]);
				                    var _date = _tr.attr( 'data-date' );
				                    var _roomID = _tr.attr('data-id');
				                    if( _date === res.search_key && _roomID === res.id )
				                    {
				                        if( typeof res.item_total !== 'undefined' )
				                    		_tr.find('.hb_gross_total').html( res.item_total );
				                        break;
				                    }
				                }

				                if( typeof res.sub_total !== 'undefined' )
				                    _table.find('span.hb_sub_total_value').html( res.sub_total );

				                if( typeof res.grand_total !== 'undefined' )
				                    _table.find('span.hb_grand_total_value').html( res.grand_total );

				                if( typeof res.advance_payment !== 'undefined' )
				                    _table.find('span.hb_advance_payment_value').html( res.advance_payment );

				            }
						});
					}
				} );
			});
		},

	};

	$(document).ready(function(){
		TPHB_Extra_Site.init();
	});

})(jQuery);