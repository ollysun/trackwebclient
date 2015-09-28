(function () {
    $(document).ready(function() {
        $("#state").change(function () {
            $("#city").html("<option>Loading...</option>");
            $.getJSON("/admin/cities?state_id=" + $(this).val(), function(data){
                var html = "";
                console.log(data.data);
                $(data.data).each(function (i, v) {
                    var option = new Option(v.name.toUpperCase(), v.id);
                    html += option.outerHTML;
                });
                $("#city").html(html);
            }).error(function() {

            });
        });
    });
})();