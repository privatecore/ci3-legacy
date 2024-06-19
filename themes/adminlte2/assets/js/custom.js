jQuery(function($) {

	/**
	 * Bootstrap IE10 viewport bug workaround
	 */
	if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
		var msViewportStyle = document.createElement('style');
		msViewportStyle.appendChild(
			document.createTextNode('@-ms-viewport{width:auto!important}')
		);

		document.querySelector('head').appendChild(msViewportStyle);
	}

	/**
	 * Encode a set of form elements as an object of serializeArray names and
	 * values. Based on: https://stackoverflow.com/q/8900587
	 */
	$.fn.serializeObject = function() {
		var obj = {};
		var arr = this.serializeArray();

		$.each(arr, function() {
			if (obj[this.name]) {
				if (!obj[this.name].push) obj[this.name] = [obj[this.name]];
				obj[this.name].push(this.value || '');
			} else {
				obj[this.name] = this.value || '';
			}
		});

		return obj;
	};

	/**
	 * @todo
	 * @param {*} obj
	 * @returns
	 */
	$.fn.tmpl = function(obj) {
		const self = this;
		const $this = $(self);

		return (function() {
			if ($this.length == 0) return self;

			var original = $this.html();
			$this.html($this.html().replace(/{{([^}}]+)}}/g, function(wholeMatch, key) {
				var substitution = obj[$.trim(key)];
				return typeof substitution === 'undefined' ? wholeMatch : substitution;
			}));

			return $this.html() == original ? self : $($this).tmpl(obj);
		})();
	};

});
