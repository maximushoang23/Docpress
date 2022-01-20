;'use strict';

var Thim_Getting_Started = (function ($) {
	var current_step = 1;
	var current_key_step = '';
	var max_step = 1;
	const BASE_URL_AJAX = thim_gs.url_ajax;

	return {
		init: init,
		onEvent: onEvent
	};

	function init() {
		max_step = $('.thim-getting-started .tc-controls').attr('data-max');
		current_step = _get_step_by_url();
		current_key_step = _get_key_step_by_index(current_step);

		_go_to_step(current_step);
	}

	function onEvent() {
		var $root = $(document);

		$root.on('click', '.tc-controls .step', function (event) {
			event.preventDefault();
			return;//Disable

			var self = $(this);
			var position = self.attr('data-position');

			_go_to_step(position);
		});

		$('.tc-run-step').on('click', function () {
			var $self = $(this);

			var request = $self.attr('data-request') ? true : false;
			_next_step(request);

			if (request) {
				$self.addClass('updating-message');
				$self.attr('disabled', true);
			}
		});

		$root.on('click', '#skip-step', function (e) {
			e.preventDefault();
			_next_step();
		});
	}

	function _request(step, data) {
		var url_request = BASE_URL_AJAX + step;

		return $.ajax({
			url: url_request,
			method: 'POST',
			data: data,
			dataType: 'json'
		}).complete(function () {
			var $run_step = $('.tc-run-step');
			$run_step.removeClass('updating-message');
			$run_step.attr('disabled', false);
		});
	}

	function _next_step(request) {
		request = (undefined != request) ? request : false;

		if (current_step == max_step) {
			return;
		}

		if (!request) {
			current_step++;
			current_key_step = _get_key_step_by_index(current_step);
			_go_to_step(current_step);
		} else {
			_run_step(current_key_step);
		}
	}

	function _run_step(key_step) {
		switch (key_step) {
			case 'quick-setup':
				_request_quick_setup();
				break;

			case 'install-plugins':
				_request_install_plugins();
				break;

			case 'import-demo':
				_request_import_demo();
				break;

			default:
				_increase_step();
				_go_to_step(current_step);
		}
	}

	function _request_quick_setup() {
		var $form = $('.tc-step.quick-setup form');
		var data = $form.serialize();

		_request(current_key_step, data)
			.success(function (response) {
				_increase_step();
				_go_to_step(current_step);
			})
			.error(function (error) {
				console.error(error);
			});
	}

	function _request_install_plugins() {
		var arrSlug = [];
		var current_plugin = false;

		$('.thim-table-plugins').addClass('running');
		var $plugins = $('.thim-getting-started').find('.thim-plugins input.thim-input:checked');

		$plugins.each(function (index) {
			var slug = $(this).val();
			arrSlug.push(slug);

			$('.thim-table-plugins tr[data-plugin="' + slug + '"]').addClass('processing');
		});

		_install_and_active_plugins();

		function _install_and_active_plugins() {
			current_plugin = arrSlug[0];

			_install_current_plugin(current_plugin);
		}

		function _next_plugin() {
			if (arrSlug.length == 0) {
				_increase_step();
				_go_to_step(current_step, true);
				return;
			}
			current_plugin = arrSlug[0];

			_install_current_plugin();
		}

		function _install_current_plugin() {
			Thim_Plugins.request('install', current_plugin)
				.success(
					function (response) {
						if (response.success) {
							console.log('Install successful');
						} else {
							console.log('Install failed or was installed');
						}
					}
				)
				.complete(function () {
					_activate_current_plugin(current_plugin);
				});
		}

		function _activate_current_plugin() {
			Thim_Plugins.request('activate', current_plugin)
				.success(
					function (response) {
						var $plugin = $('.thim-table-plugins tr[data-plugin="' + current_plugin + '"]');
						if (response.success) {
							$plugin.removeClass('inactive processing').addClass('active');
							$plugin.find('.updating-message').text('Active');
						} else {
							$plugin.removeClass('processing').addClass('failed');
							$plugin.find('.updating-message').text('Please try again later!');
						}
					}
				)
				.complete(
					function () {
						arrSlug.splice(0, 1);
						_next_plugin();
					}
				)
		}
	}

	function _request_import_demo() {
		_request(current_key_step, {})
			.success(function (response) {
				_increase_step();
				_go_to_step(current_step);
			})
			.error(function (error) {
				console.error(error);
			});
	}


	function _increase_step() {
		current_step++;
		current_key_step = _get_key_step_by_index(current_step);
	}

	function _get_key_step_by_index(index) {
		var $step = $('.tc-controls .step[data-position="' + index + '"]');

		if (!$step.length) {
			return false;
		}

		return $step.attr('data-step');
	}

	function _get_step_by_url() {
		var current_url = window.location.href;

		var regex = /#step-(\d+)$/gi;
		var arr = regex.exec(current_url);

		if (!arr || arr.length !== 2) {
			return 1;
		}

		var index = parseInt(arr[1]);
		if (index > max_step) {
			return max_step;
		}

		if (index < 1) {
			return 1;
		}

		return index;
	}

	function _update_current_url(index, reload) {
		document.location.hash = '#step-' + index;

		if (reload === true) {
			window.location.reload();
		}
	}

	function _go_to_step(index, reload) {
		current_step = index;

		_update_current_url(current_step, reload);
		if (reload === true) {
			return;
		}

		if (current_step > max_step) {
			current_step = max_step;
		}

		current_key_step = _get_key_step_by_index(current_step);

		if (current_step == max_step) {
			$('#skip-step').hide();
			$('#next-step').hide();
			$('#finish-step').show();
		} else {
			$('#finish-step').hide();
			$('#next-step').show();
			$('#skip-step').show();
		}

		$('.tc-step').removeClass('active');

		var $steps = $('.tc-controls .step');
		$steps.removeClass('active current');
		$steps.each(function () {
			var $st = $(this);
			var p = $st.attr('data-position');

			if (p <= current_step) {
				$st.addClass('active');
			}

			if (p == current_step) {
				$st.addClass('current');
				var key_step = $st.attr('data-step');
				$('.tc-step.' + key_step).addClass('active');
			}
		});
	}
})(jQuery, Thim_Plugins);

(function ($, thim) {
	$(document).ready(function () {
		thim.init();
		thim.onEvent();
	});
})(jQuery, Thim_Getting_Started);