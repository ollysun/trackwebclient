/**
 * Created by RotelandO on 7/20/15.
 */
var Parcel_Destination = {

    Url: {
        'allhubs' : '/site/allhubs',
        'allecforhubs' : '/site/allecforhubs'
    },

    fillSelectOption: function(url, param, selectSelector) {
        $.get( url, param, function(response){
            if(response.status === 'success') {
                var html = '<option>Select Name...</option>';
                $.each(response.data, function(i, item) {
                    html += "<option value='" + item.id + "'>" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).html(html);
            }
        });
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
        } else {
            url = Parcel_Destination.Url.allecforhubs;
            $('#hub_branch_label').html('Branch Name');
        }
        Parcel_Destination.fillSelectOption(url, {}, '#branch_name');
    });

    $('.chk_next').on('click', function(event){
        var waybill = $(this).closest('tr').attr('data-waybill');
        var curr = $(this).attr('value');
        if(curr) {
            if(curr === waybill) {
                $(this).removeAttr('value');
            } else {
                $(this).attr('value', waybill);
            }
        } else {
            $(this).attr('value', waybill);
        }
    });

    $('#btn_apply_dest').on('click', function(event){

        var chkboxes = $('.chk_next');
        var selected = false;
        $.each(chkboxes, function(i, chk){
            if($(chk).attr('value')) {
                selected = true;
            }
        });

        if(!selected) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return;
        }
    });
});
