//'use strict';

(function($) {
	$.fn.ns_mc_widget = function(options) {
		var target_form, opts;

		opts = jQuery.extend({
			'url' : '/',
			'cookie_id' : false,
			'cookie_value' : ''
		}, options);

		target_form = $(this);
		target_form.submit(function() {

			target_form.mask({
				label: "Loading...",
				overlayOpacity: 0.25
			})
			
			$.getJSON(opts.url, target_form.serialize(), function(data, textStatus) {
				var cookie_date, error_container, new_content;
				target_form.unmask();

				if(textStatus === 'success') {

					if(data.success === true) {
						new_content = jQuery('<p class="alert alert-success">' + data.success_message + '</p>');
						target_form.html(new_content);
						
						if(opts.cookie_id !== false) {
							cookie_date = new Date();
							cookie_date.setTime(cookie_date.getTime() + '3153600000');
							document.cookie = opts.cookie_id + '=' + opts.cookie_value + '; expires=' + cookie_date.toGMTString() + ';';
						}
					} else {
						error_container = jQuery('.error', target_form);

						if(error_container.length === 0) {
							target_form.children().show();
							error_container = jQuery('<div class="alert alert-danger"></div>');
							error_container.prependTo(target_form);
						} else {
							target_form.children().show();
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
