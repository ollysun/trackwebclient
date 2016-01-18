$(document).ready(function () {
    $('.print_btn').click(function () {
        var print_url = $(this).data('print_url');
        console.log(print_url);
        var create_task_url = $(this).data('create_task_url');
        console.log(create_task_url);

        $.ajax(print_url, {
            method: 'HEAD',
            success: function () {
                TrackPlusUtil.openInNewWindow(print_url);
            },
            error: function () {
                window.location.href = create_task_url;
            },
            cache: false
        });
    });
});

