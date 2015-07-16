(function($){
//Initialize the carousel
$('#newParcelForm').carousel('pause');
$('#newParcelForm').on('slide.bs.carousel', function (event) {
	$("html, body").animate({scrollTop:0},'fast');

	var direction = event.direction;
	if(direction=='left'){
		return validate('.carousel-inner > .item.active');
	}
	return true;
});

function validate($parent)
{
	$($parent+' .has-error .help-block').remove();
	$($parent+' .has-error').removeClass('has-error');
	$($parent+' .has-success').removeClass('has-success');
	var hasError = false;

	$($parent+' .validate').each(function()
	{
		var msg = '';
		var val = jQuery.trim($(this).val());

		if($(this).hasClass('required') && val == '')
		{
			msg = 'Required field';
			hasError = true;
		}
		else if($(this).hasClass('email'))
		{
			var em = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if(!em.test(val))
			{
				msg = 'Invalid entry';
				hasError = true;
			}
		}
		else if($(this).hasClass('integer'))
		{
			var test = /^[-+]?\d+$/;
			if(!test.test(val))
			{
				msg = 'Invalid entry';
				hasError = true;
			}
		}
		else if($(this).hasClass('number'))
		{
			var ph = /^[0-9]+(\.[0-9][0-9]?)?$/;
			if(!ph.test(val))
			{
				msg = 'Invalid entry';
				hasError = true;
			}
		}
		else if($(this).hasClass('phone'))
		{
			var ph = /^(234|0)[0-9]{10}$/;
			if(!ph.test(val))
			{
				msg = 'Invalid entry';
				hasError = true;
			}
		}
		else if($(this).hasClass('match'))
		{
			var $match = ($parent+' '+$(this).attr('match'));
			if($($match).val()!=val)
			{
				msg = 'Entries mismatch';
				hasError = true;
			}
		}
		else if($(this).find("input[type=radio]").length>0 && $(this).find("input[type=radio]:checked").length==0)
		{
			msg = 'Required field';
			hasError = true;
		}
		if(msg != ''){
			if($(this).parent().hasClass('input-group')){
				$(this).parent().parent().append('<div class="help-block no-margin clearfix">'+msg+'</div>');
				$(this).parent().parent().addClass('has-error');
			}
			else{
				$(this).parent().append('<div class="help-block no-margin clearfix">'+msg+'</div>');
				$(this).parent().addClass('has-error');
			}
		}
		else{
			$(this).parent().removeClass('has-error').addClass('has-success');
		}
	});
	if(!hasError)
	{
		return true;
	}
	return false;
}

$("form.validate").submit(function(event) {
	alert($(this).hasClass('validate'));
	event.preventDefault();
/*	if(validate($(this)) === true ) {
		return;
	}*/
});


var deliveryShowHide = {
	who: '#pickUpWrap',
	options: {
		identifier: 'input[name="delivery_type"]',
		mapping: {
			'1': true,
			'2': false
		}
	},
	callback: function(ele, val, who) {
		console.log('ele', ele);
		console.log('val '+val);
	}
};
var CODShowHide = {
	who: '#CODAmountWrap',
	options: {
		identifier: 'input[name="cash_on_delivery"]',
		mapping: {
			'true': true,
			'false': false
		},
	},
	callback: function(ele, val, who) {
		console.log('ele', ele);
		console.log('val '+val);
		if (val === 'false') {
			$('#CODAmount').val('');
			$('input[name="CODAmount"]').removeClass('required number').parent().removeClass('has-error');;
		}
		else {
			$('input[name="CODAmount"]').addClass('required number');
		}
	}
};
var merchantShowHide = {
	who: '#bank-account-details',
	options: {
		identifier: 'input[name="merchant"]',
		mapping: {
			'new': true,
			'old': true,
			'none': false
		},
	},
	callback: function(ele, val, who) {
		if (val === 'none') {
			$('#cODNo').trigger('click');
			$('input[name="account_name"], input[name="account_no"], select[name="bank"]').removeClass('required').parent().removeClass('has-error');
			$('input[name="account_no"]').removeClass('number');
		}
		else {
			$('input[name="account_name"], input[name="account_no"], select[name="bank"]').addClass('required');
			$('input[name="account_no"]').addClass('number');
		}
	}
};
var paymentMethodShowHide = {
	who: '#cashPOSAmountWrap',
	options: {
		identifier: 'input[name="payment_method"]',
		mapping: {
			'3': true,
			'1': false,
			'2': false
		},
	},
	callback: function(ele, val, who) {
		console.log('ele', ele);
		console.log('val '+val);
		if (val === '3') {
			$('input[name="amount_in_cash"], input[name="amount_in_pos"]').removeClass('required number').parent().removeClass('has-error');;
		}
		else {
			$('input[name="amount_in_cash"], input[name="amount_in_pos"]').addClass('required number');
		}
	}
};
showHideWrap(deliveryShowHide);
showHideWrap(CODShowHide);
showHideWrap(merchantShowHide);
showHideWrap(paymentMethodShowHide);



})(jQuery);

