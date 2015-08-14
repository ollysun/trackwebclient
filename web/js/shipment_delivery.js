/**
 * Created by RotelandO on 7/25/15.
 */

var parcels = {
    waybills: [],
    held_by_id: '',
    admin_id: ''
};

var Parcel_Delivery = {
    Url: {
        'staffdetails' : '/hubs/staffdetails',
        'generate_run': '/shipments/fordelivery',
    },

    getNewStaffInfo: function() {
        return {
            id: '',
            staff_name: '',
            staff_email: '',
            staff_phone: '',
            staff_role: ''
        }
    },

    fillSelectOption: function(url, param, selectSelector, selectedValue) {
        $.get( url, param, function(response){
            if(response.status === 'success') {
                var html = '<option value="">Select Name...</option>';
                $.each(response.data, function(i, item) {
                    selected = (selectedValue == item.id) ? 'selected="selected"' : '';
                    html += "<option value='" + item.id + "'" + selected + ">" + item.name.toUpperCase() + "</option>";
                });
                $(selectSelector).html(html);
            }
        });
    },

    searchStaffDetails: function(code) {
        var self = this;
        $.get( Parcel_Delivery.Url.staffdetails, { code: code }, function(response){
            if(response.status === 'success') {
                var staff = self.getNewStaffInfo();
                staff.id = response.data.id;
                staff.code = code;
                staff.staff_name = response.data.fullname;
                staff.staff_email = response.data.email;
                staff.staff_phone = response.data.phone;
                staff.staff_role = response.data.role.name;
                self.updateStaffDetails(staff);
                $('#staff_info').show();
                $('input#waybills').val(JSON.stringify(parcels));
                $('#generate').attr('disabled', false);
            } else {
                alert(response.message);
                $('#staff_info').hide();
                $('#generate').attr('disabled', true);
            }
        });
    },

    updateStaffDetails: function(staff) {

        parcels.held_by_id = staff.id;
        $('#staff_id').val(staff.id);
        $('#staff_name').html(staff.staff_name);
        $('#staff_phone').html(staff.staff_phone);
        $('#staff_role').html(staff.staff_role);
    },

    moveToInTransit: function(parcels) {
        $.ajax({
            url: Parcel_Delivery.Url.generatemanifest,
            type: 'POST',
            dataType: 'JSON',
            data: JSON.stringify(parcels),
            success: function(response) {
                if(response.status == 'success') {
                    alert('Manifest Generated Successfully!');
                } else {
                    alert('An error occurred when generating manifest. Please try again later');
                }
            },
            error: function(err) {
                alert('An error occurred when generating manifest. Please try again later');
            },
            complete: function(jqXHR) {

            }
        })
    }
};

$(document).ready(function(){
    $('[data-target=#runModal]').on('click', function(event) {
        var chkboxes = $('.checkable:checked');
        var selected = false;

        if(!chkboxes.length) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return false;
        }
        $.each(chkboxes, function(i, chk){
            parcels.waybills.push($(this).attr('data-waybill'));
            selected = true;
        });
        var html = '';
        $.each(parcels.waybills, function(i, waybill){
            html += "<tr>";
            html += "<td>" + (i+1) + "</td>";
            html += "<td>" + waybill + "</td>";
            html += "</tr>";
        });
        $('#delivery_run>tbody').html(html);
        $('input#waybills').val(JSON.stringify(parcels));
    });

    $('#get_details').on('click', function (event) {
        var staff_code = $('input#disp_id').val();
        if(staff_code == '') {
            return;
        }
        Parcel_Delivery.searchStaffDetails(staff_code);
    });
    $('input#disp_id').on('keypress', function (event) {
        if (event.which == 13) {
            event.preventDefault();
            var staff_code = $(this).val();
            if(staff_code == '') {
                return;
            }
            Parcel_Delivery.searchStaffDetails(staff_code);
        }
    });
});
