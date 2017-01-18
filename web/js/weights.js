$(document).ready(function () {
    $("#chbx_w_all").change(function () {
        $("input:checkbox").prop("checked", $(this).prop("checked"));
    });

    $("button[data-target='#editModal']").on('click', function (event) {
        target = $(this).attr('data-target');
        _id = $(this).attr('data-id');
        $(target+" input[name='min_weight']").val($("td[class='l"+_id+"']").text());
        $(target+" input[name='max_weight']").val($("td[class='m"+_id+"']").text());
        $(target+" input[name='increment_weight']").val($("td[class='i"+_id+"']").text());
        $(target+" input[name='id']").val(_id);
    });

    $(".deleteWeightRange").click(function (e) {
        var prompt = confirm("Are you sure you want to delete this weight range?");
        if(prompt) {
            $(this).parent().submit();
            return;
        }

        e.preventDefault();
        e.stopPropagation();
    });

    $('#deleteRangeBtn').click(function(){
        var chkboxes = $(".checkable:checked");

        if (!chkboxes.length) {
            alert("You must select at least one weight range!");
            event.preventDefault();
            return false;
        }
        var ranges = [];
        $.each(chkboxes, function (i, chk) {
            ranges[i] = $(this).data("id");
        });

        $('#rangeIds').val(ranges.join());
        return true;
    });
});