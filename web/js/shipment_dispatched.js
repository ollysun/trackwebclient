/**
 * Created by Lawale on 21/10/15.
 */
$(document).ready(function () {


    $("#chbx_w_all").change(function () {
        $("input:checkbox.checkable").prop("checked", $(this).prop("checked"));
    });


    $("[data-target=#passwordModal]").on("click", function (event) {
        var chkboxes = $(".checkable:checked");

        if (!chkboxes.length) {
            alert("You must select at least one parcel!");
            event.preventDefault();
            return false;
        }

        if ($(this).data("action") == 'return') {
            if (!$(".checkable:checked[data-is-return]").length) {
                alert("You have selected a parcel that is not set for return!");
                event.preventDefault();
                return false;
            }
        }

        waybill_numbers = [];

        $.each(chkboxes, function (i, chk) {
            $(this).data('held_by_id');
            waybill_numbers.push($(this).attr("data-waybill"));
        });

        $("input#task").val($(this).attr("data-action"));
        $("input#waybills").val(JSON.stringify(waybill_numbers));
    });

    $('#staff_no').on('keypress', function (event) {
        if (event.which == 13) {
            event.preventDefault();
            var staff_code = $(this).val();
            if (staff_code == '') {
                return;
            }
            Requests.searchStaffDetails(staff_code);
        }
    });

    $('#btn_staff').on('click', function (event) {
        var staff_code = $('#staff_no').val();
        if (staff_code == '') {
            return;
        }
        Requests.searchStaffDetails(staff_code);
        event.preventDefault();
    });

    $("#get_arrival").unbind('click').on('click', function () {
        var staff_no = $("#staff_no").val();
        var branch_type = $(this).data("branch_type");
        // alert(branch_type);
        if (staff_no.length > 0) {
            $("#loading_label").removeClass('text-success text-danger').addClass("text-info").html("Validating Staff ID...");

            Requests.validateSweeper(staff_no, function (response) {
                if (response.status == 'success') {
                    data = response.data;
                    $("#parcel_arrival").html("");
                    $("#sweeper_name").html(data.fullname.toUpperCase());
                    $("#role").html(data.role.name.toUpperCase());
                    $("#branch").html(data.branch.name.toUpperCase() + '(' + data.branch.code.toUpperCase() + ')');
                    $("#staff_user_id").val(data.id);
                    $("#loading_label").removeClass('text-success text-danger').addClass("text-info").html("Staff Validation Successful<br/>Loading parcels... Please wait");

                    Requests.getParcels(staff_no, beingdelivered, function (response) {
                        data = response.data;
                        if (response.status && data.length > 0) {
                            rows = '';
                            data.forEach(function (v, i) {
                                rows += "<tr id='" + v.waybill_number + "'><td>" +
                                    (i + 1) + " <input name='" +
                                    v.waybill_number + "' type='checkbox'></td><td>" +
                                    v.waybill_number + "</td><td id='L" +
                                    v.waybill_number + "'>" +
                                    (v.status == beingdelivered ? 'In transit to Customer' : 'Not In transit') +
                                    "</td></tr>";
                            });
                            $("#parcel_arrival").append(rows);
                            $("#receive_parcels_btn").removeClass('disabled');
                        }

                        $("#loading_label").removeClass('text-info text-danger').addClass("text-success").html("Loaded");
                        var payload = function () {
                            this.waybill_numbers = [];
                            this.held_by_id = 0;
                        };

                        $("#receive_parcels_btn").unbind('click').on('click', function (d) {
                            var me = $(this);
                            me.html("Receiving...").addClass("disabled");
                            var form = $("#held_parcels").serializeArray();
                            var payloadObj = new payload();
                            for (var k in form) {
                                if (form[k].name == 'staff_user_id') {
                                    payloadObj.held_by_id = form[k].value;
                                } else {
                                    payloadObj.waybill_numbers.push(form[k].name);
                                }
                            }

                            if (payloadObj.waybill_numbers.length > 0) {
                                Requests.receiveFromDispatcher({
                                    held_by_id: payloadObj.held_by_id,
                                    waybill_numbers: payloadObj.waybill_numbers.join(',')
                                }, function (resp) {

                                    var response = JSON.parse(JSON.stringify(resp));
                                    if (response.status == 'success') {
                                        if (typeof response.data.bad_parcels != "undefined") {
                                            for (var waybill_number in payloadObj.waybill_numbers) {
                                                choice = payloadObj.waybill_numbers[waybill_number];
                                                if (choice in response.data.bad_parcels) {
                                                    $("#L" + choice).html(response.data.bad_parcels[choice]);
                                                    $("#L" + choice).attr("style", "background-color:red");
                                                } else {
                                                    $("#L" + choice).html("Parcel received").parent().attr("style", "background-color:green");
                                                }
                                            }
                                        } else {
                                            window.location.reload();
                                        }
                                    } else {
                                        alert("Error. Reason:" + response.message);
                                    }
                                    me.html("Accept").removeClass("disabled");
                                });

                            } else {
                                alert("No parcels received in");
                            }
                        });
                    });
                } else {
                    $("#loading_label").removeClass('text-info').removeClass('text-success').addClass("text-danger").html("Staff Validation Failed");
                }
            });


        } else {
            alert("Invalid Staff ID");
        }
    });
});
