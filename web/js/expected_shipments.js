var ExpectedShipment = {};

/**
 * Get Selected Waybill Numbers
 * @returns {Array}
 */
ExpectedShipment.getSelectedWaybillNumbers = function () {

    var checkboxes = $(".chk_next:checked");
    var waybill_numbers = [];

    $.each(checkboxes, function (index, value) {
        waybill_numbers.push($(value).closest('tr').data('waybill'))
    });

    return waybill_numbers;
};

ExpectedShipment.constants = {
    sort_btn: $("#btn_sort_shipment"),
    branch_select: $("#branch_name"),
    to_branch: null,
    draft_sort_url: '/hubs/draftsortparcels'
};

ExpectedShipment.createDraftSortings = function () {
    $.post(ExpectedShipment.constants.draft_sort_url, {
        waybill_numbers: ExpectedShipment.getSelectedWaybillNumbers(),
        to_branch: ExpectedShipment.constants.to_branch
    });
};


$(document).ready(function () {

    ExpectedShipment.constants.sort_btn.attr('disabled', true);

    ExpectedShipment.constants.sort_btn.unbind('click').click(function () {
        ExpectedShipment.createDraftSortings();
    });

    ExpectedShipment.constants.branch_select.on('change', function () {
        if ($(this).val() == "") {
            ExpectedShipment.constants.sort_btn.attr('disabled', true);
        } else {
            ExpectedShipment.constants.to_branch = $(this).val();
            ExpectedShipment.constants.sort_btn.attr('disabled', false);
        }
    });
});