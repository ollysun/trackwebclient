String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

var Invoice = {
    getInvoiceParcelHtml: function (index, params) {
        var html = "\
            <tr>\
            <td>{{index}}</td>\
            <td>{{waybill_number}}</td>\
            <td>{{company_name}}</td>\
            <td>{{amount}}</td>\
            <td>\
            <input type='text' name='discount[]' data-amount='{{amount}}' data-waybill='{{waybill_number}}' class='form-control' style='width:50px;' value='0'>\
            <input type='hidden' name='waybill_number[]' value='{{waybill_number}}'>\
            <input type='hidden' data-parcel_waybill='{{waybill_number}}' name='net_amount[]' value='{{amount}}'>\
            </td>\
            <td data-waybill='{{waybill_number}}'>{{amount}}</td>\
            </tr>";

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

        html += "<tr>\
            <td></td>\
            <td><b>NET TOTAL</b></td>\
        <td></td>\
        <td></td>\
        <td></td>\
        <td><b id='net_total'></b></td>\
            </tr>";
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

            $('textarea[name=address]').val(parcels[0].company_address);
            $('input[name=company_id]').val(parcels[0].company_id);
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
});