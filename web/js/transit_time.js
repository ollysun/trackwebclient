$(document).ready(function () {
    var table = $('#table').DataTable({
        scrollY: "500px",
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        searching: false,
        ordering: false,
        info: false
    });

    new $.fn.dataTable.FixedColumns(table, {
        leftColumns: 1,
        //rightColumns: 1
    });

    $('.zone_mapping').on('click', function () {
        var from_id = $(this).data("from");
        var to_id = $(this).data("to");
        var payload = $(this).data("payload");
        try {
            var d = (payload);
            $("#from").val(from_id);
            $("#to").val(to_id);
            $("#to_text").val(d.to_branch.name + ' - ' + d.to_branch.code);
            $("#from_text").val(d.from_branch.name + ' - ' + d.from_branch.code);
            $("#zone_mapping_id").val(d.id);
            $("#transit_time").val(d.hours);
            $('#editModal').modal();
        } catch (e) {
            console.log(e);
        }
    });

    $('.not_set').on('click', function () {
        var hubs = $('#hub_list').data('hubs');
        var from_id = $(this).data("from");
        var to_id = $(this).data("to");

        $("#from").val(from_id);
        $("#to").val(to_id);

        hubs.forEach(function(hub){
            if(hub.id == from_id && hub.id == to_id){
                $("#from_text").val(hub.name + ' - ' + hub.code);
                $("#to_text").val(hub.name + ' - ' + hub.code);
            }else  if(hub.id == from_id){
                $("#from_text").val(hub.name + ' - ' + hub.code);
            }else if(hub.id == to_id){
                $("#to_text").val(hub.name + ' - ' + hub.code);
            }
        });

        $('#editTransitTimeModal').modal();


    });

    $("#remove_mapping").unbind("click").on("click", function () {
        var data = $("#update_transit_time_mapping_form").serialize();
        if (confirm("Are you sure you want to remove this mapping?")) {
            Hub.postToServer("removemapping", data, function (response) {
                window.location.href = 'managetransittime';
            });
        }
    });
    $("#update_mapping").unbind("click").on("click", function () {
        var that = this;
        $(that).html("Updating... Please wait.");
        var data = $("#update_transit_time_mapping_form").serialize();
        Hub.postToServer("updatemapping", data, function (response) {
            window.location.href = 'managetransittime';
        });

    });

});