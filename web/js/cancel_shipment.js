$(document).ready(function () {
    $(".cancel-shipment").click(function () {
        console.log(2);
        var waybill_number = this.dataset['waybill_number'];
        $("input[name='waybill_number']").val(waybill_number);
        $("#cancel_shipment_form").submit();
    });
});
