String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

var Invoice = {
    getInvoiceParcelHtml: function (index, params) {
        var html = $("#invoiceParcelTemplate").html();

        return html
            .replaceAll('{{id}}', index)
            .replaceAll('{{index}}', index)
            .replaceAll('{{waybill_number}}', params.waybill_number)
            .replaceAll('{{company_name}}', params.company_name)
            .replaceAll('{{amount}}', params.amount_due);
    },
    getInvoiceParcelsHtml: function (parcels) {
        var html = '';
        $(parcels).each(function (i, v) {
            html += Invoice.getInvoiceParcelHtml((i + 1), v);
        });

        html += $("#invoiceTotal").html();
        return html;
    },
    calculateNetAmount: function (elem) {
        var amount = Number($(elem).data('amount'));
        var discount = parseFloat($(elem).val());
        var newAmount = parseFloat(amount).toFixed(2);
        if (discount > 0) {
            newAmount = parseFloat(amount - (amount * (discount / 100))).toFixed(2);
        }
        $("#invoice_parcels").find("td[data-waybill='" + $(elem).data('waybill') + "']").html(newAmount);
        $("#invoice_parcels").find("input[data-parcel_waybill='" + $(elem).data('waybill') + "']").val(newAmount);
    },
    calculateTotalAmount: function () {
        var total = 0;
        $("#invoice_parcels").find("td[data-waybill]").each(function (i, v) {
            total += Number($(v).html());
        });
        total = parseFloat(total).toFixed(2);
        $("#net_total").html(total);
        $("#net_total_field").val(total);
    }
};

$(document).ready(function () {

    $("#chbx_w_all").change(function () {
        $("input[name=parcel]").prop("checked", $(this).prop("checked"));
    });

    $('.checkable').click(function (e) {
        var _this = $(this);
        var companyId = _this.data('company_id');
        if (_this.is(':checked')) {
            $('.checkable').each(function (i, v) {
                if ($(v).is(':checked') && companyId != $(v).data('company_id')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });

    $('#generateInvoiceBtn').unbind('click').click(function (e) {
        var proceed = false;
        $('.checkable').each(function (i, v) {
            if ($(v).is(':checked')) {
                proceed = true;
                return false;
            }
        });

        if (proceed) {
            var parcels = [];
            $('.checkable').each(function (i, v) {
                if ($(v).is(':checked')) {
                    parcels.push(v.dataset);
                }
            });

            var address = (parcels[0].company_address).replace(/([a-z])([A-Z])/g, '$1 $2');
            address = address.replace(/,/g,', ');
            $('textarea[name=address]').val(parcels[0].company_name + ',' +  "\n" + address);
            $('input[name=company_id]').val(parcels[0].company_id);
            $('input[name=account_number]').val(parcels[0].account_number);
            $('textarea[name=reference]').val(parcels[0].reference_number);
            $("#invoice_parcels").html(Invoice.getInvoiceParcelsHtml(parcels))
                .find("input[data-waybill]").trigger('keyup');
        } else {
            e.preventDefault();
            e.stopPropagation();
            alert('Select a least one corporate parcel to proceed');
        }
    });

    $("#invoice_parcels").on("keypress", "input[data-waybill]", function (e) {
        this.value = this.value.replace(/[^0-9\.]/g, '');
    }).on('keyup', "input[data-waybill]", function () {
        Invoice.calculateNetAmount(this);
        Invoice.calculateTotalAmount();
    });
    
    $("#same_as_invoice_to").click(function () {
       if($(this).is(":checked")) {
           $("textarea[name=to_address]").val($("textarea[name=address]").val());
       } else {
           $("textarea[name=to_address]").val('');
       }
    });

    $("select#page_width").on('change', function (event) {
        $("#records_filter").click();
    });
});


