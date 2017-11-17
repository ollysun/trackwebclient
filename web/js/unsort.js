
$('#unsort_btn').unbind('click').click(function () {
    var checkboxes = $('.chk_next:checked');

    if (checkboxes.length == 0) {
        bootbox.alert('<strong>Please select one or more parcels to unsort</strong>');
        return false;
    }

    bootbox.dialog({
        message: "Are you sure you want to unsort the selected parcels?",
        title: "Unsort Parcels",
        buttons: {
            success: {
                label: "Unsort",
                className: "btn-success",
                callback: function () {
                    var waybills = [];
                    $.each(checkboxes, function (i, v) {
                        var checkbox = $(v);
                        waybills.push(checkbox.closest('tr').data('waybill'));
                    });
                    waybills = waybills.join(',');
                    $("#unsort_waybill_numbers").val(waybills);
                    $("#unsort_form").submit();
                }
            },
            info: {
                label: "Cancel",
                className: "btn-info",
                callback: function () {
                }
            }
        }
    });

});