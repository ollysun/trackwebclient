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
        'centres': '/site/getbranches',
        'cities' : '/parcels/getcities'
    },

    getECs: function (hub_id, selectSelector) {
        $.get(Branch.Url.branch, {id: branch_id}, function (response) {
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
        $.get(Branch.Url.branch, {id: branch_id}, function (response) {
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

    getCities: function(state_id, citySelector, cityValue) {
        $(citySelector).html('').prop('disabled', true);
        $.get( Branch.Url.cities, { id: state_id }, function(response){
            if(response.status === 'success') {
                var html = '<option value="">Select City...</option>';
                var selected = '';
                $.each(response.data, function(i, item){
                    if (cityValue) {
                        selected = (cityValue == item.id) ? 'selected="selected"' : '';
                    }
                    html += "<option value='" + item.id + "' data-branch-id='" + item.branch_id + "' data-charges-id='" + item.onforwarding_charge_id + "' " + selected + ">" + item.name.toUpperCase() + "</option>";
                });
                $(citySelector).prop('disabled', false);
                $(citySelector).html(html);
            }
        });
    },
};
$(document).ready(function () {

    $("button[data-target='#editModal'], button[data-target='#status'], button[data-target='#relink']").on('click', function (event) {
        _parent = $(this).parent('td');
        _id = _parent.attr('data-id');
        target =  $(this).attr('data-target');
        $(target+" input[name='id']").val(_id);
        $(target+" input[name='name']").val($("td[class='n"+_id+"']").text());
        $(target+" select[name='status']").val(_parent.attr('data-status'));
        $(target+" textarea[name='address']").val($("td[class='a"+_id+"']").text());
        $(target+" select[name='state_id']").val(_parent.attr('data-state-id')).trigger('change');
        // remove trigger change (on previous line) and replace with next line once city id is integrated from api
        // note that extra debugging may be required on next line to make it work well.
        //Branch.getCities(_parent.attr('data-state-id'), target+" select[name='city_id']", _parent.attr('data-city-id'));
        $(target+" select[name='hub_id']").val(_parent.attr('data-parent-id'));
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
    $("select[name='state_id']").on('change', function(e){
        var state_id = $(this).val();
        var citySelector = $(this).closest('.modal-body').find('select[name="city_id"]');
        if (state_id) {
            Branch.getCities(state_id, citySelector);
        }
    });
});