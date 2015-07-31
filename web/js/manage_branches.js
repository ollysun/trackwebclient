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
            hub_id:'',
            target:''
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
            }else{
                alert(response.message);
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
            }else{
                alert(response.message);
            }
        });
    },

    getBranchDetails: function (branch_id, target) {
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
                self.setBranchDetails(ex, target);
            }else{
                alert(response.message);
            }
        });
    },

    setBranchDetails: function (bObj, target) {
        $(target+" input[name='id']").val(bObj.id);
        $(target+" input[name='name']").val(bObj.name);
        $(target+" select[name='status']").val(bObj.status);
        $(target+" textarea[name='address']").val(bObj.address);
        $(target+" select[name='state_id'], #editModal input[name='state_id']").val(bObj.state_id);
        if(bObj.hub_id){
            $(target+" select[name='hub_id']").val(bObj.hub_id);
        }
    },
};
$(document).ready(function () {

    $("button[data-target='#editModal'], button[data-target='#status'], button[data-target='#relink']").on('click', function (event) {

        $($(this).attr('data-target')+" input[name='id']").val($(this).attr('data-id'));
        Branch.getBranchDetails($(this).attr('data-id'), $(this).attr('data-target'));
    });

/*    $("#myModal select#hub_id, #editModal select#hub_id").on('change', function (event) {
        state = $(this).find("option:selected").attr('data-state-id');
        $(this).next().val(state);
    });*/
    $("select#filter_hub_id").on('change', function (event) {
        $("form#filter").submit();
    });
    $("select#filter_state_id").on('change', function (event) {
        $("form#state_filter").submit();
    });
});