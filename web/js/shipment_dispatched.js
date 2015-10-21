/**
 * Created by Lawale on 21/10/15.
 */
$(document).ready(function () {

    $("#chbx_w_all").change(function () {
        $("input:checkbox.checkable").prop("checked", $(this).prop("checked"));
    });
    $("[data-target=#passwordModal]").on("click", function (event) {
        var chkboxes = $(".checkable:checked");

        if (!chkboxes.length) {
            alert("You must select at least one parcel!");
            event.preventDefault();
            return false;
        }
        waybill_numbers = [];

        if ($(this).data("action") == 'return') {
            if(!$(".checkable:checked[data-is-return]").length) {
                alert("You have selected a parcel that is not set for return!");
                event.preventDefault();
                return false;
            }
        }

        $.each(chkboxes, function (i, chk) {
            waybill_numbers.push($(this).attr("data-waybill"));
        });

        $("input#task").val($(this).attr("data-action"));
        $("input#waybills").val(JSON.stringify(waybill_numbers));
    });
});


