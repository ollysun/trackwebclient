$(document).ready(function () {
    $("button[data-confirm]").click(function (e) {
        var prompt = confirm("Are you sure you want to delete this mapping?");
        if(!prompt) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).parent().submit();
    });
});