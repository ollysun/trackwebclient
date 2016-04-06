String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

function InvoiceObject() {
    this.company_id = 0;
    this.address = "";
    this.to_address = "";
    this.currency = "NGN";
    this.reference = "";
    this.parcels = [];
}

var invoicePayload = [];


var Invoice = {
    getInvoiceParcelHtml: function (index, params) {
        var html = $("#invoiceParcelTemplate").html();

        return html
            .replaceAll('{{serial_number}}', index + 1)
            .replaceAll('{{index}}', index)
            .replaceAll('{{waybill_number}}', params.waybill_number)
            .replaceAll('{{company_name}}', params.company_name)
            .replaceAll('{{amount}}', params.amount_due);
    },
    getInvoiceParcelsHtml: function (parcels) {
        var html = '';
        $(parcels).each(function (i, v) {
            html += Invoice.getInvoiceParcelHtml((i), v);
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
        $(elem).closest(".invoice_parcels").find("td[data-waybill='" + $(elem).data('waybill') + "']").html(newAmount);
        $(elem).closest(".invoice_parcels").find("input[data-parcel_waybill='" + $(elem).data('waybill') + "']").val(newAmount);

        invoicePayload[$(elem).closest(".invoice").data('index')]['parcels'][$(elem).data('index')]['net_amount'] = newAmount;
        invoicePayload[$(elem).closest(".invoice").data('index')]['parcels'][$(elem).data('index')]['discount'] = (discount / 100);
    },
    calculateTotalAmount: function (elem) {
        var total = 0;
        $(elem).closest(".invoice_parcels").find("td[data-waybill]").each(function (i, v) {
            total += Number($(v).html());
        });

        var stamp_duty = $(elem).closest(".invoice").find('input[name="stamp_duty"]').val();
        if(typeof stamp_duty != "undefined"){
            total += Number(stamp_duty);
        }

        total = parseFloat(total).toFixed(2);
        $(elem).closest(".invoice_parcels").find(".net_total").html(total);
        $(elem).closest(".invoice_parcels").find(".net_total_field").val(total);
        invoicePayload[$(elem).closest(".invoice").data('index')]['total'] = total;
    }
};

function updateAddress(el, key, index) {
    invoicePayload[index][key] = $(el).val();
}

$(document).ready(function () {

    var companies_count = 0;

    $("#chbx_w_all").change(function () {
        $("input[name=parcel]").prop("checked", $(this).prop("checked"));
    });

    $('.checkable').click(function (e) {
        var _this = $(this);
        var companyId = _this.data('company_id');
        if (_this.is(':checked')) {
            /*$('.checkable').each(function (i, v) {
             if ($(v).is(':checked') && companyId != $(v).data('company_id')) {
             e.preventDefault();
             return false;
             }
             });*/
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
            var packets = {};
            var parcels = [];
            $('.checkable').each(function (i, v) {
                if ($(v).is(':checked')) {
                    parcels.push(v.dataset);
                    if (typeof packets[v.dataset.company_id] == 'undefined') {
                        packets[v.dataset.company_id] = [];
                    }
                    packets[v.dataset.company_id].push(v.dataset);
                }
            });

            companies_count = (Object.keys(packets).length);

            var holder = $("#bulk_invoice");
            var __template = "";
            var x = 1;
            var tmpObj = null;


            for (var d in packets) {

                var address = (packets[d][0].company_address).replace(/([a-z])([A-Z])/g, '$1 $2');
                address = address.replace(/,/g, ', ');

                tmpObj = new InvoiceObject();
                tmpObj.company_id = packets[d][0].company_id;
                tmpObj.address = packets[d][0].company_name + ',' + "\n" + address;
                tmpObj.to_address = tmpObj.address;
                tmpObj.reference = packets[d][0].reference_number;
                tmpObj.parcels = getParcelsWaybill(packets[d]);
                tmpObj.stamp_duty = 0;
                tmpObj.account_number = packets[d][0].account_number;
                tmpObj.company_name = packets[d][0].company_name;


                __template = getAccordionHTML();

                if (x == 1) {
                    __template = replaceAll(__template, '{{collapse_status}}', 'in');
                } else {
                    __template = replaceAll(__template, '{{collapse_status}}', '');
                }
                __template = replaceAll(__template, '{{index}}', x);
                __template = replaceAll(__template, '{{waybill_number}}', packets[d][0].waybill_number);
                __template = replaceAll(__template, '{{company_name}}', packets[d][0].company_name);
                __template = replaceAll(__template, '{{amount}}', packets[d][0].amount_due);
                __template = replaceAll(__template, '{{invoiceList}}', Invoice.getInvoiceParcelsHtml(packets[d]));
                __template = replaceAll(__template, '{{account_number}}', packets[d][0].account_number);
                __template = replaceAll(__template, '{{address}}', address);
                __template = replaceAll(__template, '{{reference}}', packets[d][0].reference_number);
                __template = replaceAll(__template, '{{data_index}}', (invoicePayload.length));
                holder.append(__template);
                x++;
                __template = "";

                invoicePayload.push(tmpObj);
                tmpObj = null;
            }
            $("#generate_Invoice_btn").attr('type', 'button');
            $("#single_invoice").addClass('hidden');
            $("#multiple_invoice").removeClass('hidden');

            holder.find("input[data-waybill]").each(function (i, v) {
                $(v).trigger('keyup');
            });

        } else {
            e.preventDefault();
            e.stopPropagation();
            alert('Select a least one corporate parcel to proceed');
        }
    });

    function getParcelsWaybill(parcels) {
        var t = [];
        for (var i = 0; i < parcels.length; i++) {
            if ('undefined' == typeof parcels[i].waybill_number) {
                continue;
            }
            t.push({waybill_number: parcels[i].waybill_number, net_amount: parcels[i].amount_due, discount: 0});
        }
        return t;
    }

    function getAccordionHTML() {
        return $("#accordion_content").html();
    }

    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }

    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }


    $('body').delegate('input[data-waybill]', 'keyup', function (e) {
        Invoice.calculateNetAmount(this);
        Invoice.calculateTotalAmount(this);
    });

    $('body').delegate('input[name="stamp_duty"]', 'keyup', function (e) {
        invoicePayload[$(this).closest(".invoice").data('index')]['stamp_duty'] = $(this).val();
        $("input[data-waybill]").trigger('keyup');
    });

    $("#generate_Invoice_btn").unbind("click").on("click", function () {
        var isValid = true;
        $(".reference_number").each(function (i, v) {
            $(this).parent().removeClass('has-error');
            if ($(this).val().trim().length == 0) {
                $(this).focus().parent().addClass('has-error');
                isValid = isValid && false;
            }
        });

        if (!isValid) {
            alert('Please fill all reference number fields');
            return false;
        }

        $("#generate_Invoice_btn").attr('disabled', 'disabled').html('Processing... Please wait.');

        $.post(getInvoiceCreationURL(), {data: getInvoicePayload()}, function (response) {
            try {
                var jsonResponse = JSON.parse(response);
                alert(jsonResponse.message);
                window.location.reload();
            } catch (e) {
                alert("Unexpected response from service. Please refresh the page and try again. If this persists please contact support");
            }

            $("#generate_Invoice_btn").removeAttr('disabled').html('Generate Invoice');
        });
    });

    $("#same_as_invoice_to").click(function () {
        if ($(this).is(":checked")) {
            $("textarea[name=to_address]").val($("textarea[name=address]").val());
        } else {
            $("textarea[name=to_address]").val('');
        }
    });

    $("select#page_width").on('change', function (event) {
        $("#records_filter").click();
    });

    $("body").delegate('.same_as_invoice_to', 'click', function () {
        var invoice = $(this).closest('.invoice');

        if ($(this).is(':checked')) {
            var data_address = $(this).data('address');
            invoicePayload[invoice.data('index')].to_address = data_address;
            invoice.find('.to_address').val(data_address);
        } else {
            invoicePayload[invoice.data('index')].to_address = '';
            invoice.find('.to_address').val('');
        }
    });

    function getInvoicePayload() {
        if (companies_count == 1) {
            return invoicePayload[0];
        } else {
            return invoicePayload;
        }
    }

    function getInvoiceCreationURL() {
        if (companies_count == 1) {
            return '/finance/createinvoice';
        } else {
            return '/finance/createbulkinvoice';
        }
    }
});


