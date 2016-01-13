/**
 * Created by adeyemi.olaoye on 15/12/2015.
 */

$('#chk_all').on('click', function () {
    var is_checked = $(this).is(':checked');
    var checkboxes = $('.chk_next');
    if (is_checked) {
        $.each(checkboxes, function (i, chk) {
            $(chk).prop('checked', true);
        });
    } else {
        $.each(checkboxes, function (i, chk) {
            $(chk).prop('checked', false);
        });
    }
});

$('.chk_next').on('click', function () {
    var is_checked = $(this).is(':checked');
    if (!is_checked) {
        $('#chk_all').prop('checked', false);
    }

    if ($('.chk_next:not(:checked)').length == 0) {
        $('#chk_all').prop('checked', true);
    }
});