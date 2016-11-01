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
                        if(_this.data('property') == 'staff_id'){
                            $("#" + _this.data('staff_id')).val(data.data.staff_id);
                        }else {
                            $("#" + _this.data('staff_id')).val(data.data.id);
                        }

                    }else{
                        alert('Invalid staff id');
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
                },
                {
                    'field' : 'business_manager_staff_id',
                    'name' : 'company[business_manager_staff_id]',
                    'type' : 'input'
                },
                {
                    'field': 'account_type_id',
                    'name': 'company[account_type_id]',
                    'type' : 'input'
                },
                {
                    'field': 'discount',
                    'name': 'company[discount]',
                    'type' : 'input'
                }
            ];

            $(fieldsMap).each(function (i, v) {
                if(v.type == 'select'){
                    editCompanyForm.find(v.type + "[name='" + v.name + "']").data('selected', data[v.field]);
                    editCompanyForm.find(v.type + "[name='" + v.name + "']").val(data[v.field]).trigger('change');
                } else {
                    if(data[v.field] != 'NULL') {
                        editCompanyForm.find(v.type + "[name='" + v.name + "']").val(data[v.field]);
                    }
                }
            });
        });

        var createCompanyForm = $('#createCompanyForm');
        if(previous_data !== 0){

            createCompanyForm.find('select').each(function() {
                $(this).val($(this).data('selected')).trigger('change');
            });
            $('#myModal').modal('show');
        }

        $('.activation').on('click', function() {
            var data = {};
            data.status = $(this).attr('data-status');
            var action_text = data.status == 1 ? 'activate' : 'deactivate';
            data.company_id = $(this).attr('data-id');

            if(!confirm("This company will be " + action_text + "d")){
                return;
            }

            $.ajax({
                url: '/admin/activation',
                type: 'POST',
                dataType: 'JSON',
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.status == 'success') {
                        alert('Company successfully ' + action_text + 'd !');
                        location.reload();
                    } else {
                        alert('An error occurred when trying to ' + action_text + ' company. Please try again later');
                    }
                },
                error: function (err) {
                    alert('An error occurred when trying to ' + action_text + ' company. Please try again later');
                }
            })
        });

        $(".resetPassword").click(function () {
            var form = $("#resetCompanyAdminPasswordForm");
            form.find("input[name=company_id]").val($(this).data('company-id'));
        });
    });
})();