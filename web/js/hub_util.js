var Hub = {};
Hub.states = {};
Hub.stateHubs = {};
function log(msg){
    console.log(msg);
}
function al(msg){
    alert(msg);
}
Hub.sendToServer = function(url,data,callback){
    $.getJSON(url,data,function(response){ // Could have done it directly .... but you can do more before calling the callback :-)
        if(typeof callback == "function"){
            callback(response);
        }
    });
}
Hub.postToServer = function(url,data,callback){
    $.post(url,data,function(response){ // Could have done it directly .... but you can do more before calling the callback :-)

        if(typeof callback == "function"){
            callback((response));
        }
    });
}
Hub.Resources = {
    getBranches:'getbranches',
    getArrivedParcel:'getarrivedparcel',
    validateStaff:'validatestaff',
    checkInParcel:'checkinparcel',
    moveToForDelivery:'movetofordelivery',
    BASE_PATH:'/site/'
}
Hub.getBranches = function(state_id,branch_id,callback){
    Hub.sendToServer(Hub.Resources.BASE_PATH + Hub.Resources.getBranches,{id:state_id,branch_id:branch_id},function(response){

        if(typeof callback == "function"){
            callback(response);
        }
    });
}
Hub.validateSweeper = function(staff_id,callback){
    Hub.sendToServer(Hub.Resources.BASE_PATH + Hub.Resources.validateStaff,{staff_id:staff_id},function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
}
Hub.sendParcelToArrival = function(data,callback){
    Hub.postToServer(Hub.Resources.BASE_PATH + Hub.Resources.checkInParcel,data,function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
}
Hub.sendParcelToForDelivery = function(data,callback){
    Hub.postToServer(Hub.Resources.BASE_PATH + Hub.Resources.moveToForDelivery,data,function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
}
Hub.getParcelsForArrival = function(staff_id,callback){
    Hub.sendToServer(Hub.Resources.BASE_PATH + Hub.Resources.getArrivedParcel,{staff_no:staff_id},function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
}

$(document).ready(function(){
    $("#branch_type,#state").unbind('change').on('change',function(){
        var id = $("#state").val();
        var branch_id = $(this).val();
        var branchElement = $("#branch");
        branchElement.html('<option>Loading...</option>');
        if(id.length > 0 && branch_id.length > 0){
            Hub.getBranches(id,branch_id,function(data){
                branchElement.html("");
                if(data.status){
                    data.data.forEach(function(v,i){
                        console.log(branchElement.data('id'));
                        if(branchElement.data('id') == v.id){
                            branchElement.append("<option selected value='"+ v.id +"'>"+ (v.name + " ("+ v.code+")").toUpperCase()+"</option>");
                        } else {

                            branchElement.append("<option value='"+ v.id +"'>"+ (v.name + " ("+ v.code+")").toUpperCase()+"</option>");
                        }
                    });
                }
            });
        }else{
            $("#branch").html("");
            al("Please select a state and branch type to see branches");
        }
    });

    $("#get_arrival").unbind('click').on('click',function(){
        var staff_no = $("#staff_no").val();
        var branch_type= $(this).data("branch_type");
        // alert(branch_type);
        if(staff_no.length > 0){
            $("#loading_label").removeClass('text-success').removeClass('text-danger').addClass("text-info").html("Validating Staff ID...");
            Hub.validateSweeper(staff_no,function(response){
                if(response.status == 'success'){
                    $("#sweeper_name").html(response.data.fullname.toUpperCase());
                    $("#role").html(response.data.role.name.toUpperCase());
                    $("#branch").html(response.data.branch.name.toUpperCase()+'('+response.data.branch.code.toUpperCase()+')');
                    $("#staff_user_id").val(response.data.id);
                    $("#loading_label").removeClass('text-success').removeClass('text-danger').addClass("text-info").html("Staff Validation Successful<br/>Loading parcels... Please wait");
                    Hub.getParcelsForArrival(staff_no,function(response){
                        $("#parcel_arrival").html("");
                        if(response.status && response.data.length > 0){
                            response.data.forEach(function(v,i){
                                $("#parcel_arrival").append("<tr id='"+ v.waybill_number+"' style='background-color: rgb(187, 255, 224);'><td>"+(i+1)+" <input name='"+ v.waybill_number+"' type='checkbox'></td><td>"+ v.waybill_number+"</td><td id='L"+v.waybill_number+"'>"+ (v.status == 5?'IN TRANSIT':'Not Intransit')+"</td></tr>");
                            });
                        }
                        $("#loading_label").removeClass('text-info').removeClass('text-danger').addClass("text-success").html("Loaded");
                        var payload = function(){
                            this.waybill_numbers = [];
                            this.held_by_id = 0;
                        }
                        $("#arrived_parcels_btn").unbind('click').on('click',function(d){
                            var me = $(this);
                            me.html("Accepting...").addClass("disabled");
                            var form = $("#arrived_parcels").serializeArray();
                            var payloadObj = new payload();
                            for(var k in form){
                                if(form[k].name == 'staff_user_id'){
                                    payloadObj.held_by_id = form[k].value;
                                }else{
                                    payloadObj.waybill_numbers.push(form[k].name);
                                }
                            }
                            if(payloadObj.waybill_numbers.length > 0){
                                switch(branch_type){
                                    case 'ec':
                                        Hub.sendParcelToForDelivery({held_by_id: payloadObj.held_by_id, waybill_numbers: payloadObj.waybill_numbers.join(',') },function(resp){
                                            log(resp);
                                            var response = JSON.parse(JSON.stringify (resp));
                                            if(response.status=='success'){
                                                if(typeof response.data.bad_parcels != "undefined"){
                                                    for(var waybill_number in payloadObj.waybill_numbers){
                                                        if(payloadObj.waybill_numbers[waybill_number] in response.data.bad_parcels ){
                                                            $("#L"+payloadObj.waybill_numbers[waybill_number]).html(response.data.bad_parcels[payloadObj.waybill_numbers[waybill_number]]);
                                                            $("#L"+payloadObj.waybill_numbers[waybill_number]).attr("style","background-color:red");
                                                        }else{
                                                            $("#L"+payloadObj.waybill_numbers[waybill_number]).html("Parcel received").parent().attr("style","background-color:green");
                                                        }
                                                    }

                                                }else{
                                                    window.location.reload();
                                                }
                                            }else{
                                                alert("Error.#157-68. Reason:"+response.message);
                                            }
                                            me.html("Accept").removeClass("disabled");
                                        });
                                        break;
                                    case 'hub':
                                        Hub.sendParcelToArrival({held_by_id: payloadObj.held_by_id,waybill_numbers: payloadObj.waybill_numbers.join(',') },function(resp){

                                            var response = JSON.parse(JSON.stringify (resp));
                                            if(response.status=='success'){
                                                if(typeof response.data.bad_parcels != "undefined"){
                                                    for(var waybill_number in payloadObj.waybill_numbers){
                                                        if(payloadObj.waybill_numbers[waybill_number] in response.data.bad_parcels ){
                                                            $("#L"+payloadObj.waybill_numbers[waybill_number]).html(response.data.bad_parcels[payloadObj.waybill_numbers[waybill_number]]);
                                                            $("#L"+payloadObj.waybill_numbers[waybill_number]).attr("style","background-color:red");
                                                        }else{
                                                            $("#L"+payloadObj.waybill_numbers[waybill_number]).html("Parcel received").parent().attr("style","background-color:green");
                                                        }
                                                    }

                                                }else{
                                                    window.location.reload();
                                                }
                                            }else{
                                                alert("Error.#157-68. Reason:"+response.message);
                                            }
                                            me.html("Accept").removeClass("disabled");
                                        });
                                        break;
                                }

                            }else{
                                alert("No item scanned in");
                            }

                        });
                    });
                }else{
                    $("#loading_label").removeClass('text-info').removeClass('text-success').addClass("text-danger").html("Staff Validation Failed");
                }
            });


        }else{
            alert("Invalid Staff ID");
        }
    });
    $("#role_filter").unbind('change').on('change',function(){
        var role = $(this).val();
        if(role.trim().length > 0){
            window.location.href = "/admin/managestaff?role="+role;
        }
    });
    $("#page_width").unbind('change').on('change',function(){
        document.cookie = "page_width="+$(this).val();
    });
    $('.modal').on('hide.bs.modal', function (e) {
        window.location.reload();
    });

    if(typeof hubs != 'undefined' && typeof states != 'undefined'){

        hubs.forEach(function(v,i){
            if(typeof Hub.stateHubs[v.state_id] == 'undefined'){
                Hub.stateHubs[v.state_id] = [];
            }
            Hub.stateHubs[v.state_id].push(v);
        });
        $("#state_hub_selector").unbind("change").on("change",function(){
            $("#hub_id").html("<option>No Hub</option>");
            var state_hubs =  Hub.stateHubs[$(this).val()];
            if(state_hubs){
                $("#hub_id").html("<option>Select One</option>");
                state_hubs.forEach(function(v,i){
                    $("#hub_id").append("<option value='"+ v.id+"'>"+ v.name.toUpperCase() + " ("+ v.code.toUpperCase()+")"+"</option>");
                });
            }
        });
    }
})