/**
 * Created by RotelandO on 7/20/15.
 */
var Parcel_Destination = {

    Url: {
        'allhubs' : '/hubs/allhubs',
        'allecforhubs' : '/hubs/allecforhubs',
        'allroutesforhub' : '/hubs/allroutesforhub'
    },

    fillSelectOption: function(url, param, selectSelector) {
        $.get( url, param, function(response){
            if(response.status === 'success') {
                var html = '<option value="">Select Name...</option>';
                $.each(response.data, function(i, item) {
                    html += "<option value='" + item.id + "'>" + item.name.toUpperCase() + "</option>";
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

    Parcel_Destination.fillSelectOption(Parcel_Destination.Url.allecforhubs, {}, '#branch_name');

    $('#branch_type').on('change', function(){
        var type = $(this).val();
        var url = '';
        if(type === 'hub') {
            url = Parcel_Destination.Url.allhubs;
            $('#hub_branch_label').html('Hub Name');
        } else if(type === 'route') {
            url = Parcel_Destination.Url.allroutesforhub;
            $('#hub_branch_label').html('Route Name');
        } else {
            url = Parcel_Destination.Url.allecforhubs;
            $('#hub_branch_label').html('Branch Name');
        }
        Parcel_Destination.fillSelectOption(url, {}, '#branch_name');
    });

    $('.chk_next').on('click', function(event){
        var tr = $(this).closest('tr');
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
        }
    });

    $('#chk_all').on('click', function(event){
        var tr = null;
        var rowIndex = null;
        var waybill = null;
        var dest_value  = null;
        var destination = null;
        var is_checked = $(this).is(':checked');
        var checkboxes = $('#next_dest tr input:checkbox');
        if(is_checked) {
            $.each(checkboxes, function(i, chk){
                tr = $(chk).closest('tr');
                rowIndex = $(tr).index();
                waybill = $(tr).attr('data-waybill');
                dest_value = $('#branch_name').val();
                destination = (dest_value !== '') ? $('#branch_name').find('option:selected').text() : '';
                $(chk).prop('checked', true);
                TableHelper.setCellData('#next_dest', rowIndex, 4, destination);
            });
        } else {
            $.each(checkboxes, function(i, chk){
                tr = $(chk).closest('tr');
                rowIndex = $(tr).index();
                $(chk).removeAttr('value');
                $(chk).prop('checked', false);
                TableHelper.setCellData('#next_dest', rowIndex, 4, '');
            });
        }
    });

    $('#btn_apply_dest').on('click', function(event){

        var chkboxes = $('.chk_next');
        var selected = false;
        $.each(chkboxes, function(i, chk){
            if($(chk).is(':checked')) {
                selected = true;
            }
        });

        if(!selected) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return;
        }

        var dest_value = $('#branch_name').val();
        if(dest_value == '') {
            alert('Please select a ' + $('#hub_branch_label').html() + ' to proceed.');
            event.preventDefault();
            return;
        }

        $('#form_branch_type').val($("#branch_type").val());
        $('#form_branch_name').val(dest_value);
        $('#table_form').submit();
    });

    $('#branch_name').on('change', function(){
        var name = $(this).val();
        if(name !== '') {
            name = $(this).find('option:selected').text();
            var checkboxes = $('#next_dest tr input:checkbox');
            $.each(checkboxes, function(i, chk){
                var is_checked = $(chk).is(':checked');
                if(is_checked) {
                    tr = $(chk).closest('tr');
                    rowIndex = $(tr).index();
                    TableHelper.setCellData('#next_dest', rowIndex, 4, name);
                }
            });
        }
    });
});
