(function ($) {
	'use strict';

	$(document).ready(function () {
		$(document).on('click', '#tc-check-now', function () {
			var $self = $(this);
			$self.addClass('updating-message');
			$self.attr('disabled', true);

			_check_update();
		});

		$(document).on('click', '#tc-go-update', function () {
			window.location.href = $(this).attr('data-href');
		});

		function _check_update() {
			var url_ajax = thim_theme_update.admin_ajax;
			var i18l = thim_theme_update.i18l;

			$.ajax({
				url: url_ajax,
				method: 'GET',
				dataType: 'json'
			})
				.success(function (response) {
					if (response.success) {
						var data = response.data;

						$('.latest-version span').text(data.latest);
						$('.current-version span').text(data.current);

						var $btn_update = $('#tc-go-update');

						if (data.can_update) {
							$btn_update.attr('disabled', false);
							alert(i18l.can_update);
						} else {
							$btn_update.attr('disabled', true);
							alert(i18l.can_not_update);
						}
					} else {
						if (response.data) {
							alert(response.data);
						} else {
							alert(i18l.check_failed);
						}

						window.location.reload();
					}
				})
				.error(function (error) {
					alert(i18l.wrong);
				})
				.complete(function () {
					$('#tc-check-now').removeClass('updating-message');
					$('#tc-check-now').attr('disabled', false);
				})
		}
	});
})(jQuery);