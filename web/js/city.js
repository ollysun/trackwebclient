$(document).ready(function () {
    $("button[data-confirm]").click(function (e) {
        var prompt = confirm("Are you sure you want to delete this mapping?");
        if(!prompt) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).parent().submit();
    });

    $(".edit-city").click(function () {
        $('#id').val($(this).data('id'));
        $('#city_name').val($(this).data('name'));
        $('#transit_time').val($(this).data('transit-time'));
        $('#state').val($(this).data('state-id'));
        $('#branch_id').val($(this).data('branch-id'));
    })
});