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
});