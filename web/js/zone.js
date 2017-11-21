$(document).ready(function () {
    $("button[data-target='#editModal']").on('click', function (event) {
        target = $(this).attr('data-target');
        _id = $(this).attr('data-id');
        $(target+" input[name='zone_name']").val($("td[class='n"+_id+"']").text());
        $(target+" input[name='zone_code']").val($("td[class='c"+_id+"']").text());
        $(target+" textarea[name='zone_desc']").val($("td[class='d"+_id+"']").text());
        $(target+" input[name='id']").val(_id);
    });
});