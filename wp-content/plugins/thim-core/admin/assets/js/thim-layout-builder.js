(function ($) {
	$(document).ready(function () {
		update();

		$(window).resize(function () {
			update();
		});

		$(document).bind('ajaxComplete', function () {
			update();
		});

		function update() {
			var max_width = get_width();
			var max_height = get_height();
			var $button_layout_builder = $(document).find('.widget-liquid-right .thim-wrapper-layout-builder > a');

			$button_layout_builder.each(function () {
				var $item = $(this);

				var url = $item.attr('href');
				if (!url) {
					return;
				}

				var new_url = render_url(url, max_width, max_height);
				$item.attr('href', new_url);
			});
		}

		function get_width() {
			return $(window).width();
		}

		function get_height() {
			return $(window).height();
		}

		function render_url(url, width, height) {
			width = parseInt(width) - 100;
			height = parseInt(height) - 100;
			var new_url = url.replace(/width=\d+/i, 'width=' + width);
			new_url = new_url.replace(/height=\d+/i, 'height=' + height);

			return new_url;
		}
	});
})(jQuery);