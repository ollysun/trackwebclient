var Hub = {};
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
Hub.Resources = {
    getBranches:'getbranches',
    getArrivedParcel:'getarrivedparcel',
    BASE_PATH:'/site/'
}
Hub.getBranches = function(state_id,branch_id,callback){
    Hub.sendToServer(Hub.Resources.BASE_PATH + Hub.Resources.getBranches,{id:state_id,branch_id:branch_id},function(response){

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
    $("#branch_type").unbind('change').on('change',function(){
        var id = $("#state").val();
        var branch_id = $(this).val();
        $("#branch").html('<option>Loading...</option>');
        if(id.length > 0 && branch_id.length > 0){
            Hub.getBranches(id,branch_id,function(data){
                $("#branch").html("");
                if(data.status){
                    data.data.forEach(function(v,i){
                        $("#branch").append("<option value='"+ v.id +"'>"+ (v.name + " ("+ v.code+")").toUpperCase()+"</option>");
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
        if(staff_no.length > 0){
            $("#loading_label").html("Loading...");
            Hub.getParcelsForArrival(staff_no,function(response){
                log(response);
                $("#parcel_arrival").html("");
                if(response.status && response.data.length > 0){
                    response.data.forEach(function(v,i){
                        $("#parcel_arrival").append("<tr id='"+ v.waybill_number+"' style='background-color: rgb(187, 255, 224);'><td>"+(i+1)+"</td><td>"+ v.waybill_number+"</td><td>"+ (v.status == 5?'IN TRANSIT':'<Not Intransit>')+"</td></tr>");
                    });
                }
                $("#loading_label").html("Loaded");
            });
        }else{
            alert("Invalid Staff ID");
        }
    });
})