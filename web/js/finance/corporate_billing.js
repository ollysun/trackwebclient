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


