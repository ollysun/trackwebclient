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
            $("#zone_id").val(d.zone_id);
            $('#editModal').modal();
        } catch (e) {
            console.log(e);
        }
    });

    $('.not_set').on('click', function () {
        var from_id = $(this).data("from");
        var to_id = $(this).data("to");
        var win = window.open('/admin/hubmapping?hub=' + from_id + '&to=' + to_id, '_blank');
        if(win){
            win.focus();
        }
    });

    $("#remove_mapping").unbind("click").on("click", function () {
        var data = $("#update_zone_mapping_form").serialize();
        if (confirm("Are you sure you want to remove this mapping?")) {
            Hub.postToServer("removemapping", data, function (response) {
                window.location.reload();
            });
        }
    });
    $("#update_mapping").unbind("click").on("click", function () {
        var that = this;
        $(that).html("Updating... Please wait.");
        var data = $("#update_zone_mapping_form").serialize();
        Hub.postToServer("updatemapping", data, function (response) {
            window.location.reload();
        });

    });

});