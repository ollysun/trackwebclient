$(document).ready(function () {
    $("button[data-target='#addCountryModel']").on('click', function (event) {
        target = $(this).attr('data-target');
        _id = $(this).attr('data-id');
        $(target+" input[name='zone_desc']").val($("td[class='d"+_id+"']").text());
        $(target+" input[name='zone_id']").val(_id);
    });
});