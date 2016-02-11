$(document).ready(function () {

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

    function editShipment(object) {
        window.location = $(object).attr('data-href') + '&edit=1';
    }
});