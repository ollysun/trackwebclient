/**
 * Created by ELACHI on 10/30/2016.
 */
$(document).ready(function () {

    $("select#filter_hub_id").on('change', function (event) {
        $("form#filter").submit();
    });
    $("select#filter_state_id").on('change', function (event) {
        $("form#state_filter").submit();
    });

    $("button[data-target='#bm_editModal']").on('click', function (event) {
        _id = $(this).attr('data-id');

        $("select[name='region_id']").val($(this).attr('data-region-id'));
        $("input[name='staff_id']").val($(this).attr('data-staff-id'));
        $("input[name='id']").val(_id);
    });
});