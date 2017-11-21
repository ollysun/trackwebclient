/**
 * Created by ELACHI on 12/14/2016.
 */
$(document).ready(function(){
    $("button[data-target='#pod-modal']").on("click", function (event) {
        var waybill_number = $(this).attr('data-waybill-number');
        alert(waybill_number);
        $("#pod_waybill_number").val(waybill_number);
    });
});
