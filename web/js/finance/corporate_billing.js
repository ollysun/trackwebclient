
var checkbox = document.getElementById('clone-billing-plan');
var cloneDetails = document.getElementById('clone-details');
$("#clone-billing-plan").click(function () {
    if (checkbox.checked) {
        $.get("getallbillingplannames",'','html').success(function (data) {
            $("#clone-details").html(data);
        }).error(function(){
            $("#clone-details").html('something went wrong');
        });
    } else {
        cloneDetails.innerHTML=" ";
    }
});


