$(document).ready(function () {
    var editStaffForm = $("#createStaffForm");
    $("#addNewStaffBtn").click(function () {
        $("#myModalLabel").html("Add a New Staff Account");
        editStaffForm.find("button[type='submit']").html('Create Staff Account');
    });

    $(".editStaff").click(function () {
        var data = this.dataset;
        var fullname = data.fullname;

        // Update Form title and button text
        $("#myModalLabel").html("Edit Staff - " + data.fullname.toUpperCase());
        editStaffForm.find("button[type='submit']").html('Save Changes');

        // Load fullname
        var temp = fullname.split(" ");
        if (temp.length > 1) {
            editStaffForm.find("input[name=firstname]").val(temp[0]);
            editStaffForm.find("input[name=lastname]").val(temp[1]);
        }

        //  load input fields
        var inputFields = ['email', 'phone', 'staff_id', 'id'];
        $(inputFields).each(function (i, v) {
            editStaffForm.find("input[name=" + v + "]").val(data[v]);
        });

       //  Load select fields
        var selectFields = ['state', 'role', 'status', 'branch_type'];
        $(selectFields).each(function (i, select) {
            editStaffForm.find("select[name=" + select + "] option").each(function (i, v) {
                if ($(v).val() == data[select]) {
                    $(v).attr("selected", true);
                }
            });

            // Exception for branch_type field
            if (select == 'branch_type') {
                editStaffForm.find("#branch").data("id", data.branch);
                editStaffForm.find("#branch_type").trigger('change');
            }
        });
    });

    $("#state").on('change', function(){
        $("#branch_type").trigger('change');
    })
});