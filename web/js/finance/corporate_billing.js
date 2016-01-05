
$("#clone-billing-plan").click(function () {
    if ($("#clone-billing-plan").is(':checked')) {
        $.get("getallbillingplannames",'','html').success(function (data) {
            $("#clone-details").html(data);
        }).error(function(){
            $("#clone-details").html('something went wrong');
        });
    } else {
        $("#clone-details").html("");
    }
});


