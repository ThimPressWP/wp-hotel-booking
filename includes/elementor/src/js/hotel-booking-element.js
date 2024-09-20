const jQuerydynamicGallery = document.getElementById("hb-room-thumbnail");
const jQuerydynamicgal = document.querySelector('.dynamic-gal');
if (jQuerydynamicgal) {
	const data = jQuerydynamicgal.getAttribute('data-dynamicpath');
	const objs = JSON.parse(data);
	const modifiedData = objs.map(obj => { 
		return {
			src: obj.src,
			responsive: obj.responsive,
			thumb: obj.thumb,
			subHtml: obj.subHtml
		};
	});
	const dynamicGallery = window.lightGallery(jQuerydynamicGallery, {  
		dynamic: true,
		plugins: [lgThumbnail],
		dynamicEl: modifiedData,
		height: "80%",
		width: "65%",
		addClass: 'fixed-size',
		download: false,
		counter: true,
		// thumbWidth: "65%",
		// enableThumbSwipe:true,
		
	});
	document.querySelectorAll(".dynamic-gal").forEach((el, index) => {
		el.addEventListener("click", () => {
			dynamicGallery.openGallery(0);
		});
	});
}

(function ($) {
	"use strict";
	$(document).ready(function () {
		$('.adults-number select').select2();
		$('.nav-adults .goUp').on('click', function () {
			var index = $('select[name="adults_capacity"] option:selected').index();
			var count = $(' select[name="adults_capacity"] option').length;

			if (index + 1 >= count) {
				return;
			}

			var selected = $($('select[name="adults_capacity"] option')[index + 1]).val();

			$('select[name="adults_capacity"]').val(selected);
			$('input.adults-input').val(selected);

			$('select[name="adults_capacity"]').trigger('change.select2'); // Notify only Select2 of changes

		});

		$('.nav-adults .goDown').on('click', function () {
			var index = $('select[name="adults_capacity"] option:selected').index();
			if (index <= 0) {
				return;
			}
			var selected = $($('select[name="adults_capacity"] option')[index - 1]).val();
			$('select[name="adults_capacity"]').val(selected);
			$('input.adults-input').val(selected);
			
			$('select[name="adults_capacity"]').trigger('change.select2'); // Notify only Select2 of changes

		});
		$('#adults').each(function () {
			var $form_list = $('.hb-form-field-list.nav-adults');
			$('#adults').on('click touch', function () {
				$form_list.toggleClass('active');
			});
			$(document).on('click touch', function (event) {
				if (!$(event.target).parents().addBack().is('#adults')) {
					$form_list.removeClass('active');
				}
			});
			$form_list.on('click touch', function (event) {
				event.stopPropagation();
			});
		});
	});

	$(document).ready(function () {
		$('.children-number select').select2();
		$('.nav-children .goUp').on('click', function () {
			var index = $('select[name="max_child"] option:selected').index();
			var count = $(' select[name="max_child"] option').length;

			if (index + 1 >= count) {
				return;
			}

			var selected = $($('select[name="max_child"] option')[index + 1]).val();

			$('select[name="max_child"]').val(selected);
			$('input.child-input').val(selected);

			$('select[name="max_child"]').trigger('change.select2'); // Notify only Select2 of changes

		});

		$('.nav-children .goDown').on('click', function () {
			var index = $('select[name="max_child"] option:selected').index();
			if (index <= 0) {
				return;
			}
			var selected = $($('select[name="max_child"] option')[index - 1]).val();
			$('select[name="max_child"]').val(selected);
			$('input.child-input').val(selected);

			$('select[name="max_child"]').trigger('change.select2'); // Notify only Select2 of changes

		});
		$('#child').each(function () {
			var $form_list = $('.hb-form-field-list.nav-children');
			$('#child').on('click touch', function () {
				$form_list.toggleClass('active');
			});
			$(document).on('click touch', function (event) {
				if (!$(event.target).parents().addBack().is('#child')) {
					$form_list.removeClass('active');
				}
			});
			$form_list.on('click touch', function (event) {
				event.stopPropagation();
			});
		});
	});
})(jQuery);