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


    $('.btnClone').on('click', function (event) {

        var self = this;
        bootbox.dialog({
            message: "What action do you want to Perform?",
            title: "Clone Shipments",
            buttons: {
                success: {
                    label: "Clone and cancel",
                    className: "btn-success",
                    callback: function() {
                        cloneShipment($(self), true)
                    }
                },
                info: {
                    label: "Clone Only",
                    className: "btn-info",
                    callback: function() {
                        cloneShipment($(self), false)
                    }
                }
            }
        });
    });

    function cloneShipment(object, ans) {

        var clone_url = $(object).attr('data-href');
        if(ans) {
            var params = { "waybill_numbers": $(object).closest('tr').data('waybill') };

            $.ajax({
                url: '/shipments/cancel',
                type: 'POST',
                dataType: 'JSON',
                data: JSON.stringify(params),
                success: function (result) {
                    if (result.status == 'success' || result.status == 200) {
                        console.log(params.waybill + ' has been cancelled!');
                        window.location = clone_url;
                    } else {
                        alert(result.message);
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            })
        } else {
            window.location = clone_url;
        }
    }
});
