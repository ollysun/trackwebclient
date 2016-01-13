var BulkShipment;
BulkShipment = {

    uploadedFile: null,

    Constants: {
        bulk_shipment_modal: $('#bulk_shipment_modal'),
        create_bulk_shipment_btn: $('#create_bulk_shipment_btn'),
        bulk_shipment_modal_body: $('#bulk_shipment_modal_body'),
        company_select: $('#company_select'),
        company_billing_plan_select: $('#company_billing_plan_select'),
        bulk_upload_btn: $('#bulk_upload_btn'),
        bulk_upload_file_btn: $('#bulk_upload_file_btn'),
        uploaded_file_name_span: $('#uploaded_file_name'),
        modal_create_btn: $('#modal_create_btn'),
        url_create_bulk_shipment: 'createbulkshipment',
        bulk_shipment_form: $('#bulk_shipment_form'),
        company_id_input: $('#company_id_input'),
        billing_plan_id_input: $('#billing_plan_id_input'),
        payment_type_input: $('#payment_type_input'),
        payment_method_select: $('#payment_method_select')
    },

    /**
     * Initialize
     */
    init: function () {
        $('#message_area').hide();
        this.Constants.modal_create_btn.prop('disabled', true);

        this.Constants.bulk_shipment_modal.unbind('hide.bs.modal');

        this.Constants.company_select.on('change', function () {
            if (BulkShipment.Constants.company_select.val() == "") {
                BulkShipment.Constants.modal_create_btn.prop('disabled', true);
                BulkShipment.Constants.company_billing_plan_select.html('<option>Select a Billing Plan</option>');
                return true;
            }
            var selected_company = BulkShipment.Constants.company_select.find('option:selected');
            var billing_plans = selected_company.data('billing_plans');
            if (billing_plans.length == 0) {
                BulkShipment.Constants.modal_create_btn.prop('disabled', true);
                BulkShipment.Constants.company_billing_plan_select.html('<option>Company has no billing plan</option>');
                return true;
            }
            BulkShipment.populateBillingPlansDropDown(billing_plans);
            if (BulkShipment.uploadedFile != null) {
                BulkShipment.Constants.modal_create_btn.prop('disabled', false);
            }
        });

        this.Constants.bulk_upload_btn.unbind('click').click(function () {
            BulkShipment.Constants.bulk_upload_file_btn.click();
            return false;
        });

        this.Constants.bulk_upload_file_btn.on('change', function (e) {
            if (e.target.files.length == 0) {
                BulkShipment.Constants.uploaded_file_name_span.html('');
                BulkShipment.Constants.modal_create_btn.prop('disabled', false);
                return false;
            }
            BulkShipment.uploadedFile = e.target.files[0];
            BulkShipment.Constants.uploaded_file_name_span.html('Data File: ' + '<strong>' + BulkShipment.uploadedFile.name + '</strong>');
            BulkShipment.Constants.modal_create_btn.prop('disabled', false);
        });

        this.Constants.modal_create_btn.unbind('click').click(function () {
            $(this).text('Working...');
            $(this).prop('disabled', true);
            var _this = $(this);
            BulkShipment.Constants.company_id_input.val(BulkShipment.Constants.company_select.val());
            BulkShipment.Constants.billing_plan_id_input.val(BulkShipment.Constants.company_billing_plan_select.val());
            BulkShipment.Constants.payment_type_input.val(BulkShipment.Constants.payment_method_select.val());

            BulkShipment.Constants.bulk_shipment_form.ajaxSubmit({
                success: function (response) {
                    if (response.status == TrackPlusUtil.ResponseConstants.error) {
                        BulkShipment.showMessage(response.message);
                    } else if (response.status == TrackPlusUtil.ResponseConstants.success) {
                        BulkShipment.showMessage('Shipments have been queued for creation. View Progress', false);
                    }
                },
                error: function (e) {
                    //BulkShipment.showMessage('Something went wrong while creating bulk shipment. Please try again');
                },
                complete: function(){
                    _this.text('Create');
                    _this.prop('disabled', false);
                },
                dataType: 'json',

                url: BulkShipment.Constants.url_create_bulk_shipment
            })
            ;
            return false;
        });
    },
    /**
     * Populate billing plans dropdown with billing plans
     * @param billing_plans
     */
    populateBillingPlansDropDown: function (billing_plans) {
        var selectOptions = '';
        for (var billing_plan_id in billing_plans) {
            selectOptions += '<option value="' + billing_plan_id + '">' + billing_plans[billing_plan_id].toUpperCase() + '</option>';
        }
        this.Constants.company_billing_plan_select.html(selectOptions);
    },

    showMessage: function (message, is_error) {
        TrackPlusUtil.showMessage($('#message_area'), message, is_error);
    }
};

$(document).ready(function () {
    BulkShipment.init();
});