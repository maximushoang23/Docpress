jQuery(document).ready(function ($) {

	'use strict';

	jQuery('a.kirki-reset-section').on('click', function () {

		var id = jQuery(this).data('reset-section-id'),
			controls = wp.customize.section(id).controls();

		// Loop controls
		_.each(controls, function (control, i) {

			// Set value to default
			kirkiSetSettingValue(controls[i].id, control.params['default']);

		});

	});

	/**
	 * Evaluate to show/hide all fields depending on value of current field
	 *
	 * @param control
	 * @param _control
	 * @returns {mixed}
	 */
	function evaluate(control, _control) {
		var show = undefined;
		if (!$.isArray(_control.active_callback)) {
			return show;
		}
		$.each(_control.active_callback, function (k, v) {
			if (control.id != v.setting) {
				return;
			}
			show = evaluate_requirement(control, _control, v);
			if (show === false) {
				return false;
			}
		});
		return show;
	}

	window.thim_evaluate = evaluate;

	/**
	 * Evaluate to see a field should be show/hide if there are 'active_callback'
	 *
	 * @param $object
	 * @param $field
	 * @param $requirement
	 * @returns {boolean}
	 */
	function evaluate_requirement($object, $field, $requirement) {
		var show = undefined;
		if (!$requirement) {
			return show;
		}
		if ($requirement['operator'] && $requirement['value'] && $requirement['setting']) {
			var current_setting = $object.setting.get();
			show = compare($requirement['value'], current_setting, $requirement['operator']);
		}
		return show;
	}

	/**
	 * Compares two values with an operation
	 *
	 * @param $value1
	 * @param $value2
	 * @param $operator
	 * @returns {*}
	 */
	function compare($value1, $value2, $operator) {
		var show = undefined;
		switch ($operator) {
			case '===':
				show = ( $value1 === $value2 ) ? true : false;
				break;
			case '==':
			case '=':
			case 'equals':
			case 'equal':
				show = ( $value1 == $value2 ) ? true : false;
				break;
			case '!==':
				show = ( $value1 !== $value2 ) ? true : false;
				break;
			case '!=':
			case 'not equal':
				show = ( $value1 != $value2 ) ? true : false;
				break;
			case '>=':
			case 'greater or equal':
			case 'equal or greater':
				show = ( $value1 >= $value2 ) ? true : false;
				break;
			case '<=':
			case 'smaller or equal':
			case 'equal or smaller':
				show = ( $value1 <= $value2 ) ? true : false;
				break;
			case '>':
			case 'greater':
				show = ( $value1 > $value2 ) ? true : false;
				break;
			case '<':
			case 'smaller':
				show = ( $value1 < $value2 ) ? true : false;
				break;
			case 'contains':
			case 'in':
				var _array, _string;
				if ($.isArray($value1) && !$.isArray($value2)) {
					_array = $value1;
					_string = $value2;
				} else if ($.isArray($value2) && !$.isArray($value1)) {
					_array = $value2;
					_string = $value1;
				}
				if (_array && _string) {
					if (!$.inArray(_string, _array)) {
						show = false;
					}
				} else {
					if (-1 === $value1.indexOf($value2) && -1 === $value2.indexOf($value1)) {
						show = false;
					}
				}
				break;
			default:
				show = ( $value1 == $value2 ) ? true : false;

		}

		if (show != undefined) {
			return show;
		}

		return true;
	}

	/**
	 * Loop through all sections to get controls
	 */
	$('[data-reset-section-id]').each(function () {
		var id = $(this).data('reset-section-id'),
			controls = wp.customize.section(id).controls();
		/*
		 * Loop controls and bind event 'change'
		 * When user change any field in customize we need to show/hide a depending field
		 */
		_.each(controls, function (control, i) {
			control.setting.control = control;
			control.setting.bind('change', function () {
				var setting = this;
				$.each(_wpCustomizeSettings.controls, function (k, _control) {
					var show = evaluate(setting.control, _control);
					if (show !== undefined) {
						var findControl = wp.customize.control(k);
						if (findControl) {
							findControl.container.toggle(show);
						}
					}
				});
			});
		});
	})
});
