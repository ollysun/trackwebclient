/**
 * Created by epapa on 22/10/2015.
 */
$(document).ready(function () {
    var editForm = $("#editModal");

    $(".editCompanyEc").unbind("click").click(function () {
        var fields = [
            {
                'field' : 'id',
                'type' : 'input'
            },
            {
                'field' : 'company_id',
                'type' : 'select'
            },
            {
                'field' : 'branch_id',
                'type' : 'select'
            }
        ];

        var data = this.dataset;
        $(fields).each(function (i, v) {
            editForm.find(v.type + "[name=" + v.field + "]").val(data[v.field]);
        });
    });
});