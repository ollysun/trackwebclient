/**
 * Created by RotelandO on 17/10/2015.
 */

var Bag = {

    Url: {
        removeFromBag: '/shipments/removefrombag'
    },
    waybill: {
        id: '',
        linked_waybills: []
    },

    removeItems: function() {

        $.ajax({
            url: Bag.Url.removeFromBag,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(this.waybill),
            success: function (result) {
                if (result.status == 'success' || result.status == 200) {
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            },
            error: function (err) {
                console.log(err);
            }
        })
    }
};

$(document).ready(function(){
    $('#btnOpenBag').on('click', function(event){

        var unbag_url = $(this).attr('data-href');
        var waybill_number = $(this).attr('data-waybill');
        bootbox.dialog({
            message: "Do you want to open the bag ( <strong>#" + waybill_number + "</strong> ) ?",
            title: "Open Bag",
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-success",
                    callback: function() {
                        window.location = unbag_url;
                    }
                },
                info: {
                    label: "No",
                    className: "btn-info",
                    callback: function() {}
                }
            }
        });
    });

    $('#btnRemoveItem').on('click', function(event){

        $('#removeItem').modal('show');
    });

    $("#chbx_w_all").change(function () {
        $("input:checkbox").prop("checked", $(this).prop("checked"));
    });

    $('#btnDlgRemove').on('click', function(event){

        if(!atLeastOneSelected()) {
            return false;
        }

        Bag.waybill.id = $('#remove_bag_waybill').val();
        Bag.removeItems();
    });

    function atLeastOneSelected() {

        var chkboxes = $('.chk_next');
        var selected = false;
        Bag.waybill.linked_waybills = [];

        $.each(chkboxes, function (i, chk) {

            var checked = $(chk).is(':checked');
            if (checked) {
                selected = true;
                var tr = $(chk).closest('tr');
                var waybill = $(tr).attr('data-waybill');
                Bag.waybill.linked_waybills.push(waybill);
            }
        });

        if (!selected) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return false;
        }

        return true;
    }
});