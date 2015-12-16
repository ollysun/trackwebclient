var ExpectedShipment = {

    constants: {
        sort_btn: $("#btn_sort_shipment"),
        branch_select: $("#branch_name"),
        discard_btn: $("#discard_sorting"),
        confirm_btn: $('#confirm_sorting'),
        to_branch: "",
        to_branch_select: $("#to_branch"),
        draft_sort_url: '/hubs/draftsortparcels',
        discard_sorting_url: '/hubs/discarddraftsort',
        confirm_sorting_url: '/hubs/confirmdraftsort',
        get_draft_bag_parcels: '/hubs/getdraftbagparcels',
        create_bag_url: '/hubs/createdraftbag',
        create_draft_bag_btn: $('#create_draft_bag'),
        modal_create_draft_bag_btn: $('#create_draft_bag_btn'),
        create_draft_bag_modal: $('#create_draft_bag_modal'),
        bag_action_btn: $(".bag-action-btn")
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
    },

    /**
     * Create a draft bag
     */
    createDraftBag: function () {
        $.post(this.constants.create_bag_url, {
            sort_numbers: this.getSelected('sortnumber'),
            to_branch: this.constants.to_branch_select.val()
        });
    },

    /**
     * Get draft bag parcels
     * @param bag_number
     */
    getDraftBagParcels: function (bag_number) {
        $.get(this.constants.get_draft_bag_parcels, {
            bag_number: bag_number
        }, function (response) {
            console.log(response);
        })
    },

    addRowToTableBody: function (tbody, rowData) {
        var row_content = '<tr>';
        for (var i = 0; i < rowData.length; i++) {
            row_content += '<td>' + rowData[i] + '</td>';
        }
        row_content += '</tr>';
        tbody.append(row_content);
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

    ExpectedShipment.constants.create_draft_bag_btn.unbind('click').click(function () {
        var selectedWaybillNumbers = ExpectedShipment.getSelected();
        var selectedNextDestination = ExpectedShipment.getSelected('nextdestination');

        if (selectedWaybillNumbers.length == 0) {
            alert("You need to select at least one draft sort to create a bag");
            return false;
        }

        var draft_items = $("#draft_items");
        draft_items.html('');

        for (var i = 0; i < selectedWaybillNumbers.length; i++) {
            ExpectedShipment.addRowToTableBody(draft_items, [(i + 1), selectedWaybillNumbers[i], selectedNextDestination[i]]);
        }
        ExpectedShipment.constants.create_draft_bag_modal.modal('show');
    });

    ExpectedShipment.constants.modal_create_draft_bag_btn.unbind('click').click(function () {
        if (ExpectedShipment.constants.to_branch_select.val() == '') {
            alert("Please select a destination branch");
            return false;
        }

        ExpectedShipment.createDraftBag();
    });

    ExpectedShipment.constants.bag_action_btn.unbind('click').click(function () {
        var sort_number = $(this).closest('tr').data('sortnumber');
        ExpectedShipment.getDraftBagParcels(sort_number);
        ExpectedShipment.constants.create_draft_bag_modal.modal('show');
    });


});