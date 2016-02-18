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
        invoiceParcels : $("#invoiceParcels"),
        invoiceNumber : $("#invoice_number"),
        invoiceNumberLabel : $("#invoiceNumberLabel"),
        view_invoiceNumber : $("#view_invoiceNumber"),
        view_invoiceAddress : $("#view_invoiceAddress"),
        view_invoiceTo : $("#view_invoiceTo"),
        view_reference : $("#view_reference"),
        view_accountNumber : $("#view_accountNumber"),
        view_stampDuty : $("#view_stampDuty"),
        view_currency : $("#view_currency"),
        viewInvoiceLoading : $("#viewInvoiceLoading"),
        viewInvoiceTable : $("#viewInvoiceTable"),
        viewPrintInvoice : $("#viewPrintInvoice"),
    },
    Templates : {
        invoiceParcel : $("#invoiceParcelTmpl").html(),
        total : $("#invoiceParcelTotalTmpl").html(),
        viewInvoiceParcels : $("#viewInvoiceParcelsTmpl").html(),
        viewInvoiceTotal : $("#viewInvoiceTotalTmpl").html()
    },
    calculateNetAmount: function (elem) {
        var netAmount = Number($(elem).data('net_amount'));
        var deductedAmount = parseFloat($(elem).val());
        var newAmount = parseFloat(netAmount).toFixed(2);
        if (deductedAmount > 0) {
            newAmount = parseFloat(netAmount - deductedAmount).toFixed(2);
        }
        Invoice.Constants.invoiceParcels.find("td[data-waybill='" + $(elem).data('waybill') + "']").html(newAmount);
        Invoice.Constants.invoiceParcels.find("input[data-parcel_waybill='" + $(elem).data('waybill') + "']").val(newAmount);
    },
    calculateTotalAmount: function () {
        var total = 0;
        Invoice.Constants.invoiceParcels.find("td[data-waybill]").each(function (i, v) {
            total += Number($(v).html());
        });
        total = parseFloat(total).toFixed(2);
        $("#net_total").html(total);
    }
};
$(document).ready(function () {
    $("button[data-generate_credit_note]").click(function () {
        var companyName = $(this).data('company_name');
        var invoiceNumber = $(this).data('invoice_number');
        Invoice.Constants.invoiceNumber.val(invoiceNumber);
        Invoice.Constants.invoiceNumberLabel.html(invoiceNumber);
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
                        .replaceAll("{{net_amount}}", v.net_amount)
                        .replaceAll("{{invoice_parcel_id}}", v.id)
                        .replaceAll("{{discount}}", parseFloat(Number(v.discount) * 100).toFixed(2) + '%');
                });
                Invoice.Constants.invoiceParcels.html(html + Invoice.Templates.total);
                Invoice.calculateTotalAmount();
            }
        }, function () {
            //TODO Handle Errors
            $("#loading").addClass('hide');
            alert('Unable to load invoice parcels');
        });
    });

    $("button[data-view_invoice='true']").unbind('click').click(function () {
        var invoice = JSON.parse($(this).prev().html());
        Invoice.Constants.view_invoiceNumber.html(invoice.invoice_number);
        Invoice.Constants.view_accountNumber.val(invoice.account_number);
        Invoice.Constants.view_currency.val(invoice.currency);
        Invoice.Constants.view_invoiceTo.val(invoice.to_address);
        Invoice.Constants.view_reference.val(invoice.reference);
        Invoice.Constants.view_invoiceAddress.val(invoice.address);
        Invoice.Constants.view_stampDuty.val(invoice.stamp_duty);
        Invoice.Constants.viewPrintInvoice.prop('href', '/finance/printinvoice?invoice_number=' + invoice.invoice_number);

        Invoice.getInvoiceParcels(invoice.invoice_number, function (data) {
            Invoice.Constants.viewInvoiceLoading.addClass('hide');
            Invoice.Constants.viewInvoiceTable.removeClass('hide');
            var response = new ResponseHandler(data);

            if(response.isSuccess()) {
                var invoiceParcels = response.getData();
                var html = "";

                $(invoiceParcels).each(function (i, v) {
                    html += Invoice.Templates.viewInvoiceParcels
                                .replaceAll("{{index}}", i  + 1)
                                .replaceAll("{{waybill_number}}", v.waybill_number)
                                .replaceAll("{{company_name}}", invoice.company.name.toUpperCase())
                                .replaceAll("{{amount}}", v.parcel.amount_due)
                                .replaceAll("{{discount}}", parseFloat(v.discount * 100).toFixed(2))
                                .replaceAll("{{net_amount}}", v.net_amount);
                });
                html += Invoice.Templates.viewInvoiceTotal.replaceAll("{{total}}", invoice.total);
                Invoice.Constants.viewInvoiceTable.find('tbody').html(html);
            }
        }, function () {
            Invoice.Constants.viewInvoiceLoading.addClass('hide');
            alert('Unable to load invoice parcels');
        });
    });

    Invoice.Constants.invoiceParcels.on("keypress", "input[data-waybill]", function (e) {
        this.value = this.value.replace(/[^0-9\.]/g, '');
    }).on('keyup', "input[data-waybill]", function () {
        Invoice.calculateNetAmount(this);
        Invoice.calculateTotalAmount();
    });
});