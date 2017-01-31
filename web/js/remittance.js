/**
 * Created by ELACHI on 1/25/2017.
 */
$(document).ready(function () {
    $("#chbx_w_all").change(function () {
        $("input:checkbox").prop("checked", $(this).prop("checked"));
    });

    $('#submit_btn').click(function () {
        $('#form').submit();
    });

    //view details
});