/**
 * Configurations
 */
window.config = {
	logging: true,
	baseURL: location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '') + '/',
	datepicker: { autoclose: true, todayHighlight: true, format: 'dd.mm.yyyy' },
	summernote: {
		height: 200,
		callbacks: {
			onChangeCodeview: function() {
				// Update the target textarea with the code view content as we type so we don't
				// have to switch back to wysiwyg from code view for it to be seen on post
				// @see https://github.com/summernote/summernote/issues/94
				$(this).trigger('summernote.change');
			},
			onImageUpload: function(files) {
				var $this = $(this);
				for (var i = 0; i < files.length; i++) {
					var ajaxData = new FormData();
					ajaxData.append('image', files[i]);

					executeAjax(window.config.baseURL + 'admin/ajax/image/post', ajaxData, function(response) {
						if (response.status) {
							response.url = parseURL(response.url);
							$this.summernote('insertImage', response.url.pathname);
						}
					}, { method: 'post', cache: false, contentType: false, processData: false });
				}
			}
		}
	},
};

/**
 * Language
 */
window.language = [];

/**
 * Cancel the event if it is cancelable, meaning that the default action that
 * belongs to the event will not occur.
 */
function preventDefaultBehavior(event) {
	event.preventDefault ? event.preventDefault() : (event.returnValue = false);
}

/**
 * Get URL parameter value
 */
function getURLVar(key, url = '') {
	var result = null, tmp = [];

	var urlHalves = decodeURIComponent(String(url ? url : document.location).toLowerCase()).split('?')[1] || '';
	var urlVars = urlHalves.split('&');

	for (var i = 0; i < urlVars.length; i++) {
		tmp = urlVars[i].split('=');

		if (tmp[0] == key.toLowerCase()) {
			result = tmp[1];
			break;
		}
	}

	return result;
}

/**
 * Parse a URL and return its components
 */
function parseURL(url) {
	var parser = document.createElement('a');
	parser.href = url;

	return parser;
}

/**
 * Execute an AJAX call
 */
function executeAjax(url, data, callback, options = {}) {
	const defaultSettings = {
		method: 'get',
		dataType: 'json',
		async: true,
		error: function(xhr) {
			if (xhr.readyState == 0 || xhr.status == 0) {
				return;
			}

			console.error('Error ' + xhr.status + ': ' + xhr.statusText);
			if (typeof bootoast !== 'undefined') {
				bootoast.toast({ message: xhr.responseJSON.message, type: 'danger'});
			}
		}
	};

	var settings = $.extend({}, defaultSettings, options);

	settings.url = url;
	settings.data = data;
	settings.success = callback;

	$.ajax(settings);
}

/**
 * Clear all inputs by selector
 */
function clearAllInputs(selector) {
	$(selector).find(':input').each(function() {
		switch (this.type) {
			case 'password':
			case 'text':
			case 'textarea':
			case 'select-one':
			case 'date':
			case 'number':
			case 'tel':
			case 'email':
				$(this).val('');
				break;
			case 'select-multiple':
				$(this).val([]);
				break;
			case 'checkbox':
			case 'radio':
				this.checked = false;
				break;
			case 'file':
				$(this).val(null);
				break;
		}
	});
}

/**
 * Set widget related input values
 */
function setWidgetValue(object, value) {
	switch (object.prop('type')) {
		case 'password':
		case 'text':
		case 'textarea':
		case 'select-one':
		case 'select-multiple':
		case 'date':
		case 'number':
		case 'tel':
		case 'email':
			object.val(value).trigger('change');
			if (object.hasClass('widget-image-upload') && value) {
				object.prev().prop('src', value).removeClass('hidden');
			}
			break;
		case 'checkbox':
		case 'radio':
			object.prop('checked', !!value);
			break;
	}
}

/**
 * Clone widget element item
 */
function cloneWidgetItem(object) {
	var item = object.find('.widget-item-clone');
	var clonedItem = item.first().clone().removeClass('hidden');
		clonedItem.find('input,textarea,select').prop('disabled', false);

	item.last().after(clonedItem);

	return clonedItem;
}
