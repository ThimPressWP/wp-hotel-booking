;(function ($) {
	function _ready() {
		$('form#hb-payment-form').on('hotel_booking_place_order', function () {
			var $form = $(this),
				method = $form.find('input[name="hb-payment-method"]:checked').val();
			if (method.match(/^wc_/)) {
				$form.find('input[name="action"]').remove();
				$.ajax({
					url    : hotel_settings.ajax + '?action=hb_wc_checkout',
					data   : $form.serialize(),
					success: function () {

					}
				});
			}
			return false;
		});

		$('body').on('hb_added_item_to_cart', function () {
			$('body').trigger('wc_fragment_refresh');
		})
			.on('hb_removed_item_to_cart', function () {
				$('body').trigger('wc_fragment_refresh');
			});
	}

	$(document).ready(_ready);

})(jQuery);