$(document).ready(function () {

    $("#chbx_w_all").change(function () {
        $("input:checkbox").prop("checked", $(this).prop("checked"));
    });

    $("button[data-target='#teller-modal']").on("click", function (event) {
        var chkboxes = $(".checkable:checked");

        if (!chkboxes.length) {
            alert("You must select at least one parcel!");
            event.preventDefault();
            return false;
        }
        var shipments = {};
        var amount_due = 0;
        $.each(chkboxes, function (i, chk) {
            shipments[$(this).data("waybill")] = $(this).data("sender");
            amount_due += parseFloat($(this).data('amount_due'));
        });
        var html = "";
        var i = 1;
        $.each(shipments, function (waybill, sender) {
            html += "<tr>";
            html += "<td>" + (i++) + "</td>";
            html += "<td>" + waybill + "</td>";
            html += "<td>" + sender + "</td>";
            html += "</tr>";
        });
        $('#amount_paid').val(amount_due);
        $("#teller-modal-table>tbody").html(html);
        $("input#waybill_numbers").val(Object.keys(shipments).toString());
    });

    $("button[id='btnSubmitTeller']").on("click", function (event) {
        if(amount_due - 49 > parseFloat($("#amount_paid"))){
            alert('Invalid amount entered');
            return false;
        }
    })

    $('.btnClone').on('click', function (event) {

        var self = this;
        bootbox.dialog({
            message: "What action do you want to Perform?",
            title: "Clone Shipments",
            buttons: {
                success: {
                    label: "Clone and cancel",
                    className: "btn-success",
                    callback: function () {
                        cloneShipment($(self), true)
                    }
                },
                info: {
                    label: "Clone Only",
                    className: "btn-info",
                    callback: function () {
                        cloneShipment($(self), false)
                    }
                },
                edit: {
                    label: "Edit",
                    className: "btn-default",
                    callback: function () {
                        editShipment($(self));
                    }
                }
            }
        });
    });

    function cloneShipment(object, ans) {

        var clone_url = $(object).attr('data-href');
        if (ans) {
            var params = {"waybill_numbers": $(object).closest('tr').data('waybill')};

            $.ajax({
                url: '/shipments/cancel',
                type: 'POST',
                dataType: 'JSON',
                data: JSON.stringify(params),
                success: function (result) {
                    if (result.status == 'success' || result.status == 200) {
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

    function editShipment(object) {
        window.location = $(object).attr('data-href') + '&edit=1';
    }
});
