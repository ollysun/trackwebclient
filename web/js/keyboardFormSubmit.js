(function($){
	var defaults = {
		type: 'disable',
		formAttribute: 'data-keyboard-submit',
		eventNamespace: '.CP.form.keyboard-submit',
		inputSelector: 'input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="range"])',
	};

	var formAttr = defaults.formAttribute;

	var keyboardFormSubmit = function(form, type) {
		var isEnabled;
		var eventName = 'keypress'+defaults.eventNamespace;

		if (typeof form.jquery === 'undefined') //not a jquery object
			form = $(form);

		type = (typeof type !== 'undefined') ? type : form.attr(formAttr) ? form.attr(formAttr) : defaults.type;

		var input = form.find(defaults.inputSelector);

		if (type === 'enable' || type === '1' || type === 'true')
			enable();
		else if (type === 'disable' || type === '0' || type === 'false' || type === '')
			disable();


		function disable() {
			$(input).off(eventName).on(eventName,function(e) {
				if(e.which === 13) { //enter
					e.preventDefault();
					return false;
				}
			});
			isEnabled = false;
		}
		function enable() {
			$(input).off(eventName);
			isEnabled = true;
		}
	};

	// Register as a jQuery function
	$.fn.keyboardFormSubmit = function(action) {
		return this.each(function(){
			keyboardFormSubmit(this, action);
		});
	};

	// Automatically activate using the form's data- attribute.
	$('form['+formAttr+']').keyboardFormSubmit();

})(jQuery);