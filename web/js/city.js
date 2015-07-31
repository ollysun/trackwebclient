$(document).ready(function () {
    $("button[data-target='#editModal']").on('click', function (event) {
        target = $(this).attr('data-target');
        _id = $(this).attr('data-id');
        $(target+" input[name='city_name']").val($("td[class='n"+_id+"']").text());
        $(target+" select[name='state']").val($(this).attr('data-state-id'));
        $(target+" input[name='id']").val(_id);
    });
});