(function () {
    $(document).ready(function () {
        $("#receiver_state_id, #pickup_state_id, #destination_state_id").change(function () {
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

        $("#company_filter").change(function () {
            $("#company_filter_form").submit();
        });

        $("#clearFilters").click(function () {
            var currentUrl = window.location.href;
            var tempArr = currentUrl.split('?');
            window.location.href = tempArr[0];
        });
    });
})();