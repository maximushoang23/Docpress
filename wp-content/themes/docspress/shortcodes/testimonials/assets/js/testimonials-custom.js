(function ($) {
	"use strict";

	$(document).ready(function () {

		$('.testimonial-slider').each(function () {
			var elem = $(this),
				item_visible = parseInt(elem.data('visible')),
				autoplay = elem.data('autoplay') ? true : false,
				mousewheel = elem.data('mousewheel') ? true : false;

			var testimonial_slider = $(this).thimContentSlider({
				items            : elem,
				itemsVisible     : item_visible,
				mouseWheel       : mousewheel,
				autoPlay         : autoplay,
				itemMaxWidth     : 80,
				itemMinWidth     : 80,
				activeItemRatio  : 1,
				activeItemPadding: 0,
				itemPadding      : 15,
				contentPosition  : 'top',
			});
		});

	});

})(jQuery);