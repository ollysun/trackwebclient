var BulkShipment = {

    Constants: {
        bulk_shipment_modal: $('#bulk_shipment_modal'),
        create_bulk_shipment_btn: $('#create_bulk_shipment_btn'),
        bulk_shipment_modal_body: $('#bulk_shipment_modal_body'),
        company_select: $('#company_select'),
        company_billing_plan_select: $('#company_billing_plan_select'),
        Urls: {
            bulk_shipment: 'bulkshipment'
        }
    },

    /**
     *
     */
    init: function () {
        this.Constants.create_bulk_shipment_btn.unbind('click').click(function () {
            BulkShipment.Constants.bulk_shipment_modal.modal();
            TrackPlusUtil.renderPartial(BulkShipment.Constants.bulk_shipment_modal_body, BulkShipment.Constants.Urls.bulk_shipment);

        });

        this.Constants.bulk_shipment_modal.unbind('hide.bs.modal');

        $('body').delegate(this.Constants.company_select, 'change', function () {
            if (BulkShipment.Constants.company_select.val() != "") {
                BulkShipment.Constants.company_billing_plan_select.find('option:selected').html('Select a Billing Plan');
            }
        });
    }
};

$(document).ready(function () {
    BulkShipment.init();
});