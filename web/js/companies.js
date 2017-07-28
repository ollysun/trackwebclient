(function () {
    var nextIndex = 0;
    var BillingPlan = {
        addPlan: function(tableBodySelector, planId, isdefault){
            var plan = _.find(billing_plans, function(billing_plan){
                return billing_plan.id == planId;
            });
            var actionBtn = '<button type="button" class="btn btn-danger btn_remove" data-index="' + (planId) + '">Remove</button>';
            var planIdInput = '<input type="hidden" name="company_billing_plan[' + planId + '][billing_plan_id]" value="' + plan.id + '"/>';
            var isDefaultInput = '<input type="hidden" name="company_billing_plan[' + planId + '][is_default]" value="' + isdefault + '"/>';
            var tr = '<tr data-index="' + planId + '">' +
                        '<td>' + plan.name.toUpperCase() + '</td>' +
                        '<td>' + (isdefault == 1?'Yes':'No') + '</td> ' +
                        '<td>' + actionBtn + planIdInput + isDefaultInput + '</td>' +
                     '</tr>';

            $(tableBodySelector).append(tr);

            nextIndex++;
        },

        removePlan:function(tableBodySelector, planId){
            $(tableBodySelector).remove('tr[data-index="' + planId + '"]')
        }
    };

    $(document).ready(function () {
        var editCompanyForm = $("#editCompanyForm");

        $('#newRegionId').change(function(){
            var selectedRegionId = $(this).val();
            $('#newBusinessZoneId').html('<option>Loading...</option>');
            var html  = '';
            var newZones = _.filter(businessZones, function(zone){
                return zone.region_id == selectedRegionId;
            })
            $(newZones).each(function (i, v) {
                var option = new Option(v.name.toUpperCase(), v.id);
                html += option.outerHTML;
            });
            $('#newBusinessZoneId').html(html);
        });

        $('#editRegionId').change(function () {
            var selectedRegionId = $(this).val();
            $('#editBusinessZoneId').html('<option>Loading</option>');
            var html = '<option>Select Business Zone</option>';
            var newZones = _.filter(businessZones, function(zone){
                return zone.region_id == selectedRegionId;
            })
            $(newZones).each(function (i, v) {
                var option = new Option(v.name.toUpperCase(), v.id, false, v.id == business_zone_id);
                html += option.outerHTML;
            });
            $('#editBusinessZoneId').html(html);
        })

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

        var business_zone_id = undefined;
        $(".editCompany").click(function () {
            var data = this.dataset;
            business_zone_id = $(this).attr('data-business_zone_id');

            var zone = _.find(businessZones, function(i, v){
               return v.id = business_zone_id;
            });

            $('#editRegonId').html('');
            var html = new Option('Select Region', '', true).outerHTML;
            $(regions).each(function (i, v) {
                var selected = zone != undefined && v.id == zone.region_id;
                console.log(selected);
                var option = new Option(v.name, v.id, false, selected).outerHTML;
                html += option;
            });
            $('#editRegonId').html(html);

            if(zone != undefined) {
                $('#editBusinessZoneId').html('<option>Loading</option>');
                var html = '';
                var newZones = _.filter(businessZones, function (zone) {
                    return zone.region_id == zone.region_id;
                })
                $(newZones).each(function (i, v) {
                    var option = new Option(v.name.toUpperCase(), v.id, false, v.id == zone.id);
                    html += option.outerHTML;
                });
                $('#editBusinessZoneId').html(html);
            }

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
                    'field' : 'business_zone_id',
                    'name' : 'company[business_zone_id]',
                    'type' : 'select'
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
                },
                {
                    'field': 'credit_limit',
                    'name': 'company[credit_limit]',
                    'type' : 'input'
                },
                {
                    'field': 'credit_balance',
                    'name': 'company[credit_balance]',
                    'type' : 'input'
                },
                {
                    'field': 'override_credit',
                    'name': 'company[override_credit]',
                    'type' : 'input'
                },
                {
                    'field': 'extra_info',
                    'name': 'company[extra_info]',
                    'type' : 'textarea'
                }
            ];
            //
            $(fieldsMap).each(function (i, v) {
                if(v.type == 'select'){
                    editCompanyForm.find(v.type + "[name='" + v.name + "']").data('selected', data[v.field]);
                    editCompanyForm.find(v.type + "[name='" + v.name + "']").val(data[v.field]).trigger('change');
                } else {
                    if(data[v.field] != 'NULL' && data[v.field]=='checked'){
                        editCompanyForm.find(v.type + "[name='" + v.name + "']").attr('checked',data[v.field])
                    }
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

        $('#resetLimit').on('click', function() {
            var data = {};
            data.company_id = document.getElementById('cid').value;
            data.status = 'resetLimit';
            if(!confirm("This company's credit balance is about to be reset")){
                return;
            }

            $.ajax({
                url: '/admin/creditreset',
                type: 'POST',
                dataType: 'JSON',
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.message) {
                        alert('Reset was successful ' + response.message);
                        document.getElementById('creditRemaining').value=response.message
                    } else {
                        location.reload();
                    }
                },
                error: function (err) {
                    alert('An error occurred when trying Reset company Credit Limit. Please try again later later');
                }
            })

            return false
        })

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

        $('#new_btn_add_plaan').click(function () {
            var is_default = $('#new_is_default').val();
            var plan_id = $('#new_billing_plan_id').val();
            BillingPlan.addPlan('#new_billing_plans_list', plan_id, is_default);
        });

        $('#edit_btn_add_plan').click(function () {
            var is_default = $('#edit_is_default').val();
            var plan_id = $('#edit_billing_plan_id').val();
            BillingPlan.addPlan('#edit_billing_plans_list', plan_id, is_default);
        });

        $('.btn_remove').on('click', function(){
            alert($(this).attr('data-index'));
        });

        $('.btn_remove').click(function () {
           BillingPlan.removePlan('#new_billing_plans_list', $(this).attr('data-index'));
        });

    });
})();


ko.observableArray.fn.find = function(prop, data) {
    var valueToMatch = data[prop];
    return ko.utils.arrayFirst(this(), function(item) {
        return item[prop] === valueToMatch;
    });
};

var ViewModel = function() {
    var self = this;


    self.plan_id = 0;
    self.is_default_plan = 0;

    self.all_plans = ko.observableArray(ko.utils.arrayMap(billing_plans, function (plan) {
        return {id: plan.id, name: plan.name};
    }));

    self.is_default_options = ko.observableArray([{value:0, text:'No'}, {value: 1, text:'Yes'}]);

    self.plans = ko.observableArray();

    self.addPlan = function () {
        if(self.plans.find("id", self.plan_id) != undefined){
            alert('You have already added this plan');
            return;
        }

        /*ko.utils.arrayForEach(self.plans, function (plan) {
            if(plan.plan.id == self.plan_id){
                alert('You have already added this plan');
                return;
            }
        });*/

        if(self.is_default_plan == 1){
            ko.utils.arrayForEach(self.plans, function(plan){
                plan.is_default_plan = 0;
            });
            /*
            $.forEach(self.plans, function (index, plan) {
                plan.is_default_plan = 0;
            });*/
        }

        var plan = _.find(billing_plans, function(billing_plan){
            return billing_plan.id == self.plan_id;
        });
        plan.is_default_plan = self.is_default_plan;
        self.plans.push(plan);
        //self.plans.push({'plan': plan, 'is_default_plan': self.is_default_plan});
        self.plan_id = null;
        self.is_default_plan = 0;
    }

    self.markAsDefaultPlan = function(plan_id){

    }


    self.removePlan = function (plan_id) {
        $.ajax({
            url: 'billing/removecompanyfromplan?billing_plan_id=' + self.currentBillingPlan + '&company_id=' + company_id,
            type: 'GET',
            success: function (result) {
                if (result.status == 'success') {
                    self.viewCompanies(self.currentBillingPlan);
                    alert(result.data.message);
                } else {
                    alert('Unable to remove company');
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    self.regionId = null;
    self.regions = ko.observableArray(ko.utils.arrayMap(regions, function(region){
        return {id: region.id, name: region.name};
    }));

    self.zoneId = null;
    self.zones = ko.observableArray(ko.utils.arrayMap(businessZones, function(zone){
        return {id: zone.id, regionId:zone.region_id, name:zone.name};
    }));



    self.filteredZones = function(){
        return self.zones().filter(function(zone){
            return zone.regionId == self.regionId;
        });
    }


}

ko.applyBindings(new ViewModel());