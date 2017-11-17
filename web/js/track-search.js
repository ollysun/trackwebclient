(function($){
	var form = $('#track-search-form');
	var input = $(form.find('input[name="query"]'));
	var formGroup = input.closest('.form-group');

	form.submit(function(){
		return validateInput();
	});
	function validateInput() {
		removeValidationMsg();
		var val = input.val();
		var arr = val.split(',');
		var isValid = ( arr.length <= 10 );
		if (!isValid) {
			addValidationMsg('You have more than the required shipments, please reduce to a maximum of 10');
		}
		return isValid;
	}
	function addValidationMsg(message) {
		var msgEle = $('<span></span>')
			.addClass('help-block validation-msg')
			.html(message);
		formGroup.addClass('has-error').append(msgEle);
		if (form.hasClass('navbar-form')) { //header-form
			msgEle.addClass('text-right');
		}
	}
	function removeValidationMsg() {
		formGroup.removeClass('has-error').remove('.validation-msg');
	}
})(jQuery);