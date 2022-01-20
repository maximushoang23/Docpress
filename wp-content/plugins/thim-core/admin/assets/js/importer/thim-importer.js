;'use strict';
var Thim_Importer = (function ($) {
	var demos = thim_importer_data.demos;
	var url_ajax_action = thim_importer_data.admin_ajax_action;
	var packages = [];
	var current_request = null;
	var current_demo = {};
	var doing_import = false;
	var is_started = false;

	return {
		chooseDemo: chooseDemo,
		onEvent: onEvent
	};

	//@public
	function chooseDemo(demo_key) {
		doing_import = false;
		_unlock_window();
		_openModal('.tc-modal-importer');
		_setup(demo_key);
	}

	//@public
	function onEvent() {
		$('.tc-modal-importer .close').on('click', function () {
			if (doing_import) {
				var text_confirm = thim_importer_data.confirm_close;
				var close = confirm(text_confirm);
				if (close) {
					_unlock_window();
					_closeModal('.tc-modal-importer');
				} else {
					_lock_window();
				}

				return;
			}

			_closeModal('.tc-modal-importer');
		});

		$('#start-import').on('click', function () {
			is_started = true;
			$(this).attr('disabled', true);
			_startImport();
		});

		$('.tc-modal-importer').on('click', '.package:not(.disabled, .obligatory)', function () {
			if (doing_import) {
				return;
			}

			var self = this;
			var key = $(self).data('package');
			var $checkbox = $(self).find('input');
			$checkbox.prop('checked', !$checkbox.prop('checked'));
			_on_change_input(key);
		});

		$('#form-importer input').on('change', function () {
			var pack = $(this).parents('.package').data('package');
			_on_change_input(pack);
		});

		$('#btn-uninstall').on('click', function () {
			_openModal('.tc-modal-importer-uninstall');
		});

		$('.tc-modal-importer-uninstall .close').on('click', function () {
			if (doing_import) {
				var text_confirm = thim_importer_data.confirm_close;
				var close = confirm(text_confirm);
				if (close) {
					_unlock_window();
					_closeModal('.tc-modal-importer-uninstall');
				} else {
					_lock_window();
				}

				return;
			}

			_closeModal('.tc-modal-importer-uninstall');
		});

		$('.tc-modal-importer-uninstall .tc-start').on('click', function () {
			$(this).addClass('updating-message');
			$(this).attr('disabled', true);
			_uninstall_demo();
		});

		$(window).on('thim_importer_start', function () {
			$('head title').text('Import demo is running...');
		});

		$(window).on('thim_importer_complete', function () {
			$('head title').text('Import demo completed :]');
		});

		$(window).on('thim_importer_failed', function () {
			$('head title').text('Import demo failed :]');
		});
	}

	function _emit_event(key_event, args) {
		$(window).trigger(key_event, args);
	}

	//@private
	function _uninstall_demo() {
		var url_ajax = thim_importer_data.admin_ajax_uninstall;

		current_request = $.ajax({
			url: url_ajax,
			method: 'POST',
			dataType: 'text'
		})
			.success(function (response) {
				response = _parseJSON(response);

				if (response.success) {
					alert(thim_importer_data.uninstall_successful);
				} else {
					alert(thim_importer_data.uninstall_failed);
				}

				window.location.reload();
			})
			.error(function (error) {
				if (error.statusText == 'abort') {
					return;
				}
				alert(thim_importer_data.something_went_wrong);
				window.location.reload();
			})
			.complete(function () {
				$('.tc-modal-importer-uninstall .tc-start')
					.removeClass('updating-message');
			})

	}

	//@private
	function _parseJSON(data) {
		var m = data.match(/<!-- THIM_IMPORT_START -->(.*)<!-- THIM_IMPORT_END -->/);
		try {
			if (m) {
				data = $.parseJSON(m[1]);
			} else {
				data = $.parseJSON(data);
			}
		} catch (e) {
			data = false;
		}
		return data;
	}

	//@private
	function _openModal(selector) {
		var $modal_importer = $(selector);
		$modal_importer.addClass('md-show');
		$modal_importer.find('.main').scrollTop(0);

		var $thim_dashboard = $('.thim-dashboard');
		$thim_dashboard.addClass('thim-modal-open');
	}

	//@private
	function _startImport() {
		_emit_event('thim_importer_start');
		doing_import = true;
		_lock_window();
		$('.tc-modal-importer').addClass('importing');
		$('.tc-modal-importer input').attr('disabled', true);
		_update_selected_packages();
		_initializeImporter();
	}

	//@private
	function _setup(demo_key) {
		_setDemoData(demo_key);
		_setupDOM();
	}

	//@private
	function _setDemoData(demo_key) {
		current_demo = demos[demo_key] || {};
		current_demo.key = demo_key;
	}

	//@private
	function _setupDOM() {
		var title = current_demo.title;
		$('.tc-modal-importer .demo-name').text(title);

		var $pre_import = $('.tc-modal-importer .pre-import');
		$pre_import.show();
		var plugins_required = current_demo.plugins_required;
		if (plugins_required.length == 0) {
			$pre_import.hide();
		} else {
			var str_plugins = '';
			$.each(plugins_required, function (index) {
				var plugin = this;
				str_plugins += '<span data-slug="' + plugin.slug + '" data-status="' + plugin.status + '">' + plugin.name + '</span>';
				if (index + 1 < plugins_required.length) {
					str_plugins += ', ';
				}
			});
			$('.plugins-required').html(str_plugins);
		}

		// Reset
		$('.tc-modal-importer').removeClass('importing completed');
		$('.package').removeAttr('data-status').filter('.main_content, .media').attr('data-percentage', '0%').find('.package-progress-bar').css('width', 0);
		$('#start-import').attr('disabled', false);
		$('.tc-modal-importer input').attr('disabled', false);
	}

	//@private
	function _initializeImporter() {
		current_request = $
			.ajax({
				url: url_ajax_action,
				method: 'POST',
				data: {
					demo: current_demo,
					packages: packages
				},
				dataType: 'text'//'json'
			})
			.success(function (response) {
				response = _parseJSON(response);
				var success = response.success || false;
				if (!success) {
					console.error('Failed!');
				}

				var data = response.data;
				var first_step = data.next;
				if (!first_step) {
					return _notify_success();
				}

				_stepByStep(first_step);
			})
			.error(function (error) {
				return _notify_error_ajax(error);
			});
	}

	//@private
	function _on_change_input(pack) {
		_update_selected_packages();
		_check_required(pack);

		var $btn_start_import = $('#start-import');
		if (packages.length > 0) {
			$btn_start_import.attr('disabled', false);
			return;
		}

		$btn_start_import.attr('disabled', true);
	}

	//@private
	function _check_required(pack) {
		var $package = $('#form-importer .package[data-required="' + pack + '"]');
		if ($package.length == 0) {
			return;
		}

		var sub_pack = $package.data('package');
		var $input = $('#importer-' + sub_pack);
		var is_checked = $('#importer-' + pack).prop('checked');
		$input.attr('disabled', !is_checked);
		$package.toggleClass('disabled');
	}

	//@private
	function _update_selected_packages() {
		packages = [];
		$('#form-importer .package:not(.disabled) input:checked').each(function () {
			var pack = $(this).parents('.package').data('package');

			packages.push(pack);
		});
	}

	//@private
	function _stepByStep(step) {
		$('.package.' + step).attr('data-status', 'running');
		_scroll_to(step);

		current_request = $
			.ajax({
				url: url_ajax_action,
				method: 'POST',
				dataType: 'text'//'json'
			})
			.success(function (response) {
				response = _parseJSON(response);
				if (!response) {
					return _stepByStep(step);
				}

				var success = response.success || false;
				var data = response.data;
				if (!success) {
					return _notify_error(data);
				}

				var done_step = data.done || false;
				if (done_step) {
					var $done_wrap = $('.package.' + done_step).attr('data-status', 'completed');
					$done_wrap.attr({'data-percentage': '100%'}).find('.package-progress-bar').css('width', '100%');
				} else {
					if (data.ext) {
						if ($.inArray(data.next, ['media', 'main_content']) != -1) {
							var $done_wrap = $('.package.' + data.next).attr({'data-percentage': data.ext.percentage + '%'});
							$done_wrap.find('.package-progress-bar').css('width', data.ext.percentage + '%');
						}
					}
				}

				/**
				 * Install or activate plugin
				 */
				var ext = data.ext || false;
				if (ext) {
					var activated = ext.activated || false;
					if (activated) {
						$('.plugins-required > span[data-slug="' + activated + '"]').attr('data-status', 'active');
					}
					var installed = ext.installed || false;
					if (installed) {
						$('.plugins-required > span[data-slug="' + installed + '"]').attr('data-status', 'inactive');
					}
				}


				var next_step = data.next;
				if (!next_step) {
					return _notify_success();
				}

				/**
				 * Recursive
				 */
				_stepByStep(next_step);
			})
			.error(function (error) {
				return _notify_error_ajax(error);
			});
	}

	//@private
	function _notify_error_ajax(error) {
		var l18n = thim_importer_data.details_error;

		var details = {
			title: l18n.title
		};

		if (error.status === 200) {
			details.code = l18n.code.request;

			return _notify_error(details);
		}

		if (error.status > 200) {
			details.code = l18n.code.server;

			return _notify_error(details);
		}

		return true;
	}

	//@private
	function _notify_error(details) {
		_emit_event('thim_importer_failed');

		var $detail_error = $('.tc-modal-importer .wrapper-finish .details-error');
		$detail_error.find('.get-support .error-code').text(details.code);
		$detail_error.find('h3').text(details.title);

		var how_to = details.how_to || false;
		if (!how_to) {
			$detail_error.find('.how-to').hide();
		} else {
			$detail_error.find('.how-to').html(how_to).show();
		}

		$('.tc-modal-importer .wrapper-finish').removeClass('success').addClass('failed');
		_finish();

		return true;
	}

	//@private
	function _notify_success() {
		_emit_event('thim_importer_complete');
		$('.tc-modal-importer .wrapper-finish').removeClass('failed').addClass('success');
		_finish();

		return true;
	}

	//@private
	function _closeModal(selector) {
		if (is_started) {
			return window.location.reload(true);
		}

		_finish();

		var $modal_importer = $(selector);
		$modal_importer.removeClass('md-show');

		var $thim_dashboard = $('.thim-dashboard');
		$thim_dashboard.removeClass('thim-modal-open');
	}

	//@private
	function _finish() {
		_force_stop();

		return true;
	}

	//@private
	function _force_stop() {
		$('.tc-modal-importer').removeClass('importing').addClass('completed');

		doing_import = false;
		_unlock_window();
		if (current_request) {
			current_request.abort();
		}
	}

	//@private
	function _scroll_to(step) {
		var container = $('.tc-modal-importer .main');
		var target = $('.package.' + step);

		container.stop().animate({
			scrollTop: target.offset().top - container.offset().top + container.scrollTop()
		}, 500);
	}

	//@private
	function _lock_window() {
		window.onbeforeunload = function () {
			return 'The import process will cause errors if you leave this page!';
		};
	}

	//@private
	function _unlock_window() {
		window.onbeforeunload = null;
	}
})(jQuery);

/**
 * Main thim importer
 */
(function ($, Thim_Importer) {
	$(document).ready(document_ready);

	function document_ready() {
		Thim_Importer.onEvent();

		$('.action-import').on('click', function () {
			var $thim_demo = $(this).closest('.thim-demo');
			var demo_key = $thim_demo.data('thim-demo');
			Thim_Importer.chooseDemo(demo_key);
		});

		$('.thim-screenshot').on('click', function () {
			var $thim_demo = $(this).closest('.thim-demo');
			var demo_key = $thim_demo.data('thim-demo');
			Thim_Importer.chooseDemo(demo_key);
		});
	}
})(jQuery, Thim_Importer);