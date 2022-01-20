;'use strict';

var Thim_Plugins = (function ($) {
	var ajax_url = thim_plugins_manager.admin_ajax_action;

	return {
		request: request,
		unescape_html: unescape_html
	};

	function request(action, slug) {
		return $.ajax({
			url: ajax_url,
			method: 'POST',
			data: {
				plugin_action: action,
				slug: slug
			},
			dataType: 'json'
		})
			.error(function (error) {
				alert('Bad request! Please try again!');
				document.location.reload(true);
			})
	}

	function unescape_html(html) {
		return $('<textarea></textarea>').html(html).text();
	}
})(jQuery);