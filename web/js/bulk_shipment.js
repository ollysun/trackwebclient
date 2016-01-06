var BulkShipment = {

    Constants: {
        bulk_shipment_modal: $('#bulk_shipment_modal'),
        create_bulk_shipment_btn: $('#create_bulk_shipment_btn'),
        bulk_shipment_modal_body: $('#bulk_shipment_modal_body'),
        company_select: $('#company_select'),
        company_billing_plan_select: $('#company_billing_plan_select')
    },

    /**
     * Intialize
     */
    init: function () {
        this.Constants.bulk_shipment_modal.unbind('hide.bs.modal');
        this.Constants.company_select.on('change', function () {
            if (BulkShipment.Constants.company_select.val() == "") {
                BulkShipment.Constants.company_billing_plan_select.html('<option>Select a Billing Plan</option>');
                return true;
            }
            var selected_company = BulkShipment.Constants.company_select.find('option:selected');
            var billing_plans = selected_company.data('billing_plans');
            if (billing_plans.length == 0) {
                BulkShipment.Constants.company_billing_plan_select.html('<option>Company has no billing plan</option>');
                return true;
            }
            BulkShipment.populateBillingPlansDropDown(billing_plans);
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
    }
};

$(document).ready(function () {
    BulkShipment.init();
});