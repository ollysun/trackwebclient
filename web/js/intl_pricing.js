/**
 * Created by RotelandO on 8/5/15.
 */

var Billing = {

    data: {},

    Url: {
        'save': '/intlbilling/save',
        'delete': '/intlbilling/delete',
        'fetchbyid': '/intlbilling/fetchbyid'
    },

    newBillingObject: function() {
       return {
            zone_id: '',
            zone_name: '',
            parcel_type_id: '',
            parcel_type_name: '',
            weight_range_id: '',
            weight_range_name: '',
            base_cost: '',
           increment_cost:0
       };
    },

    getBillingFormData: function() {
        var billing = this.newBillingObject();
        var id = $('#id').val();
        if(id) {
            billing.id = id;
        }
        billing.zone_id = $('#zone').val();
        billing.zone_name = $('#zone').find('option:selected').text();
        billing.parcel_type_id = $('#parcel_type').val();
        billing.parcel_type_name = $('#parcel_type').find('option:selected').text();
        billing.weight_range_id = $('#weight_range').val();
        billing.weight_range_name = $('#weight_range').find('option:selected').text();
        billing.base_cost = $('#base_cost').val();
        return billing;
    },

    setBillingFormData: function(billing) {
        $('#id').val(billing.id);
        $('#zone').val(billing.zone_id);
        $('#weight_range').val(billing.weight_range_id);
        $('#parcel_type').val(billing.parcel_type_id);
        $('#base_cost').val(billing.base_cost);
        return billing;
    },

    getServiceTypeText: function(parcel_type_id){
        switch (parcel_type_id){
            case 1: return 'Express Documents';
            case 2: return 'Express Non-Documents';
            case 3: return 'Economy Express';
        }
        return '';
    },

    clearBillingFormData: function() {
        $('#id').val('');
        $('#zone').attr('disabled', false);
        $('#weight_range').attr('disabled', false);
        $('#zone').val('');
        $('#parcel_type_id').val('');
        $('#weight_range').val('');
        $('#base_cost').val('');

    },

    buildRowMarkUp: function(billing) {
        var row = '<tr data-row-id="' + billing.id + '">';
        row += '<td>' + billing.weight_range_name + ' Kg.</td>';
        row += '<td>' + billing.zone_name + '</td>';
        row += '<td>' + billing.parcel_type_name + '</td>';
        row += '<td>' + billing.base_cost + '</td>';
        row += '</tr>';
        return row;
    },

    saveBilling: function(billing) {
        var self = this;
        $.ajax({
            url: this.Url.save,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(billing),
            success: function(result) {
                if(result.status == 'success') {
                    billing.id = result.data.id;
                    self.data[billing.id] = billing;
                    var row = self.buildRowMarkUp(billing);
                    $('#dlg_tbl_pricing').append(row);
                    self.clearBillingFormData();
                } else {
                    alert(result.message);
                }
            },
            error: function(err) {
                console.log(err);
            },
            complete: function(jqXHR) {

            }
        })
    },

    deleteBilling: function(id) {
        var self = this;
        $.ajax({
            url: this.Url.delete,
            type: 'GET',
            dataType: 'JSON',
            data: 'id='+id,
            success: function(result) {
                if(result.status == 'success') {
                    document.location.reload();
                } else {
                    alert(result.message);
                }
            },
            error: function(err) {
                console.log(err);
            },
            complete: function(jqXHR) {

            }
        })
    },

    fetchBillingById: function(id) {
        var self = this;
        return $.ajax({
            url: this.Url.fetchbyid,
            type: 'GET',
            dataType: 'JSON',
            data: 'id='+id,
            success: function(result) {
                if(result.status == 'success') {
                    var billing = self.newBillingObject();
                    billing.id = result.data.id;
                    billing.base_cost = result.data.base_amount;
                    billing.zone_id = result.data.zone_id;
                    billing.zone_name = result.data.zone.name;
                    billing.weight_range_id = result.data.weight_range_id;
                    billing.weight_range_name = result.data.weight_range.min_weight + ' - ' + result.data.weight_range.max_weight;
                    billing.parcel_type_id = result.data.parcel_type_id;
                    billing.parcel_type_name = self.getServiceTypeText(result.data.parcel_type_id);
                    self.setBillingFormData(billing);
                } else {
                    alert(result.message);
                }
            },
            error: function(err) {
                console.log(err);
            },
            complete: function(jqXHR) {

            }
        })
    }
};

$(document).ready(function(){

    $('#add_billing').on('click', function(evt){
        removeValidateMsg('#billing-form');
        Billing.clearBillingFormData();
        $('#modal_pricing').modal('show');
    });

    $('.edit_billing').on('click', function(evt){
        var btn = $(this);
        btn.prop('disabled', true);
        var id = btn.attr('data-weight-billing-id');
        $('#id').val(id);
        $('#zone').attr('disabled', true);
        $('#weight_range').attr('disabled', true);
        $('#parcel_type').attr('disabled', true);
        removeValidateMsg('#billing-form');
        Billing.fetchBillingById(id).then(function(result){
            if (result.status === 'success') {
                $('#modal_pricing').modal('show');
            }
            btn.prop('disabled', false);
        });
    });

    $('.del_billing').on('click', function(evt){
        var id = $(this).attr('data-weight-billing-id');
        $('#id').val(id);
        var resp = confirm('This action will remove this billing. Remove?');
        if (resp == true) {
            Billing.deleteBilling(id);
        }
    });

    $('#save_billing').on('click', function(evt){
        var formIsValid = validate('#billing-form');
        if (formIsValid) {
            var billing = Billing.getBillingFormData();
            Billing.saveBilling(billing);
        }
    });

    $('#refresh').on('click', function(evt){
        document.location.reload();
    });
});