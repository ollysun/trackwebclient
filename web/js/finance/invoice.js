String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};
var Invoice = {
    Urls : {
        INVOICE_PARCELS : "/finance/getinvoiceparcels"
    },
    getInvoiceParcels : function (invoiceNumber, successCallback, errorCallback) {
        $.getJSON(Invoice.Urls.INVOICE_PARCELS, {invoice_number : invoiceNumber}, successCallback)
            .error(errorCallback);
    },
    Constants : {
        invoiceParcels : $("#invoiceParcels")
    },
    Templates : {
        invoiceParcel : $("#invoiceParcelTmpl").html(),
        total : $("#invoiceParcelTotalTmpl").html()
    }
};
$(document).ready(function () {
    $("button[data-generate_credit_note]").click(function () {
        var companyName = $(this).data('company_name');
        Invoice.getInvoiceParcels($(this).data("invoice_number"), function (data) {
            $("#loading").addClass('hide');
            $("#table").removeClass('hide');
            var response = new ResponseHandler(data);

            if(response.isSuccess()) {
                var invoiceParcels = response.getData();
                var html = "";
                var parcelTemplate = Invoice.Templates.invoiceParcel;
                $(invoiceParcels).each(function (i, v) {
                    html += parcelTemplate
                        .replaceAll("{{index}}", i + 1)
                        .replaceAll("{{waybill_number}}", v.waybill_number)
                        .replaceAll("{{amount}}", v.parcel.amount_due)
                        .replaceAll("{{company_name}}", companyName.toUpperCase())
                        .replaceAll("{{net_amount}}", v.net_amount);
                });
                Invoice.Constants.invoiceParcels.html(html + Invoice.Templates.total);
            }
        }, function () {
            
        });
    });
});