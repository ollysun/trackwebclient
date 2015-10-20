(function () {
    $(document).ready(function () {

        var editCompanyForm = $("#editCompanyForm");

        $("[data-state]").change(function () {
            var target = $(this).data('target');
            var targetElem = $("#" + target);
            if ($(this).val() != "") {
                targetElem.html("<option>Loading...</option>");
                $.getJSON("/admin/cities?state_id=" + $(this).val(), function (data) {
                    var html = "";
                    $(data.data).each(function (i, v) {
                        var option = new Option(v.name.toUpperCase(), v.id);
                        html += option.outerHTML;
                    });
                    var selected = targetElem.data('selected');
                    targetElem.html(html).val(selected);
                }).error(function () {

                });
            }
        });
        
        $("[data-load_staff]").click(function () {
            var _this = $(this);
            var staff = $("#" + _this.data('staff')).val();
            if(staff != "") {
                $.getJSON("/admin/getstaff?staff_id=" + staff, function (data) {
                    if(data.status == "success" && data.data && data.data.fullname && data.data.id) {
                        $("#" + _this.data('staff_name')).html(data.data.fullname.toUpperCase());
                        $("#" + _this.data('staff_id')).val(data.data.id);
                    }
                });
            }
        });
        
        $("#enableSecondaryContact").change(function () {
            $("[data-secondary_contact]").attr("disabled", !($(this).prop("checked")));

            if($(this).prop("checked")) {
                $("[data-secondary_contact]").addClass("validate").addClass("required");
            } else {
                $("[data-secondary_contact]").removeClass("validate").addClass("required");
            }
        });

        $(".editCompany").click(function () {
            var data = this.dataset;

            var fieldsMap = [
                {
                    'field' : 'name',
                    'name' : 'company[name]',
                    'type' : 'input'
                },
                {
                    'field' : 'id',
                    'name' : 'company[id]',
                    'type' : 'input'
                },
                {
                    'field' : 'email',
                    'name' : 'company[email]',
                    'type' : 'input'
                },
                {
                    'field' : 'phone_number',
                    'name' : 'company[phone_number]',
                    'type' : 'input'
                },
                {
                    'field' : 'address',
                    'name' : 'company[address]',
                    'type' : 'input'
                },
                {
                    'field' : 'reg_no',
                    'name' : 'company[reg_no]',
                    'type' : 'input'
                },
                {
                    'field' : 'city_id',
                    'name' : 'company[city_id]',
                    'type' : 'select'
                },
                {
                    'field' : 'state_id',
                    'name' : 'company[state]',
                    'type' : 'select'
                },
                {
                    'field' : 'relations_officer_staff_id',
                    'name' : 'company[relations_officer_staff_id]',
                    'type' : 'input'
                },
                {
                    'field' : 'relations_officer_id',
                    'name' : 'company[relations_officer_id]',
                    'type' : 'input'
                }
            ];

            $(fieldsMap).each(function (i, v) {
                if(v.type == 'select'){
                    editCompanyForm.find(v.type + "[name='" + v.name + "']").data('selected', data[v.field]);
                    editCompanyForm.find(v.type + "[name='" + v.name + "'] option").each(function (index, option) {
                        if ($(option).val() == data[v.field]) {
                            $(option).attr("selected", true);
                        }
                    }).trigger('change');
                } else {
                    if(data[v.field] != 'NULL') {
                        editCompanyForm.find(v.type + "[name='" + v.name + "']").val(data[v.field]);
                    }
                }
            });
        });
    });
})();