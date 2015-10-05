(function () {
    $(document).ready(function () {
        $("#receiver_state_id").change(function () {
            if ($(this).val() != "") {
                var target = $(this).data('city_target');
                var targetId = "#" + target;
                $(targetId).html("<option>Loading...</option>");
                $.getJSON("/corporate/locations/cities?state_id=" + $(this).val(), function (data) {
                    var html = "";
                    $(data.data).each(function (i, v) {
                        var option = new Option(v.name.toUpperCase(), v.id);
                        html += option.outerHTML;
                    });
                    $(targetId).html(html);
                }).error(function () {

                });
            }
        });
    });

})();