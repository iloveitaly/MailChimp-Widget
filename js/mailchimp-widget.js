//'use strict';

(function($) {
	$.fn.ns_mc_widget = function(options) {
		var eL, opts;

		opts = jQuery.extend({
			'url' : '/',
			'cookie_id' : false,
			'cookie_value' : ''
		}, options);

		eL = $(this);
		eL.submit(function() {

			eL.mask({
				label:"Loading...",
				overlayOpacity: 0.25
			})
			
			$.getJSON(opts.url, eL.serialize(), function(data, textStatus) {
				var cookie_date, error_container, new_content;
				eL.unmask();

				if (textStatus === 'success') {
					if(data.success === true) {
						new_content = jQuery('<p class="alert alert-success">' + data.success_message + '</p>');
						eL.html(new_content);
						
						if(opts.cookie_id !== false) {
							cookie_date = new Date();
							cookie_date.setTime(cookie_date.getTime() + '3153600000');
							document.cookie = opts.cookie_id + '=' + opts.cookie_value + '; expires=' + cookie_date.toGMTString() + ';';
						}
					} else {
						error_container = jQuery('.error', eL);

						if(error_container.length === 0) {
							eL.children().show();
							error_container = jQuery('<div class="alert alert-danger"></div>');
							error_container.prependTo(eL);
						} else {
							eL.children().show();
						}

						error_container.html(data.error);
					}
				}

				return false;
			});

			return false;
		});
	};
}(jQuery));
