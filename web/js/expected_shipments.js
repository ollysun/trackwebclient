var ExpectedShipment = {};

ExpectedShipment.constants = {
    sort_btn: $("#btn_sort_shipment"),
    branch_select: $("#branch_name"),
    discard_btn: $("#discard_sorting"),
    confirm_btn: $('#confirm_sorting'),
    to_branch: "",
    draft_sort_url: '/hubs/draftsortparcels',
    discard_sorting_url: '/hubs/discarddraftsort'
};

/**
 * @param data_key
 * @returns {Array}
 */
ExpectedShipment.getSelected = function (data_key) {

    if (typeof data_key == 'undefined') {
        data_key = 'waybill';
    }
    var checkboxes = $(".chk_next:checked");
    var selected = [];

    $.each(checkboxes, function (index, value) {
        selected.push($(value).closest('tr').data(data_key))
    });

    return selected;
};


/**
 * Create draft sortings
 * @returns {boolean}
 */
ExpectedShipment.createDraftSortings = function () {
    if (ExpectedShipment.constants.to_branch == "") {
        alert("You need to select a destination branch");
        return false;
    }

    if (ExpectedShipment.getSelected().length == 0) {
        alert("You need to select at least one parcel to draft sort");
        return false;
    }

    $.post(ExpectedShipment.constants.draft_sort_url, {
        waybill_numbers: ExpectedShipment.getSelected(),
        to_branch: ExpectedShipment.constants.to_branch
    });
};

/**
 * Discard draft sorts
 * @returns {boolean}
 */
ExpectedShipment.discardSortings = function () {
    var selectedSortNumbers = ExpectedShipment.getSelected('sortnumber');

    if (selectedSortNumbers.length == 0) {
        alert("You need to select at least one parcel to discard sort");
        return false;
    }

    $.post(ExpectedShipment.constants.discard_sorting_url, {
        sort_numbers: selectedSortNumbers
    });
};


$(document).ready(function () {
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

    ExpectedShipment.constants.confirm_btn.unbind('click').click(function () {

    });

    ExpectedShipment.constants.discard_btn.unbind('click').click(function () {
        ExpectedShipment.discardSortings();
    });
});