function showHideWrap(object) {
	showHide(object.who, object.options, object.callback);
}
function showHide(who, options, callback, evt) {
	var self = this;
	if(typeof evt === 'undefined' || !evt)
		evt = 'change';

	var trigger = $(options.identifier);
	var mapping = options.mapping;
	if (!mapping.hasOwnProperty('default'))
		mapping['default'] = false;

	trigger.on(evt, triggerCallback);

	// trigger the callback on document ready
	triggerCallback.apply($(options.identifier+':checked'));

	function triggerCallback(){
		var val = $(this).val();
		var show;
		if(mapping.hasOwnProperty(val)) {
			show = mapping[val];
		}
		else {
			show = mapping['default'];
		}
		if (show) {
			$(who).removeClass('hidden');
		}
		else {
			$(who).addClass('hidden');
		}
		if(typeof callback === 'function')
			callback.apply(self,[this, val, who]);
	}
}

var hello = new FlyOutPanel('#shipperAddressFlyOutPanelTrigger');
var hello2 = new FlyOutPanel('#receiverAddressFlyOutPanelTrigger');

// Hide trigger link
$('#shipperAddressFlyOutPanelTrigger,#receiverAddressFlyOutPanelTrigger').addClass('hidden');

// var hello3 = new FlyOutPanel('#shipperSearchBox', 'keypress');
// var hello4 = new FlyOutPanel('#receiverSearchBox', 'keypress');

function FlyOutPanel (triggerSelector, evt) {
	var toggleClass = 'open';
	var trigger = $(triggerSelector);
	var panel = $(trigger.attr('data-target'));

	//close on init
	closePanel();

	if (evt && evt !== 'custom') { // for other events, 'custom' keyword means opening would be handled manually
		trigger.on(evt, openPanel);
	}
	else {
		trigger.on('click', openPanel);
	}
	panel.find('.close').on('click', closePanel);

	this.open = function() {
		openPanel();
	};
	this.close = function() {
		closePanel();
	};
	this.toggle = function() {
		togglePanel();
	};

	function openPanel() {
		panel.addClass(toggleClass);
	}
	function closePanel() {
		panel.removeClass(toggleClass);
	}
	function togglePanel() {
		panel.toggleClass(toggleClass);
	}
}

var Parcel = {

	newUserObject: function() {
		return {
			firstname: '',
			lastname: '',
			email: '',
			phonenumber: '',
			street1: '',
			street2: '',
			city: '',
			country: '',
			state: ''
		}
	},

	Url: {
		'states' : '/site/getstates',
		'userdetails' : '/site/userdetails'
	},

	getStates: function(country_id, selectSelector) {
		$.get( Parcel.Url.states, { id: country_id }, function(response){
			if(response.status === 'success') {
				var html = '';
				$.each(response.data, function(i, item){
					html += "<option value='" + item.id + "'>" + item.name.toUpperCase() + "</option>";
				});
				$(selectSelector).attr('disabled', false);
				$(selectSelector).html(html);
			}
		});
	},

	getUserInformation: function(term, suffix) {
		var self = this;
		$.get( Parcel.Url.userdetails, { term: term }, function(response) {
			if(response.status === 'success') {

				var userObj = self.newUserObject();
				userObj.id = response.data.id;
				userObj.firstname = response.data.firstname;
				userObj.lastname = response.data.lastname;
				userObj.email = response.data.email;
				userObj.phone = response.data.phone;
				userObj.address = response.data.address;

				self.setUserDetails(userObj, suffix);
			}
		});
	},

	setUserDetails: function(userObj, suffix) {

		$('#firstname_' + suffix).val(userObj.firstname);
		$('#lastname_' + suffix).val(userObj.lastname);
		$('#email_' + suffix).val(userObj.email);
		$('#phone_' + suffix).val(userObj.phone);
		/*$('#address_' + suffix + '_1').val();
		$('#address_' + suffix + '_2').val();
		$('#city_' + suffix).val();
		$('#country_' + suffix).val();
		$('#state_' + suffix).val();*/
	}
};
$(document).ready(function(){

	$('#country_shipper, #country_receiver').on('change', function(evt) {

		var country_id = $(this).val();
		var elementName = $(this).attr('name');
		var selector = elementName.indexOf('shipper') !== -1 ? '#state_shipper' : '#state_receiver';
		Parcel.getStates(country_id, $(selector));
	});

	$('#btn_Search_shipper, #btn_Search_receiver').on('click', function(event){

		var suffix = '';
		if($(this).hasClass('shipper')) {
			suffix = 'shipper';
		} else {
			suffix = 'receiver';
		}
		var term = $("#" + suffix + "SearchBox").val();
		Parcel.getUserInformation(term, suffix);
	});
});