jQuery(function ($) {
	checkboxToggle();
 	/**
	 * Show, hide a <div> based on a checkbox
	 *
	 * @return void
	 * @since 1.0
	 */
	function checkboxToggle() {
		$('.rwmb-group-clone').each(function () {
			//var $this = $(this),
				$(this).on('change', '.checkbox-toggle input', function () {
				var $this = $(this),
					$toggle = $this.closest('.checkbox-toggle'),
					action;
				if (!$toggle.hasClass('reverse'))
					action = $this.is(':checked') ? 'slideDown' : 'slideUp';
				else
					action = $this.is(':checked') ? 'slideUp' : 'slideDown';

				$toggle.parent().next('.group-section-lever')[action]();
			})
			$('.checkbox-toggle input').trigger('change');
		});
		$('.wrapper-content-section').each(function () {
			//var $this = $(this),
				$(this).on('click', '.close-section', function () {
				var $this = $(this),
					$toggle = $this.closest('.checkbox-toggle'),
					action;
				if (!$toggle.hasClass('reverse'))
					action = $this.is(':checked') ? 'slideDown' : 'slideUp';
				else
					action = $this.is(':checked') ? 'slideUp' : 'slideDown';

				$toggle.parent().next('.group-section-lever')[action]();
			})
			$('.checkbox-toggle input').trigger('change');
		})
  	}

});