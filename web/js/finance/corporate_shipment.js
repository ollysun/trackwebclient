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

function updateAddress(el, key, index){
    invoicePayload[index][key] = $(el).val();
}

$(document).ready(function () {

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
                    if(typeof packets[v.dataset.company_id] == 'undefined') {
                        packets[v.dataset.company_id] = [];
                    }
                    packets[v.dataset.company_id].push(v.dataset);
                }
            });
            var company_count = (Object.keys(packets).length);
            if(company_count > 1) {
                var holder = $("#bulk_invoice");
                var __template = "";
                var x = 1; var tmpObj = null;


                for(var d in packets) {

                    var address = (packets[d][0].company_address).replace(/([a-z])([A-Z])/g, '$1 $2');
                    address = address.replace(/,/g,', ');

                    tmpObj = new InvoiceObject();
                    tmpObj.company_id = packets[d][0].company_id;
                    tmpObj.address = packets[d][0].company_name + ',' +  "\n" + address;
                    tmpObj.to_address = tmpObj.address;
                    tmpObj.reference = packets[d][0].reference_number;
                    tmpObj.parcels = getParcelsWaybill(packets[d]);
                    invoicePayload.push(tmpObj);
                    tmpObj = null;


                    __template = getAccordionHTML();

                    if(x==1){
                        __template = replaceAll(__template,'{{collapse_status}}', 'in' );
                    }else{
                        __template = replaceAll(__template,'{{collapse_status}}', '' );
                    }
                    __template = replaceAll(__template,'{{index}}', x );
                    __template = replaceAll(__template,'{{waybill_number}}', packets[d][0].waybill_number );
                    __template = replaceAll(__template,'{{company_name}}', packets[d][0].company_name );
                    __template = replaceAll(__template,'{{amount}}', packets[d][0].amount_due );
                    __template = replaceAll(__template,'{{invoiceList}}', Invoice.getInvoiceParcelsHtml(packets[d]));
                    __template = replaceAll(__template,'{{account_number}}', packets[d][0].account_number );
                    __template = replaceAll(__template,'{{address}}', address );
                    __template = replaceAll(__template,'{{reference}}', packets[d][0].reference_number );
                    __template = replaceAll(__template,'{{data_index}}', (invoicePayload.length -1) );
                    holder.append(__template);
                    x++;
                    __template = "";
                }
                $("#generate_Invoice_btn").attr('type', 'button');
                $("#single_invoice").addClass('hidden');
                $("#multiple_invoice").removeClass('hidden');
                $(".same_as_invoice_to").unbind('click').on('click', function(){
                    var data_index = $(this).data('index');

                    if($(this).is(':checked')) {
                        var data_address = $(this).data('address');
                        invoicePayload[data_index].to_address = data_address;
                        $("#to_address" +data_index).val(data_address);
                    }else{
                        invoicePayload[data_index].to_address = "";
                        $("#to_address" +data_index).val("");
                    }

                });
            }else{
                var address = (parcels[0].company_address).replace(/([a-z])([A-Z])/g, '$1 $2');
                address = address.replace(/,/g,', ');
                $('textarea[name=address]').val(parcels[0].company_name + ',' +  "\n" + address);
                $('input[name=company_id]').val(parcels[0].company_id);
                $('input[name=account_number]').val(parcels[0].account_number);
                $('textarea[name=reference]').val(parcels[0].reference_number);
                $("#invoice_parcels").html(Invoice.getInvoiceParcelsHtml(parcels))
                    .find("input[data-waybill]").trigger('keyup');
                $("#generate_Invoice_btn").attr('type', 'submit');
                $("#multiple_invoice").addClass('hidden');
                $("#single_invoice").removeClass('hidden');

            }



        } else {
            e.preventDefault();
            e.stopPropagation();
            alert('Select a least one corporate parcel to proceed');
        }
    });

    function getParcelsWaybill(parcels) {
        var t = [];
        for(var i =0; i < parcels.length; i++) {
            if('undefined' == typeof parcels[i].waybill_number) {continue;}
            t.push(parcels[i].waybill_number);
        }
        return t;
    }

    function getAccordionHTML(){
        return $("#accordion_content").html();
    }

    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }

    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }



    $("#invoice_parcels").on("keypress", "input[data-waybill]", function (e) {
        this.value = this.value.replace(/[^0-9\.]/g, '');
    }).on('keyup', "input[data-waybill]", function () {
        Invoice.calculateNetAmount(this);
        Invoice.calculateTotalAmount();
    });

    $("#generate_Invoice_btn").unbind("click").on("click", function(){

        $("#generate_Invoice_btn").attr('disabled', 'disabled').html('Processing... Please wait.');
        $.post("/finance/createbulkinvoice",{data:invoicePayload}, function(response){

            try{
                var jsonResponse = JSON.parse(response);
                alert(jsonResponse.message);
                window.location.reload();
            }catch(e){
                alert("Unexpected response from service. Please refresh the page and try again. If this persists please contact support");
            }

            $("#generate_Invoice_btn").removeAttr('disabled').html('Generate Invoice');
        });
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


