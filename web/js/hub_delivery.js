/**
 * Created by RotelandO on 7/25/15.
 */

/**
 * Created by RotelandO on 7/20/15.
 */

var parcels = {
    waybills: [],
    to_branch_id: '',
    to_branch_name: ''
};

var Parcel_Destination = {

    Url: {
        'allhubs' : '/hubs/allhubs',
        'allecforhubs' : '/hubs/allecforhubs'
    },

    fillSelectOption: function(url, param, selectSelector, selectedValue) {
        $.get( url, param, function(response){
            if(response.status === 'success') {
                var html = '<option value="">Select Name...</option>';
                $.each(response.data, function(i, item) {
                    selected = (selectedValue == item.id) ? 'selected="selected"' : '';
                    html += "<option value='" + item.id + "'" + selected + ">" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).html(html);
            }
        });
    }
};

var TableHelper = {


    renumberTableSerialNo: function (tableSelector, colIndex) {


        var trs = $(tableSelector + ' tbody').children();
        if(!this.isDefined(colIndex)) {
            colIndex = 0;
        }
        for(var i = 0; i < trs.length; i++) {
            var children = $(trs[i]).find('td:eq(' + colIndex + ')').first().html(i+1);
        }
    },

    isItemInTableColumn: function(tableSelector, colIndex, searchItem) {

        var tempText = '';
        var found = false;
        var trs = $(tableSelector + ' tbody').children();
        if(!this.isDefined(colIndex)) {
            colIndex = 0;
        }

        $.each(trs, function(index, tr){
            tempText = $.trim($(tr).children().eq(colIndex).html());
            if((tempText) == searchItem) {
                found = true;
            }
        });
        return found;
    },

    getColumTextData: function(tableSelector, colIndex) {

        var items = [];
        var trs = $(tableSelector + ' tbody').children();
        if(!this.isDefined(colIndex)) {
            colIndex = 0;
        }

        $.each(trs, function(index, tr){
            items.push($.trim($(tr).children().eq(colIndex).html()));
        });
        return items;
    },

    setCellData: function(tableSelector, rowIndex, colIndex, content) {

        if(!this.isDefined(rowIndex) || !this.isDefined(colIndex)) {
            return;
        }
        var trs = $(tableSelector + ' tbody').children();
        $(trs).eq(rowIndex).children().eq(colIndex).html(content);
    },

    setColumnData: function(tableSelector, colIndex, content, withCheckbox) {

        if(!this.isDefined(colIndex)) {
            return;
        }
        var trs = $(tableSelector + ' tbody').children();

        $.each(trs, function(rowIndex, tr){
            if(withCheckbox) {
                var chkValue = $(tr).children().find(':checkbox').attr('value');
                if(chkValue) {
                    $(tr).children().eq(colIndex).html(content);
                }
            } else {
                $(tr).children().eq(colIndex).html(content);
            }
        });
    },

    getCellData: function(tableSelector, colIndex, rowIndex) {
        var cellData = '';
        var trs = $(tableSelector + ' tbody').children();
        if(!this.isDefined(colIndex)) {
            colIndex = 0;
        }

        $.each(trs, function(index, tr){
            if(index === rowIndex) {
                cellData = $.trim($(tr).children().eq(colIndex).html());
            }
        });

        return cellData;
    },

    isCallback: function(callback) {
        return (callback && typeof(callback) === typeof(Function));
    },

    isDefined: function (value) {

        return (typeof value !== 'undefined');
    }
};

$(document).ready(function(){

    var btype = $('#branch_type').find('option:selected').val();
    var bid = $('#branch_name').attr('data-bid');
    fillBranchesOrHub(btype, bid);

    $('#branch_type').on('change', function(){
        var type = $(this).val();
        fillBranchesOrHub(type);
    });

    function fillBranchesOrHub(type, bid) {
        var url = '';
        if (type === 'hub') {
            url = Parcel_Destination.Url.allhubs;
            $('#hub_branch_label').html('Hub Name');
        } else {
            url = Parcel_Destination.Url.allecforhubs;
            $('#hub_branch_label').html('Branch Name');
        }
        Parcel_Destination.fillSelectOption(url, {}, '#branch_name', bid);
    }

    $('#manifest').on('click', function(event) {

        var chkboxes = $('.chk_next');
        var selected = false;
        var same_branch = true;
        parcels.waybills = [];
        var old_branch = '';
        $.each(chkboxes, function(i, chk){

            if($(chk).is(':checked')) {
                selected = true;
                var waybill = {};
                var tr = $(chk).closest('tr');
                if(i == 0) {
                    old_branch = parcels.to_branch_id = $(tr).attr('data-to-branch-id');
                }
                waybill.number = $(tr).attr('data-waybill');
                waybill.final = TableHelper.getCellData('#next_dest', 5, $(tr).index());
                parcels.waybills.push(waybill);
                parcels.to_branch_id = $(tr).attr('data-to-branch-id');
                parcels.to_branch_name = TableHelper.getCellData('#next_dest', 4, $(tr).index());

                if(old_branch !== parcels.to_branch_id) {
                    same_branch = false;
                }
            }
        });

        if(!selected) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return;
        }

        if(!same_branch) {
            alert('Manifest can only be generated for same next destination branch!');
            event.preventDefault();
            return;
        }

        populateDialog(parcels);
        $('#genManifest').modal('show');
    });

    function populateDialog(parcels) {

        $('#dlg_location').val(parcels.to_branch_name);
        var html = '';
        $.each(parcels.waybills, function(i, waybill){
            html += "<tr>";
            html += "<td>" + (i+1) + "</td>";
            html += "<td>" + waybill.number + "</td>";
            html += "<td>" + waybill.final + "</td>";
            html += "</tr>";
        });
        $('#tbl_manifest > tbody').html(html);
    }

    $('.chk_next').on('click', function(event){
            /*var tr = $(this).closest('tr');
             var rowIndex = $(tr).index();
             var waybill = $(tr).attr('data-waybill');
             var dest_value = $('#branch_name').val();
             var destination = (dest_value !== '') ? $('#branch_name').find('option:selected').text() : '';
             var curr = $(this).attr('value');
             if(curr) {
             if(curr === waybill) {
             $(this).removeAttr('value');
             TableHelper.setCellData('#next_dest', rowIndex, 4, '');
             } else {
             $(this).attr('value', waybill);
             TableHelper.setCellData('#next_dest', rowIndex, 4, destination);
             }
             } else {
             $(this).attr('value', waybill);
             TableHelper.setCellData('#next_dest', rowIndex, 4, destination);
             }*/
    });

    $('#branch_name').on('change', function(){
        var name = $(this).val();
        if(name !== '') {
            name = $(this).find('option:selected').text();
        }
        TableHelper.setColumnData('#next_dest', 4, name, true);
    });
});
