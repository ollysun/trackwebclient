var ExpectedShipment = {
    constants: {
        sort_btn: $("#btn_sort_shipment"),
        branch_select: $("#branch_name"),
        discard_btn: $("#discard_sorting"),
        confirm_btn: $('#confirm_sorting'),
        to_branch: "",
        draft_sort_url: '/hubs/draftsortparcels',
        discard_sorting_url: '/hubs/discarddraftsort',
        confirm_sorting_url: '/hubs/confirmdraftsort'
    },

    /**
     * @param data_key
     * @returns {Array}
     */
    getSelected: function (data_key) {

        if (typeof data_key == 'undefined') {
            data_key = 'waybill';
        }
        var checkboxes = $(".chk_next:checked");
        var selected = [];

        $.each(checkboxes, function (index, value) {
            selected.push($(value).closest('tr').data(data_key))
        });

        return selected;
    },


    /**
     * Create draft sortings
     * @returns {boolean}
     */
    createDraftSortings: function () {
        if (this.constants.to_branch == "") {
            alert("You need to select a destination branch");
            return false;
        }

        if (this.getSelected().length == 0) {
            alert("You need to select at least one parcel to draft sort");
            return false;
        }

        $.post(this.constants.draft_sort_url, {
            waybill_numbers: this.getSelected(),
            to_branch: this.constants.to_branch
        });
    },

    /**
     * Discard draft sorts
     * @returns {boolean}
     */
    discardSortings: function () {
        var selectedSortNumbers = this.getSelected('sortnumber');

        if (selectedSortNumbers.length == 0) {
            alert("You need to select at least one parcel to discard sort");
            return false;
        }

        $.post(this.constants.discard_sorting_url, {
            sort_numbers: selectedSortNumbers
        });
    },

    /**
     * Confirm draft sorts
     * @returns {boolean}
     */
    confirmSortings: function () {
        var selectedSortNumbers = this.getSelected('sortnumber');

        if (selectedSortNumbers.length == 0) {
            alert("You need to select at least one parcel to confirm sort");
            return false;
        }

        $.post(this.constants.confirm_sorting_url, {
            sort_numbers: selectedSortNumbers
        });
    }
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
        ExpectedShipment.confirmSortings();
    });

    ExpectedShipment.constants.discard_btn.unbind('click').click(function () {
        ExpectedShipment.discardSortings();
    });
});