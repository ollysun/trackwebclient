(function($){
//Initialize the carousel
$('#newParcelForm').carousel('pause');
$('#newParcelForm').on('slide.bs.carousel', function () {
	$("html, body").animate({scrollTop:0},'fast');
	return true;
});

var deliveryShowHide = {
	who: '#pickUpWrap',
	options: {
		identifier: 'input[name="delivery_type"]',
		mapping: {
			'centre': true,
			'address': false
		},
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
		}
	}
};
var paymentMethodShowHide = {
	who: '#cashPOSAmountWrap',
	options: {
		identifier: 'input[name="payment_method"]',
		mapping: {
			'cash_pos': true,
			'cash': false,
			'pos': false
		},
	},
	callback: function(ele, val, who) {
		console.log('ele', ele);
		console.log('val '+val);

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

var hello3 = new FlyOutPanel('.shipperSearchFlyOutPanelTrigger', 'keypress');
var hello4 = new FlyOutPanel('.receiverSearchFlyOutPanelTrigger', 'keypress');

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