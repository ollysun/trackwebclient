$(document).ready(function () {
    var fields = [
        {
            'field' : 'firstname',
            'type' : 'input'
        },
        {
            'field' : 'lastname',
            'type' : 'input'
        },
        {
            'field' : 'id',
            'type' : 'input'
        },
        {
            'field' : 'user_auth_id',
            'type' : 'input'
        },
        {
            'field' : 'email',
            'type' : 'input'
        },
        {
            'field' : 'phone_number',
            'type' : 'input'
        },
        {
            'field' : 'status',
            'type' : 'select'
        },
        {
            'field' : 'role_id',
            'type' : 'select'
        }
    ];

    var editForm = $("#editForm");
    $("[data-edit-user]").click(function () {
        var data = this.dataset;
        $(fields).each(function (i, v) {
            editForm.find(v.type + "[name=" + v.field + "]").val(data[v.field]);
        });
    });
});