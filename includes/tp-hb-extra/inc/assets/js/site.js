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
					beforeSend: function(){

					}
				}).done( function( res ){
					if( typeof res.status !== 'undefined' && res.status == 'success' )
						HB_Booking_Cart.hb_add_to_cart_callback( res );
				} );
			});
		}

	};

	$(document).ready(function(){
		TPHB_Extra_Site.init();
	});

})(jQuery);