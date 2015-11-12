// Old browser support (IE especially)
if (!Array.isArray) {
    Array.isArray = function (arg) {
        return Object.prototype.toString.call(arg) === '[object Array]';
    };
}
function getServerResponse(statusCode, message) {
    alert(message);
    switch (statusCode) {
        case '0':
            // Handle error here
            window.location.href = message;
            break;
        default :
            //Navigate here
            alert(message);
            break;

    }
}
(function ($) {
//Initialize the carousel
    $('#newParcelForm').carousel('pause').off('keydown.bs.carousel');
    $('#newParcelForm').on('slide.bs.carousel', function (event) {
        $("html, body").animate({scrollTop: 0}, 'fast');

        var direction = event.direction;
        var isValidate = true;
        if (direction == 'left') {
            isValidate = validate('.carousel-inner > .item.active');
            if (isValidate && canCalculateAmount()) {
                calculateAmount();
            }
        }
        return isValidate;
    });

    function canCalculateAmount() {
        var from_branch_id = $('#city_shipper').find('option:selected').attr('data-branch-id');
        if (!from_branch_id) {
            return false;
        }

        var to_branch_id = $('#city_receiver').find('option:selected').attr('data-branch-id');
        if (!to_branch_id) {
            return false;
        }

        var city_id = $('#city_receiver').find('option:selected').attr('data-city_id');
        if (!city_id) {
            return false;
        }

        var weight = $('#weight').val();
        if (!weight && !isNaN(weight)) {
            return false;
        }

        return true;
    }

    function calculateAmount() {
        var params = {};
        params.from_branch_id = $('#city_shipper').find('option:selected').attr('data-branch-id');
        params.to_branch_id = $('#city_receiver').find('option:selected').attr('data-branch-id');
        params.city_id = $('#city_receiver').find('option:selected').attr('data-city_id');
        params.weight = $('#weight').val();
        var onforwardingBillingField = $("#onforwarding_billing_plan");
        var weightBillingField = $("#onforwarding_billing_plan");
        if (weightBillingField.is(":visible") && onforwardingBillingField.is(":visible")) {
            params.weight_billing_plan_id = weightBillingField.val();
            params.onforwarding_billing_plan_id = onforwardingBillingField.val();
        }
        Parcel.calculateAmount(params);
    }

    var CODShowHide = {
        who: '#CODAmountWrap',
        options: {
            identifier: 'input[name="cash_on_delivery"]',
            mapping: {
                'true': true,
                'false': false
            },
        },
        callback: function (ele, val, who) {
            if (val === 'false') {
                $('#CODAmount').val('');
                $('input[name="CODAmount"]').removeClass('validate required non-zero-number').parent().removeClass('has-error');
                ;
            }
            else {
                $('input[name="CODAmount"]').addClass('validate required non-zero-number');
            }
        }
    };
    var merchantShowHide = {
        who: '#bank-account-details',
        options: {
            identifier: 'input[name="merchant"]',
            mapping: {
                'yes': true,
                'no': false
            },
        },
        callback: function (ele, val, who) {
            if (val === 'none') {
                $('#cODNo').trigger('click');
                $('input[name="account_name"], input[name="account_no"], select[name="bank"]')
                    .removeClass('required validate active-validate')
                    .off('blur.CP.form.validate')
                    .parent().removeClass('has-error');
                $('input[name="account_no"]').removeClass('integer length');
                $('input[name="account_name"]').removeClass('name');
            }
            else {
                /*$('input[name="account_name"], input[name="account_no"], select[name="bank"]')
                 .addClass('required validate active-validate')
                 .on('blur.CP.form.validate', validateFxn);
                 $('input[name="account_no"]').addClass('integer length');
                 $('input[name="account_name"]').addClass('name');*/
            }
        }
    };
    var paymentMethodShowHide = {
        who: '#cashPOSAmountWrap',
        options: {
            identifier: 'input[name="payment_method"]',
            mapping: {
                '1': false,
                '2': false,
                '3': true,
                '4': false
            },
        },
        callback: function (ele, val, who) {
            if (val !== '3') {
                $('input[name="amount_in_cash"], input[name="amount_in_pos"]').removeClass('validate required number').parent().removeClass('has-error');
                ;
            }
            else {
                $('input[name="amount_in_cash"], input[name="amount_in_pos"]').addClass('validate required number');
            }
        }
    };
    var POSIDShowHide = {
        who: '#POSIDWrap',
        options: {
            identifier: 'input[name="payment_method"]',
            mapping: {
                '1': false,
                '2': true,
                '3': true,
                '4': false
            },
        },
        callback: function (ele, val, who) {
            if (val === '1' || val === '4') {
                $('input[name="pos_transaction_id"]').removeClass('validate required').removeClass('has-error');
                ;
            }
            else {
                $('input[name="pos_transaction_id"]').addClass('validate required');
            }
        }
    };
    var HubShowHide = {
        who: '#hubsWrap',
        options: {
            identifier: 'input[name="send_to_hub"]',
            mapping: {
                '0': false,
                '1': true
            },
        },
        callback: function (ele, val, who) {
            if (val === '1') {
                $('select[name="to_branch_id"]').addClass('validate required').prop('disabled', '');
            }
            else {
                $('select[name="to_branch_id"]').removeClass('validate required').removeClass('has-error').prop('disabled', 'disabled');
            }
        }
    };
//showHideWrap(deliveryShowHide);
    showHideWrap(CODShowHide);
    showHideWrap(merchantShowHide);
    showHideWrap(paymentMethodShowHide);
    showHideWrap(POSIDShowHide);
    showHideWrap(HubShowHide);


})(jQuery);

