$(document).on("click", ".OpenDialog", function () {
    var parcelid = $(this).data('id');
    var ref_no = $(this).data('ref_no');
    $(".modal-footer #parcelId").val( parcelid );
    $("#parcel-number").html(ref_no);

   //alert(ref_no);
});
$(document).on("click", ".OpenDialogTrack", function () {
    var exportparcelId = $(this).data('id');
    var ref_no = $(this).data('ref_no');
    $(".modal-footer #exportparcelId").val(exportparcelId );
    $("#parcel-number").html(ref_no);
});
