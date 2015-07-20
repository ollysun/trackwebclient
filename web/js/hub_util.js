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
    BASE_PATH:'http://local.courierplus.com/site/'
}

Hub.getBranches = function(state_id,branch_id,callback){
    Hub.sendToServer(Hub.Resources.BASE_PATH + Hub.Resources.getBranches,{id:state_id,branch_id:branch_id},function(response){
        log(response);
        if(typeof callback == "function"){
            callback(response);
        }
    });
}

$(document).ready(function(){
    $("#branch_type").unbind('change').on('change',function(){
        var id = $("#state").val();
        var branch_id = $(this).val();
        $("#branch").html('Loading...');
        if(id.length > 0 && branch_id.length > 0){
            Hub.getBranches(id,branch_id,function(data){
                if(data.status){
                    data.data.forEach(function(v,i){
                        $("#branch").append("<option>"+ (v.name + " ("+ v.code+")").toUpperCase()+"</option>");
                    });
                }
            });
        }else{
            al("Please select a state and branch type to see branches");
        }
    });
})