(function($){
	var defaults = {
		type: 'disable',
		formAttribute: 'data-keyboard-submit',
		eventNamespace: '.CP.form.keyboard-submit',
	};

	var formAttr = defaults.formAttribute;

	var keyboardFormSubmit = function(form, type) {
		var isEnabled;
		var eventName = 'keypress'+defaults.eventNamespace;

		if (typeof form.jquery === 'undefined') //not a jquery object
			form = $(form);

		type = (typeof type !== 'undefined') ? type : form.attr(formAttr) ? form.attr(formAttr) : defaults.type;


		if (type === 'enable' || type === '1' || type === 'true')
			enable();
		else if (type === 'disable' || type === '0' || type === 'false' || type === '')
			disable();


		function disable() {
			form.off(eventName).on(eventName,function(e) {
				if(e.which === 13) { //enter
					e.preventDefault();
					return false;
				}
			});
			isEnabled = false;
		}
		function enable() {
			form.off(eventName);
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