/**
 * Created by RotelandO on 7/25/15.
 */


/**
 * Created by RotelandO on 7/20/15.
 */
var parcels = {
    waybills: [],
    to_branch_id: '',
    to_branch_name: '',
    staff_id: '',
    staff_code: ''
};

var Parcel_Destination = {

    Url: {
        'allhubs' : '/hubs/allhubs',
        'allecforhubs' : '/hubs/allecforhubs',
        'staffdetails' : '/hubs/staffdetails',
        'generatemanifest': '/hubs/generatemanifest',
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
        $.get( Parcel_Destination.Url.staffdetails, { code: code }, function(response){
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
                $('#btnGenerate').attr('disabled', false);
            } else {
                alert(response.message);
                $('#staff_info').hide();
                $('#btnGenerate').attr('disabled', true);
            }
        });
    },

    updateStaffDetails: function(staff) {

        parcels.staff_id = staff.id;
        parcels.staff_code = staff.code;
        $('#staff_name').html(staff.staff_name);
        $('#staff_email').html(staff.staff_email);
        $('#staff_phone').html(staff.staff_phone);
        $('#staff_role').html(staff.staff_role);
    },

    moveToInTransit: function(parcels) {
        $.ajax({
            url: Parcel_Destination.Url.generatemanifest,
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

var Parcel_Delivery = {

    Url: {
        '' : 'staffcheck',
        'dispatch' : '/parcel/set',
        'staffdetails' : '/hubs/staffdetails',
        'generatemanifest': '/hubs/generatemanifest',
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

    searchStaffDetails: function(code) {
        var self = this;
        $.get( Parcel_Destination.Url.staffdetails, { code: code }, function(response){
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
                $('#btnGenerate').attr('disabled', false);
            } else {
                alert(response.message);
                $('#staff_info').hide();
                $('#btnGenerate').attr('disabled', true);
            }
        });
    },

    checkStaff: function(code, password){
        var self = this;
        $.get( Parcel_Delivery.Url.login, {staff_id:code, password:password}, function(response){
            if(response.status === 'success') {
                var staff = self.getNewStaffInfo();
                staff.id = response.data.id;
                //self.updateStaffDetails(staff);
                $('div#delivery_run').show();
            } else {
                alert(response.message);
                $('div#delivery_run').hide();
            }
        });
    },
};

$(document).ready(function(){
    $('[data-target=#runModal]').on('click', function(event) {
        var chkboxes = $('.checkable:checked');
        var selected = false;

        if(!chkboxes.length) {
            alert('You must select at least one parcel!');
            event.preventDefault();
            return;
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
    });

    $('#staff_id, #password').on('keypress', function (event) {
        if (event.which == 13) {
            event.preventDefault();
            var staff_code = $('#staff_id').val();
            var pword = $('#password').val();
            if(staff_code == '' || pword == '') {
                return;
            }
            Parcel_Delivery.checkStaff(staff_code,pword);
        }
    });

    $('#staff').on('keypress', function (event) {
        if (event.which == 13) {
            event.preventDefault();
            var staff_code = $(this).val();
            if(staff_code == '') {
                return;
            }
            Parcel_Destination.searchStaffDetails(staff_code);
        }
    });

    $('#btnGenerate').on('click', function(event){
        $('#payload').val(JSON.stringify(parcels));
    });
});
