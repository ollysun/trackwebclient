$(document).ready(function(){

    $("#chbx_w_all").change(function () {
        $("input:checkbox").prop("checked", $(this).prop("checked"));
    });

    $("[data-target=#teller-modal]").on("click", function(event) {
        var chkboxes = $(".checkable:checked");

        if(!chkboxes.length) {
            alert("You must select at least one parcel!");
            event.preventDefault();
            return false;
        }
        var shipments = {};
        $.each(chkboxes, function(i, chk){
            shipments[$(this).data("waybill")]=$(this).data("sender");
        });
        var html = "";
        var i=1;
        $.each(shipments, function(waybill, sender){
            html += "<tr>";
            html += "<td>" + (i++) + "</td>";
            html += "<td>" + waybill + "</td>";
            html += "<td>" + sender + "</td>";
            html += "</tr>";
        });
        $("#teller-modal-table>tbody").html(html);
        $("input#waybill_numbers").val(Object.keys(shipments).toString());
    });

});
