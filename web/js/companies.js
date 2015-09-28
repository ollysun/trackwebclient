(function () {
    $(document).ready(function () {
        $("#state").change(function () {
            if ($(this).val() != "") {
                $("#city").html("<option>Loading...</option>");
                $.getJSON("/admin/cities?state_id=" + $(this).val(), function (data) {
                    var html = "";
                    console.log(data.data);
                    $(data.data).each(function (i, v) {
                        var option = new Option(v.name.toUpperCase(), v.id);
                        html += option.outerHTML;
                    });
                    $("#city").html(html);
                }).error(function () {

                });
            }
        });
        
        $("#loadStaff").click(function () {
            var staff = $("#staff").val();
            if(staff != "") {
                $.getJSON("/admin/getstaff?staff_id=" + staff, function (data) {
                    if(data.status == "success" && data.data && data.data.fullname && data.data.id) {
                        $("#staffName").html(data.data.fullname.toUpperCase());
                        $("#staffId").val(data.data.id);
                    }
                });
            }
        });
    });
})();