function showHideWrap(object) {
    showHide(object.who, object.options, object.callback);
}
function showHide(who, options, callback, evt) {
    var self = this;
    if (typeof evt === 'undefined' || !evt)
        evt = 'change';

    var trigger = $(options.identifier);
    var mapping = options.mapping;
    if (!mapping.hasOwnProperty('default'))
        mapping['default'] = false;

    trigger.on(evt, triggerCallback);

    // trigger the callback on document ready
    triggerCallback.apply($(options.identifier + ':checked'));

    function triggerCallback() {
        var val = $(this).val();
        var show;
        if (mapping.hasOwnProperty(val)) {
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
        if (typeof callback === 'function')
            callback.apply(self, [this, val, who]);
    }
}

var hello = new FlyOutPanel('#shipperAddressFlyOutPanelTrigger');
var hello2 = new FlyOutPanel('#receiverAddressFlyOutPanelTrigger');

// Hide trigger link
$('#shipperAddressFlyOutPanelTrigger,#receiverAddressFlyOutPanelTrigger').addClass('hidden');

// var hello3 = new FlyOutPanel('#shipperSearchBox', 'keypress');
// var hello4 = new FlyOutPanel('#receiverSearchBox', 'keypress');

function FlyOutPanel(triggerSelector, evt) {
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

    this.open = function () {
        openPanel();
    };
    this.close = function () {
        closePanel();
    };
    this.toggle = function () {
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
    onFormErrorCallback: function (code, payload) {
        //Handler as sent from the server
        alert(payload.message);
    },
    onFormSuccessCallback: function (code, payload) {
        $(window).trigger('success.CP.Form.watchChanges');
        var waybill_number;
        if (Array.isArray(payload.waybill_number)) {
            var split_waybill_number = payload.waybill_number[0];
            var waybill_number_parts = split_waybill_number.split('-');
            waybill_number = waybill_number_parts[0];
        } else {
            waybill_number = payload.waybill_number;
        }
        window.location = "/shipments/view?waybill_number=" + waybill_number;
    },
    newUserObject: function () {
        return {
            id: '',
            firstname: '',
            lastname: '',
            email: '',
            phonenumber: '',
            address: null
        }
    },

    newAddress: function () {
        return {
            street_address1: '',
            street_address2: '',
            city_id: '',
            state_id: '',
            country_id: ''
        }
    },

    newAccountObject: function () {
        return {
            name: '',
            number: '',
            bank: '',
            id: ''
        }
    },

    Url: {
        'states': '/parcels/getstates',
        'cities': '/parcels/getcities',
        'userdetails': '/parcels/userdetails',
        'accountdetails': '/parcels/accountdetails',
        'calcbilling': '/parcels/calculatebilling'
    },

    getStates: function (country_id, selectSelector, selectedValue) {
        $.get(Parcel.Url.states, {id: country_id}, function (response) {
            if (response.status === 'success') {
                var html = '<option value="">Select State...</option>';
                var selected = '';
                $.each(response.data, function (i, item) {

                    selected = (selectedValue == item.id) ? 'selected="selected"' : '';
                    html += "<option value='" + item.id + "' " + selected + ">" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).attr('disabled', false);
                $(selectSelector).html(html);
            }
        });
    },

    getCities: function (state_id, selectSelector, selectedValue) {
        $.get(Parcel.Url.cities, {id: state_id}, function (response) {
            if (response.status === 'success') {
                var html = '<option value="">Select City...</option>';
                var selected = '';
                $.each(response.data, function (i, item) {

                    selected = (selectedValue == item.id) ? 'selected="selected"' : '';
                    html += "<option value='" + item.id + "' data-branch-id='" + item.branch_id + "' data-city_id='" + item.id + "' " + selected + ">" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).attr('disabled', false);
                $(selectSelector).html(html);
            }
        });
    },

    getUserInformation: function (term, suffix) {
        var self = this;
        return $.get(Parcel.Url.userdetails, {term: term}, function (response) {
            if (response.status === 'success') {

                var userObj = self.newUserObject();
                userObj.id = response.data.id;
                userObj.firstname = response.data.firstname;
                userObj.lastname = response.data.lastname;
                userObj.email = response.data.email;
                userObj.phone = response.data.phone;
                if (response.data.address) {
                    userObj.address = response.data.address;
                } else {
                    userObj.address = self.newAddress();
                }

                self.setUserDetails(userObj, suffix);
            }
            else {
                alert(response.message);
            }
        });
    },

    setUserDetails: function (userObj, suffix) {

        $('#id_' + suffix).val(userObj.id);
        $('#firstname_' + suffix).val(userObj.firstname);
        $('#lastname_' + suffix).val(userObj.lastname);
        $('#email_' + suffix).val(userObj.email);
        $('#phone_' + suffix).val(userObj.phone);

        //Set address information
        $('#address_' + suffix + '_1').val(userObj.address.street_address1);
        $('#address_' + suffix + '_2').val(userObj.addressstreet_address2);
        $('#country_' + suffix).val(userObj.address.country_id);
        var stateSelector = $('#state_' + suffix);
        if (userObj.address.country_id !== '') {
            this.getStates(userObj.address.country_id, stateSelector, userObj.address.state_id);
        } else {
            $(stateSelector).attr('disabled', true);
        }

        var citySelector = $('#city_' + suffix);
        if (userObj.address.state_id !== '') {
            this.getCities(userObj.address.state_id, citySelector, userObj.address.city_id);
        }
    },

    getAccountDetails: function (owner_id) {
        var self = this;
        $.get(Parcel.Url.accountdetails, {owner_id: owner_id}, function (response) {

            var accountObj = self.newAccountObject();
            if (response.status === 'success') {

                if (response.data.length !== 0) {
                    accountObj.id = response.data.id;
                    accountObj.name = response.data.account_name;
                    accountObj.number = response.data.account_no;
                    accountObj.bank = response.data.bank;
                }
                else {
                    alert('No bank records found.');
                }
            }
            else {
                alert(response.message);
            }
            self.setAccountDetails(accountObj);
        });
    },

    setAccountDetails: function (accountObj) {
        $('#account_id').val(accountObj.id);
        $('#account_name').val(accountObj.name);
        $('#account_no').val(accountObj.number);
        $('#bank').val(accountObj.bank.id);
    },

    calculateAmount: function (params) {

        $('.amount-due').html("calculating...");
        var amount = '';
        $('#amount').val(amount);

        $.ajax({
            url: this.Url.calcbilling,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(params),
            success: function (result) {
                if (result.status == 'success') {
                    amount = result.data;
                    $('.amount-due').text(amount);
                    $('input#amount').val(amount);
                    $('input#corporate_amount').val(amount);
                } else {
                    alert(result.message);
                }
            },
            error: function (err) {
                console.log(err);
            },
            complete: function (jqXHR) {
                if (!amount) {
                    $('.amount-due').html("Unable to calculate amount...");
                }
            }
        })
    }
};
$(document).ready(function () {

    //if initial data is set for cloning, run ajax calls for state and city on page load
    initializeState('shipper');
    initializeCity('shipper');
    //initialize data for receiver
    initializeState('receiver');
    initializeCity('receiver');

    function initializeState(prefix) {
        var selector = '#state_' + prefix;
        var country_id = $('#country_' + prefix).val();
        if (country_id) {
            var state_id = $(selector).attr('data-selected-id');
            Parcel.getStates(country_id, $(selector), state_id);
        }
    }

    function initializeCity(prefix) {
        var selector = '#city_' + prefix;
        var state_id = $('#state_' + prefix).attr('data-selected-id');
        if (state_id) {
            var city_id = $(selector).attr('data-selected-id');
            Parcel.getCities(state_id, $(selector), city_id);
        }
    }

    $('#country_shipper, #country_receiver').on('change', function (evt) {

        var country_id = $(this).val();
        var elementName = $(this).attr('name');
        var selector = elementName.indexOf('shipper') !== -1 ? '#state_shipper' : '#state_receiver';
        Parcel.getStates(country_id, $(selector));
    });

    $('#state_shipper, #state_receiver').on('change', function (evt) {

        var state_id = $(this).val();
        var elementName = $(this).attr('name');
        var selector = elementName.indexOf('shipper') !== -1 ? '#city_shipper' : '#city_receiver';
        Parcel.getCities(state_id, $(selector));
    });

    $('#btn_Search_shipper, #btn_Search_receiver').on('click', function (event) {

        var suffix = '';
        if ($(this).hasClass('shipper')) {
            suffix = 'shipper';
        } else {
            suffix = 'receiver';
        }
        var searchTextbox = $("#" + suffix + "SearchBox");
        if (validateFxn.apply(searchTextbox)) {
            var term = searchTextbox.val();
            Parcel.getUserInformation(term, suffix).then(function (response) {
                if (response.status !== 'success') {
                    $('#phone_' + suffix).val(searchTextbox.val());
                    $('#firstname_' + suffix).trigger('focus');
                }
                searchTextbox.val('');
            });
        }
    });

    $('#shipperSearchBox, #receiverSearchBox').on('keyup', function (e) {
        if (e.which === 13) { //enter key
            $(this).parent().find('.btn').trigger('click');
        }
    });

    $('#merchantNew').on('click', function (event) {
        Parcel.setAccountDetails(Parcel.newAccountObject());
    });

    $('input[name="send_to_hub"]').on('change', function (event) {
        if ($('input[name="send_to_hub"]:checked') == '1') {
            $('select[name="to_branch_id"]').addClass('validate required').prop('disabled', '');
        }
        else {
            $('select[name="to_branch_id"]').removeClass('validate required').removeClass('has-error').prop('disabled', 'disabled');
        }
    });

    $('#merchantOld').on('click', function (event) {
        var owner_id = $('#id_shipper').val();
        if (owner_id) {
            Parcel.getAccountDetails(owner_id);
        } else {
            alert('No Bank Account record found');
        }
    });

    var calculateBilling = function () {
        var params = {};
        params.from_branch_id = $('#city_shipper').find('option:selected').attr('data-branch-id');
        params.to_branch_id = $('#city_receiver').find('option:selected').attr('data-branch-id');
        params.city_id = $('#city_receiver').find('option:selected').attr('data-city_id');
        params.weight = $('#weight').val();
        var billingField = $("#billing_plan");
        if (billingField.is(":visible")) {
            params.weight_billing_plan_id = billingField.val();
            params.onforwarding_billing_plan_id = billingField.val();
        }
        Parcel.calculateAmount(params);
    };

    /**
     * Hides and shows billing input box
     * Depends on billing method
     */
    $("input[name='billing_method']").click(function () {

        $(".amount-due-wrap").hide();
        var val = $(this).val();
        $('#' + val + '_billing').show();
        if (val == 'manual') {
            $("input[name='manual_amount']").addClass('validate integer required');
        } else if (val == 'corporate') {
            $(".amount-due").html("");
            $("#company").trigger("change");
        } else {
            $("input[name='manual_amount']").removeClass('validate integer required');
        }
    });

    $("input[name='manual_amount']").keyup(function () {
        var _this = $(this);
        _this.val(_this.val().replace(/[^\d]/g, ''));
    });

    $("#billing_plan").change(function () {
        calculateBilling();
    });

    $("#company").change(function () {
        var companyId = $(this).val();
        $("#billing_plan").html("<option selected>Select Company</option>");
        if (typeof billingPlans != "undefined"&& companyId != "") {
            if (!billingPlans.hasOwnProperty(companyId)) {
                alert("This company does not have a billing plan");
                return false;
            }

            var selectedBillingPlans = billingPlans[companyId];
            var html = "";
            for (var planId in selectedBillingPlans) {
                html += new Option(selectedBillingPlans[planId].toUpperCase(), planId).outerHTML;
            }
            $("#billing_plan").html(html);
            $("#billing_plan").trigger("change");
        }
    });
});