/**
 * Created by ELACHI on 10/29/2016.
 */


var Parcel = {
    onFormErrorCallback: function (code, payload) {
        console.log(payload);
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
        'calcbilling': '/parcels/calculatebilling',
        'qetquote': '/parcels/getquote'
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
                    $('.amount-due').html("Unable to calculate amount...");
                }
            },
            error: function (err) {
                console.log(err);
            },
            complete: function (jqXHR) {
                if (amount != 0 && !amount) {
                    $('.amount-due').html("Unable to calculate amount...");
                }
            }
        })
    },

    qetQuote: function(params){
        $('#quote').hide();
        $('#calculating_info').show().html('calculating...');

        var quote = null;


        $.ajax({
            url: this.Url.qetquote,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(params),
            success: function (result) {
                if (result.status == 'success') {
                    quote = result.data;

                    console.log(quote);

                    $('#total_amount').text(quote.total_amount);
                    $('#discount').text(quote.discount);
                    $('#discount_percentage').text(quote.discount_percentage);
                    $('#gross_amount').text(quote.gross_amount);
                    $('#amount_due').text(quote.amount_due);
                    $('#vat').text(quote.vat);
                    $('#calculating_info').hide();
                    $('#quote').show();
                } else {
                    alert(result.message);
                    $('#calculating_info').show().html('Unable to calculate amount...');
                    $('#quote').hide();
                }
            },
            error: function (err) {
                console.log(err);
            },
            complete: function (jqXHR) {
                if (quote == null) {
                    $('#calculating_info').html("Unable to calculate amount...").show();
                    $('#quote').hide();
                }
            }
        })
    }

};

$("#company").change(function () {
    var companyId = $(this).val();
    $("#billing_plan").html("<option value='' selected>Select Company</option>");
    $(".amount-due").html("0.00");

    if (typeof billingPlans != "undefined" && companyId != "") {
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

$('#originating_state').on('change', function (evt) {
    $(".amount-due").html("0.00");
    var state_id = $(this).val();
    Parcel.getCities(state_id, '#originating_city');
});


$('#destination_state').on('change', function (evt) {
    $(".amount-due").html("0.00");
    var state_id = $(this).val();
    Parcel.getCities(state_id, '#destination_city');
});

$('#btncalculate').click(function(){
    var params = {};
    params.from_branch_id = $('#originating_city').find('option:selected').attr('data-branch-id');
    params.to_branch_id = $('#destination_city').find('option:selected').attr('data-branch-id');
    params.city_id = $('#destination_city').find('option:selected').attr('data-city_id');
    params.weight = $('#weight').val();
    var billingField = $("#billing_plan");
    if (billingField.val() != '') {
        params.weight_billing_plan_id = billingField.val();
        params.onforwarding_billing_plan_id = billingField.val();
    }
    Parcel.qetQuote(params);
})
