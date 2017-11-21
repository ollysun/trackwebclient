var cloneBillingPlan = $("#clone-billing-plan");
var cloneDetails = $("#clone-details");
cloneBillingPlan.click(function () {
    if (cloneBillingPlan.is(':checked')) {
        $.get("/billing/getallbillingplannames",'','html').success(function (data) {
            cloneDetails.html(data);
        }).error(function(){
            cloneDetails.html('something went wrong');
        });
    } else {
        cloneDetails.html("");
    }
});


$('.editbtn').click(function(){
    var _this = $(this);
    $('#edit_name').val($(this).attr('data-name'));
    $('#edit_id').val($(this).attr('data-id'));

    $("#edit_company option").prop('selected', false).filter(function() {
        return $(this).val() == 1;
    }).prop('selected', true);

    $('#edit_discount').val($(this).attr('data-discount'));

})


$('.linkCompany').click(function () {
    var _this = $(this);
    $('#plan_name').val(_this.attr('data-plan_name'));
    $('#plan_id').val(_this.attr('data-plan_id'));
});


var ViewModel = function() {
    var self = this;


    self.companies = ko.observableArray();
    self.currentBillingPlan = null;

    self.viewCompanies = function (billing_plan_id) {
        self.currentBillingPlan = billing_plan_id;
        //fetch billing plans
        self.companies([]);
        $.ajax({
            url: 'billing/getcompaniesbyplan?billing_plan_id=' + billing_plan_id,
            type: 'GET',
            success: function (result) {
                if (result.status == 'success') {
                    self.companies(result.data);
                } else {
                    alert('Unable to fetch companies for this account');
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    };

    self.remove = function (company_id) {
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

}

ko.applyBindings(new ViewModel());