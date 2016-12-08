$(document).ready(function () {

    var select2Options = {
        width: 'element',
        placeholder: 'Not Applicable'
    };

    var deliveryBranchSelect = $('#delivery_branch_select');
    var currentBranchSelect = $('#current_branch_select');
    var createdBranchSelect = $('#created_branch_select');
    currentBranchSelect.removeClass('form-control').attr('style', 'width:300px').select2(select2Options);
    createdBranchSelect.removeClass('form-control').attr('style', 'width:300px').select2(select2Options);
    deliveryBranchSelect.removeClass('form-control').attr('style', 'width:300px').select2(select2Options);
    currentBranchSelect.on('change', function () {
        $('input[name="from_branch_id"]').val($(this).select2('val').join(','));
    });

    createdBranchSelect.on('change', function () {
        $('input[name="created_branch_id"]').val($(this).select2('val').join(','));
    });

    if (filters.created_branch_id != null) {
        createdBranchSelect.select2('val', filters.created_branch_id.split(',')).change();
    }

    if (filters.from_branch_id != null) {
        currentBranchSelect.select2('val', filters.from_branch_id.split(',')).change();
    }

   /* if (filters.delivery_branch_id != null) {
        deliveryBranchSelect.select2('val', filters.delivery_branch_id.split(',')).change();
    }*/

    $('#branch_type').on('change', function () {
        var branches = ($(this).val() == 'hub') ? hubs : ecs;
        currentBranchSelect.select2('val', '');
        TrackPlusUtil.fillSelect(currentBranchSelect, branches, 'id', function (data) {
            return data['name'].toUpperCase()
        }, 'Not Applicable');

        createdBranchSelect.select2('val', '');
        TrackPlusUtil.fillSelect(createdBranchSelect, branches, 'id', function (data) {
            return data['name'].toUpperCase()
        }, 'Not Applicable');

        deliveryBranchSelect.select2('val', '');
        TrackPlusUtil.fillSelect(deliveryBranchSelect, branches, 'id', function (data) {
            return data['name'].toUpperCase()
        }, 'Not Applicable');
    });
});
