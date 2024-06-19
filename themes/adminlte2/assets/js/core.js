$(function () {
	'use strict';

	/**
	 * summernote wysiwyg editor
	 */
	if ($.fn.summernote) {
		$('textarea.wysiwyg').summernote(window.config.summernote);
	}

	// datepicker input field
	if ($.fn.datepicker) {
		$('input.datepicker').datepicker(window.config.datepicker);
	}

	// tooltip
	$(document).tooltip({ selector: '[rel=tooltip]', trigger: 'hover' });

	// iCheck for checkbox and radio inputs
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
		radioClass: 'iradio_minimal-blue'
	})
	// Red color scheme for iCheck
	$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
		checkboxClass: 'icheckbox_minimal-red',
		radioClass: 'iradio_minimal-red'
	})
	// Flat red color scheme for iCheck
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	})

	$('input.image-upload').on('change', function() {
		var dataTarget = $(this).data('target');
		if ($(dataTarget).length === 0) return;

		if (this.files && this.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$(dataTarget).attr('src', e.target.result).removeClass('hidden');
			};

			reader.readAsDataURL(this.files[0]);
		}
	});

	$(document)

		/**
		 * Click event for the table rows
		 */
		.on('click', '#simple-table>tbody>tr.click-row', function(e) {
			if (this === e.target.parentElement) {
				window.location.href = $(this).data('href') || window.location.href;
			}
		});

	$(document)

		/**
		 * Append entry to the 'delete' modal body
		 */
		.on('show.bs.modal', '#modal-clone,#modal-delete', function(e) {
			const $related = $(e.relatedTarget);

			var dataId = 0;
			var modalAppend = '';

			if ($related.data('id')) {
				dataId = modalAppend = $related.data('id');
			} else {
				const $tr = $related.closest('tr');
				dataId = $tr.data('id');
				modalAppend = $tr.find('.modal-append:first').text().trim();
			}

			$(this).find('.modal-field-id').val(dataId);
			$(this).find('.modal-text-append').html(modalAppend);
		})

		/**
		 * Reset 'delete' modal body when is hidden
		 */
		.on('hidden.bs.modal', '#modal-delete', function() {
			$(this).find('.modal-field-id').val(0);
			$(this).find('.modal-text-append').html('');
		})

		/**
		 * Submit modal form
		 */
		.on('click', '.modal-submit', function(e) {
			// prevent default behavior
			preventDefaultBehavior(e);
			$(this).closest('.modal').find('form').submit();
		});

	$(document)

		/**
		 * Trigger AJAX upload when input type=file is changed
		 */
		.on('change', '.ajax-image-upload', function(e) {
			// to check that even single file is selected or not
			if (!e.target.files) return;

			const $this = $(this);

			var files = e.target.files;
			for (var i = 0; i < files.length; i++) {
				var ajaxData = new FormData();
					ajaxData.append('image', files[i]);

				executeAjax(window.config.baseURL + 'admin/ajax/image/post', ajaxData, function(response) {
					response.url = parseURL(response.url);
					setWidgetValue($this.prev(), response.url.pathname);
					$this.val(null);
				}, { method: 'post', cache: false, contentType: false, processData: false });
			}
		});

	// --------------------------------------------------------------------

	/**
	 * Bootstrap select modal
	 */
	var modalEventInvoker = {};

	$(document)

		/**
		 * Trigger update on 'select' modal show
		 */
		.on('show.bs.modal', '#modal-select,#modal-multiselect', function(e) {
			modalEventInvoker = $(e.relatedTarget);

			var dataParams = modalEventInvoker.data('params');
			if (typeof dataParams === 'undefined' || $.isEmptyObject(dataParams)) {
				modalEventInvoker.data('params', {
					selected: 0,
					limit: 10,
					offset: 0,
					filters: {},
				});
			}

			$(this).trigger('update.bs.modal');
		})

		/**
		 * Reset values and 'select' modal body on hidden
		 */
		.on('hidden.bs.modal', '#modal-select,#modal-multiselect', function() {
			$(this).find('.modal-body').empty();
			modalEventInvoker.data('params', {});
		})

		/**
		 * Main event to get data for 'select' modal
		 */
		.on('update.bs.modal', '#modal-select,#modal-multiselect', function() {
			if (typeof modalEventInvoker.data('url') === 'undefined') {
				return false;
			}

			const $this = $(this);

			var dataParams = modalEventInvoker.data('params');
			if ( ! $.isArray(dataParams.selected)) {
				const dataInputVal = $('[name='+modalEventInvoker.data('input')+'_id]').val();
				try {
					dataParams.selected = JSON.parse(dataInputVal);
				} catch (e) {
					dataParams.selected = dataInputVal || 0;
				}
			}
			modalEventInvoker.data('params', dataParams);

			// set up AJAX request data
			var ajaxData = dataParams;

			// define callback function to handle AJAX call result
			var ajaxResults = function(data, textStatus, xhr) {
				if (xhr.status < 200 || xhr.status > 204) {
					return;
				}

				$this.find('.modal-body').html(data.html);
			};

			executeAjax(modalEventInvoker.data('url'), ajaxData, ajaxResults);
		})

		/**
		 * AJAX based modal table pagination
		 */
		.on('click', '#modal-select ul.pagination>li>a,#modal-multiselect ul.pagination>li>a', function(e) {
			preventDefaultBehavior(e);

			var attrHref = $(this).attr('href');
			var dataParams = modalEventInvoker.data('params');
			dataParams.offset = attrHref ? parseInt(getURLVar('offset', attrHref)) : 0;

			modalEventInvoker.data('params', dataParams);

			$(this).closest('.modal').trigger('update.bs.modal');
		})

		/**
		 * Reset filters for items inside modal table
		 */
		.on('click', '#modal-select [data-toggle=reset],#modal-multiselect [data-toggle=reset]', function(e) {
			preventDefaultBehavior(e);

			$('#modal-select').find('input,select').each(function() {
				$(this).val('');
			});

			var dataParams = { limit: 10, offset: 0, filters: {} };
			modalEventInvoker.data('params', dataParams);

			$(this).closest('.modal').trigger('update.bs.modal');
		})

		/**
		 * Filters items inside modal table
		 */
		.on('click', '#modal-select [data-toggle=filter],#modal-multiselect [data-toggle=filter]', function(e) {
			preventDefaultBehavior(e);

			var dataParams = modalEventInvoker.data('params');
			dataParams.offset = 0;
			dataParams.filters = {};

			$('#modal-select').find('input,select').each(function() {
				const $this = $(this);
				if ($this.val() !== '') {
					dataParams.filters[$this.attr('name')] = $this.val();
				}
			});

			modalEventInvoker.data('params', dataParams);

			$(this).closest('.modal').trigger('update.bs.modal');
		})

		/**
		 * Select item from modal table
		 */
		.on('click', '#modal-select [data-toggle=select]', function(e) {
			preventDefaultBehavior(e);

			const dataInput = modalEventInvoker.data('input');

			var jsonObject = $(this).data('json');
			if (typeof jsonObject !== 'undefined') {
				for (var key in jsonObject) {
					$('[name^='+dataInput+'_'+key).val(jsonObject[key]).trigger('change');
				}
			}

			$(this).closest('.modal').modal('hide');
		})

		/**
		 * Select/Check item from modal table
		 */
		.on('change', '#modal-multiselect [data-toggle=select]', function(e) {
			preventDefaultBehavior(e);

			const inputVal = parseInt($(this).val());
			const closestRow = $(this).closest('tr');

			var dataParams = modalEventInvoker.data('params');

			if (this.checked) {
				dataParams.selected.push(inputVal);
				closestRow.addClass('active');
			} else {
				const i = dataParams.selected.indexOf(inputVal);
				dataParams.selected.splice(i, 1);
				closestRow.removeClass('active');
			}

			modalEventInvoker.data('params', dataParams);
		})

		/**
		 * Submit selected multiselect modal table
		 */
		.on('click', '#modal-multiselect [data-submit=modal]', function(e) {
			preventDefaultBehavior(e);

			const dataInput = modalEventInvoker.data('input');
			const dataParams = modalEventInvoker.data('params');

			$('[name='+dataInput+'_id').val(JSON.stringify(dataParams.selected)).trigger('change');

			$(this).closest('.modal').modal('hide');
		})

		/**
		 * Clear input value selected from modal table
		 */
		.on('click', '[data-toggle=clear]', function(e) {
			preventDefaultBehavior(e);

			$('[name^='+$(this).data('input')+']').val('').trigger('change');
		})

		/**
		 * Delete input value selected from modal table
		 */
		.on('click', '[data-toggle=delete]', function(e) {
			preventDefaultBehavior(e);

			const $this = $(this);
			const dataInput = $this.data('input');
			const dataInputVal = $('[name='+dataInput+'_id]').val();

			try {
				selected = JSON.parse(dataInputVal);
				const i = selected.indexOf($this.data('id'));
				selected.splice(i, 1);
				selected = JSON.stringify(selected);
			} catch (e) {
				selected = dataInputVal || 0;
			}

			$('[name='+dataInput+'_id').val(selected).trigger('change');

			$this.closest('.input-group').remove();
		});

});
