var Region = {
    rep: function () {
        return {
            region_id: '',
            name: '',
            state_id: '',
            state_code: '',
            address: '',
            code: '',
            branch_type: '',
            status: '',
            hub_id:'',
        }
    },

    Url: {
        'region': '/billing/getregion',
        'states': '/billing/getstates',
    },

    getRegionDetails: function (branch_id, target) {
        var self = this;
        $.get(Region.Url.region, {region_id: region_id}, function (response) {
            if (response.status === 'success') {
                var ex = self.rep();
                ex.region_id = response.data.region_id;
                ex.name = response.data.name;
                ex.description = response.data.description;
                self.setBranchDetails(ex, target);
            }
        });
    },

    setRegionDetails: function (bObj, target) {
        $(target+" input[name='id']").val(bObj.region_id);
        $(target+" input[name='name']").val(bObj.name);
        $(target+" select[name='status']").val(bObj.status);
        $(target+" textarea[name='description']").val(bObj.description);
    },
};
$(document).ready(function () {

    $("button[data-target='#region_editModal'], button[data-target='#region_status']").on('click', function (event) {
        target = $(this).attr('data-target');
        _id = $(this).attr('data-id');
        $(target+" input[name='name']").val($("td[class='n"+_id+"']").text());
        $(target+" textarea[name='description']").val($("td[class='d"+_id+"']").text());
        $(target+" select[name='status']").val($(this).attr('data-status'));
        $(target+" input[name='id']").val(_id);
    });

    $("select#filter_hub_id").on('change', function (event) {
        $("form#filter").submit();
    });
    $("select#filter_state_id").on('change', function (event) {
        $("form#state_filter").submit();
    });
});