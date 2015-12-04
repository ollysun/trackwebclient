$(document).ready(function () {
    $('.checkable').click(function (e) {
        var _this = $(this);
        var companyId  = _this.data('company_id');
        if(_this.is(':checked')) {
            $('.checkable').each(function (i, v) {
                if($(v).is(':checked') && companyId != $(v).data('company_id')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });

    $('#generateInvoiceBtn').unbind('click').click(function (e) {
        var proceed = false;
        $('.checkable').each(function (i, v) {
            if($(v).is(':checked')) {
                proceed = true;
                return false;
            }
        });

        if(proceed) {

        } else {
            e.preventDefault();
            e.stopPropagation();
            alert('Select a least one corporate parcel to proceed');
        }
    });
});