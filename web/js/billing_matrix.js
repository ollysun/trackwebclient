$(document).ready(function() {
    var table = $('#table').DataTable( {
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        searching:      false,
        ordering:       false,
        info:           false
    } );

    new $.fn.dataTable.FixedColumns( table, {
        leftColumns: 1,
        //rightColumns: 1
    } );

    $('.matrix_cell').on('click',function(){
        var from_id = $(this).data("from");
        var to_id = $(this).data("to");
        var payload = $(this).data("payload");
        try{
            var d = (payload);
            $("#from").val(from_id);
            $("#to").val(to_id);
            $("#to_text").val(d.to_branch.name + ' - '+d.to_branch.code);
            $("#from_text").val(d.from_branch.name + ' - '+d.from_branch.code);
            $('#editModal').modal();
        }catch(e){
            console.log(e);
        }

    });
} );