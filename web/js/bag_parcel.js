/**
 * Created by RotelandO on 17/10/2015.
 */

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
    })
})