var cloneBillingPlan = $("#clone-billing-plan");
var cloneDetails = $("#clone-details");
cloneBillingPlan.click(function () {
    if (cloneBillingPlan.is(':checked')) {
        $.get("getallbillingplannames",'','html').success(function (data) {
            cloneDetails.html(data);
        }).error(function(){
            cloneDetails.html('something went wrong');
        });
    } else {
        cloneDetails.html("");
    }
});


$('.editbtn').click(function(){
    var _this = $(this);
    $('#edit_name').val($(this).attr('data-name'));
    $('#edit_id').val($(this).attr('data-id'));

    $("#edit_company option").prop('selected', false).filter(function() {
        return $(this).val() == 1;
    }).prop('selected', true);

    $('#edit_discount').val($(this).attr('data-discount'));

})

