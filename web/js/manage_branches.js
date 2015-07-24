var Branch = {

    rep: function () {
        return {
            id: '',
            name: '',
            state: '',
            state_id: '',
            state_code: '',
            address: '',
            code: '',
            branch_type: '',
            status: '',
            hub_id:''
        }
    },

    Url: {
        'branch': '/site/branchdetails',
        'centres': '/site/getbranches'
    },

    getECs: function (hub_id, selectSelector) {
        $.get(Parcel.Url.branch, {id: branch_id}, function (response) {
            if (response.status === 'success') {
                var html = '';
                $.each(response.data, function (i, item) {
                    html += "<option value='" + item.id + "'>" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).attr('disabled', false);
                $(selectSelector).html(html);
            }
        });
    },

    getCentresList: function (hub_id, selectSelector) {
        $.get(Parcel.Url.branch, {id: branch_id}, function (response) {
            if (response.status === 'success') {
                var html = '';
                $.each(response.data, function (i, item) {
                    html += "<option value='" + item.id + "'>" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).attr('disabled', false);
                $(selectSelector).html(html);
            }
        });
    },

    getBranchDetails: function (branch_id) {
        var self = this;
        $.get(Branch.Url.branch, {id: branch_id}, function (response) {
            if (response.status === 'success') {

                var ex = self.rep();
                ex.id = response.data.id;
                ex.name = response.data.name;
                ex.state_code = response.data.state_code;
                ex.state_id = response.data.state_id;
                ex.branch_type = response.data.branch_type;
                ex.address = response.data.address;
                ex.status = response.data.status;
                if(response.data.parent !== null)
                    ex.hub_id = response.data.parent.id;
                self.setBranchDetails(ex);
            }
        });
    },

    setBranchDetails: function (bObj) {
        $("#editModal input[name='id']").val(bObj.id);
        $("#editModal input[name='name']").val(bObj.name);
        $("#editModal select[name='status']").val(bObj.status);
        $("#editModal input[name='address']").val(bObj.address);
        $("#editModal select[name='state_id'], #editModal input[name='state_id']").val(bObj.state_id);
        if(bObj.hub_id){
            $("#editModal select[name='hub_id']").val(bObj.hub_id);
        }
    },
};
$(document).ready(function () {

    $("button[data-target='#editModal']").on('click', function (event) {
        Branch.getBranchDetails($(this).attr('data-id'));
    });

    $("#myModal select#hub_id, #editModal select#hub_id").on('change', function (event) {
        state = $(this).find("option:selected").attr('data-state-id');
        $(this).next().val(state);
    });
    $("select#filter_hub_id").on('change', function (event) {
        $("form#filter").submit();
    });
    $("select#filter_state_id").on('change', function (event) {
        $("form#state_filter").submit();
    });
});