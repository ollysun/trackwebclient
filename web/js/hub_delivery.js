/**
 * Created by RotelandO on 7/25/15.
 */
var parcels = {
    waybills: [],
    to_branch_id: '',
    to_branch_name: '',
    staff_id: '',
    staff_code: ''
};

var Parcel_Destination = {

    Url: {
        'allhubs': '/hubs/allhubs',
        'allecforhubs': '/hubs/allecforhubs',
        'staffdetails': '/hubs/staffdetails',
        'generatemanifest': '/hubs/generatemanifest',
        'createbag': '/hubs/createbag'
    },

    getNewStaffInfo: function () {

        return {
            id: '',
            staff_name: '',
            staff_email: '',
            staff_phone: '',
            staff_role: ''
        }
    },

    fillSelectOption: function (url, param, selectSelector, selectedValue) {
        $.get(url, param, function (response) {
            if (response.status === 'success') {
                var html = '<option value="">Select Name...</option>';
                $.each(response.data, function (i, item) {
                    selected = (selectedValue == item.id) ? 'selected="selected"' : '';
                    html += "<option value='" + item.id + "'" + selected + ">" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).html(html);
            }
        });
    },

    searchStaffDetails: function (code) {
        var self = this;
        $.get(Parcel_Destination.Url.staffdetails, {code: code}, function (response) {
            if (response.status === 'success') {
                var staff = self.getNewStaffInfo();
                staff.id = response.data.id;
                staff.code = code;
                staff.staff_name = response.data.fullname;
                staff.staff_email = response.data.email;
                staff.staff_phone = response.data.phone;
                staff.staff_role = response.data.role.name;
                self.updateStaffDetails(staff);
                $('#staff_info').show();
                $('#btnGenerate').attr('disabled', false);
            } else {
                alert(response.message);
                $('#staff_info').hide();
                $('#btnGenerate').attr('disabled', true);
            }
        });
    },

    updateStaffDetails: function (staff) {

        parcels.staff_id = staff.id;
        parcels.staff_code = staff.code;
        $('#staff_name').html(staff.staff_name);
        $('#staff_email').html(staff.staff_email);
        $('#staff_phone').html(staff.staff_phone);
        $('#staff_role').html(staff.staff_role);
    },

    moveToInTransit: function (parcels) {


        $.ajax({
            url: Parcel_Destination.Url.generatemanifest,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(parcels),
            success: function (response) {
                if (response.status == 'success') {
                    alert('Manifest Generated Successfully!');
                } else {
                    alert('An error occurred when generating manifest. Please try again later');
                }
            },
            error: function (err) {
                alert('An error occurred when generating manifest. Please try again later');
            },
            complete: function (jqXHR) {

            }
        })
    },

    createBag: function (parcels) {
        parcels.to_branch_id = $("#to_branch").val();
        parcels.to_branch_name = $("#to_branch :selected").text();
        parcels.seal_id = $("#seal_id").val();

        $.ajax({
            url: Parcel_Destination.Url.createbag,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(parcels),
            success: function (response) {
                if (response.status == 'success') {
                    alert('Bag ' + response.data.bag_number + ' has been created successfully!');
                    $('#createBag').modal('hide');
                    location.reload();
                } else {
                    alert('An error occurred while trying to create bag. #' + response.message);
                }
            },
            error: function (err) {
                alert('We are unable to process this request. Please try again later.');
            },
            complete: function (jqXHR) {
                $("#btnBag").attr('disabled', false);
            }
        })
    }
};

var TableHelper = {


    renumberTableSerialNo: function (tableSelector, colIndex) {


        var trs = $(tableSelector + ' tbody').children();
        if (!this.isDefined(colIndex)) {
            colIndex = 0;
        }
        for (var i = 0; i < trs.length; i++) {
            var children = $(trs[i]).find('td:eq(' + colIndex + ')').first().html(i + 1);
        }
    },

    isItemInTableColumn: function (tableSelector, colIndex, searchItem) {

        var tempText = '';
        var found = false;
        var trs = $(tableSelector + ' tbody').children();
        if (!this.isDefined(colIndex)) {
            colIndex = 0;
        }

        $.each(trs, function (index, tr) {
            tempText = $.trim($(tr).children().eq(colIndex).html());
            if ((tempText) == searchItem) {
                found = true;
            }
        });
        return found;
    },

    getColumTextData: function (tableSelector, colIndex) {

        var items = [];
        var trs = $(tableSelector + ' tbody').children();
        if (!this.isDefined(colIndex)) {
            colIndex = 0;
        }

        $.each(trs, function (index, tr) {
            items.push($.trim($(tr).children().eq(colIndex).html()));
        });
        return items;
    },

    setCellData: function (tableSelector, rowIndex, colIndex, content) {

        if (!this.isDefined(rowIndex) || !this.isDefined(colIndex)) {
            return;
        }
        var trs = $(tableSelector + ' tbody').children();
        $(trs).eq(rowIndex).children().eq(colIndex).html(content);
    },

    setColumnData: function (tableSelector, colIndex, content, withCheckbox) {

        if (!this.isDefined(colIndex)) {
            return;
        }
        var trs = $(tableSelector + ' tbody').children();

        $.each(trs, function (rowIndex, tr) {
            if (withCheckbox) {
                var chkValue = $(tr).children().find(':checkbox').attr('value');
                if (chkValue) {
                    $(tr).children().eq(colIndex).html(content);
                }
            } else {
                $(tr).children().eq(colIndex).html(content);
            }
        });
    },

    getCellData: function (tableSelector, colIndex, rowIndex) {
        var cellData = '';
        var trs = $(tableSelector + ' tbody').children();
        if (!this.isDefined(colIndex)) {
            colIndex = 0;
        }

        $.each(trs, function (index, tr) {
            if (index === rowIndex) {
                cellData = $.trim($(tr).children().eq(colIndex).html());
            }
        });

        return cellData;
    },

    isCallback: function (callback) {
        return (callback && typeof(callback) === typeof(Function));
    },

    isDefined: function (value) {

        return (typeof value !== 'undefined');
    }
};

$(document).ready(function () {

    $('#staff_info').hide();
    $('#btnGenerate').attr('disabled', true);

    var btype = $('.branch_type').find('option:selected').val();
    var bid = $('.branch_name').attr('data-bid');
    fillBranchesOrHub(btype, bid);

    $('.branch_type').on('change', function () {
        var type = $(this).val();
        fillBranchesOrHub(type);
    });

    function fillBranchesOrHub(type, bid) {
        var url = '';
        if (type === 'hub') {
            url = Parcel_Destination.Url.allhubs;
            $('.hub_branch_label').html('Hub Name');
        } else {
            url = Parcel_Destination.Url.allecforhubs;
            $('.hub_branch_label').html('Branch Name');
        }
        Parcel_Destination.fillSelectOption(url, {}, '.branch_name', bid);
    }

    $('#manifest').on('click', function () {

        if (!validateParcels()) {
            return false;
        }
        var genManifest = $('#genManifest');
        genManifest.find('#dlg_location').val(parcels.to_branch_name);
        genManifest.find('#tbl_manifest > tbody').html(getParcelTableContent(parcels));

        $('#staff_info').hide();
        $('#btnGenerate').attr('disabled', true);
        genManifest.modal('show');
    });

    function validateParcels() {
        var chkboxes = $('.chk_next');
        var selected = false;
        var same_branch = true;
        parcels.waybills = [];
        var old_branch = '';
        $.each(chkboxes, function (i, chk) {

            var checked = $(chk).is(':checked');
            if (checked) {
                selected = true;
                var waybill = {};
                var tr = $(chk).closest('tr');
                if (!old_branch) {
                    old_branch = parcels.to_branch_id = $(tr).attr('data-to-branch-id');
                }
                waybill.number = $(tr).attr('data-waybill');
                waybill.final = TableHelper.getCellData('#next_dest', 5, $(tr).index());
                parcels.waybills.push(waybill);
                parcels.to_branch_id = $(tr).attr('data-to-branch-id');
                parcels.to_branch_name = TableHelper.getCellData('#next_dest', 4, $(tr).index());

                if (old_branch !== parcels.to_branch_id) {
                    same_branch = false;
                }
            }
        });

        if (!selected) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return false;
        }

        if (!same_branch) {
            alert('Manifest can only be generated for same next destination branch!');
            event.preventDefault();
            return false;
        }

        return true;
    }

    /**
     * @param parcels
     */
    function getParcelTableContent(parcels) {
        var html = '';
        $.each(parcels.waybills, function (i, waybill) {
            html += "<tr>";
            html += "<td>" + (i + 1) + "</td>";
            html += "<td>" + waybill.number + "</td>";
            html += "<td>" + waybill.final + "</td>";
            html += "</tr>";
        });
        return html;
    }

    $('#staff').on('keypress', function (event) {

        if (event.which == 13) {

            event.preventDefault();
            var staff_code = $(this).val();
            if (staff_code == '') {
                return;
            }
            Parcel_Destination.searchStaffDetails(staff_code);
        }
    });

    $('#btn_staff').on('click', function (event) {
        var staff_code = $('#staff').val();
        if (staff_code == '') {
            return;
        }
        Parcel_Destination.searchStaffDetails(staff_code);
        event.preventDefault();
    });

    $('#btnGenerate').on('click', function (event) {

        $('#payload').val(JSON.stringify(parcels));
    });

    $('#branch_name').on('change', function () {
        var name = $(this).val();
        if (name !== '') {
            name = $(this).find('option:selected').text();
        }
        TableHelper.setColumnData('#next_dest', 4, name, true);
    });

    $('#chk_all').on('click', function (event) {
        var is_checked = $(this).is(':checked');
        var checkboxes = $('#next_dest tr input:checkbox');
        if (is_checked) {
            $.each(checkboxes, function (i, chk) {
                $(chk).prop('checked', true);
            });
        } else {
            $.each(checkboxes, function (i, chk) {
                $(chk).prop('checked', false);
            });
        }
    });

    $("select#page_width").on('change', function (event) {
        $("form#records_filter").submit();
    });

    /**
     * Create Bag Button actions
     */
    $('#btnCreateBag').on('click', function () {

        if (!validateParcels()) {
            return false;
        }
        $("#parcels_destination").html(parcels.to_branch_name);
        var create_bag_modal = $('#createBag');
        create_bag_modal.find('#bag_parcels_table > tbody').html(getParcelTableContent(parcels));
        create_bag_modal.modal('show');
    });


    $('#btnBag').on('click', function (event) {
        if ($("#to_branch").val() == "") {
            alert("Please select next destination for bag");
            return false;
        }
        $(this).attr('disabled', true);
        event.preventDefault();
        Parcel_Destination.createBag(parcels);
    });

    // handle unsort action
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
});
