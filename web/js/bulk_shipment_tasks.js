$(document).ready(function () {
    $('.print_btn').click(function () {
        var print_url = $(this).data('print_url');
        var create_task_url = $(this).data('create_task_url');
        var externalWindow = window.open('', 'newwin');
        externalWindow.document.write('Checking for waybills...');

        $.ajax(print_url, {
            method: 'HEAD',
            success: function () {
                externalWindow.document.write('');
                TrackPlusUtil.openExternalLinkInNewWindow(externalWindow, print_url, 'Loading Waybills...');
            },
            error: function () {
                externalWindow.close();
                window.location.href = create_task_url;
            },
            cache: false
        });
    });
});

