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
} );