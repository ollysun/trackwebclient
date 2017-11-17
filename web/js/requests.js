/**
 * Created by Lawale on 22/10/15.
 */
var Requests = {
    URL : {
        getHeldParcels:'/shipments/getparcels',
        validateStaff:'/site/validatestaff',
        receiveFromDispatcher : '/shipments/receivefromdispatcher'
    }
};

Requests.sendToServer = function(url,data,callback){
    $.getJSON(url,data,function(response){ // Could have done it directly .... but you can do more before calling the callback :-)
        if(typeof callback == "function"){
            callback(response);
        }
    });
};

Requests.postToServer = function(url,data,callback){
    $.post(url,data,function(response){ // Could have done it directly .... but you can do more before calling the callback :-)
        if(typeof callback == "function"){
            callback((response));
        }
    });
};

Requests.validateSweeper = function(staff_id,callback){
    Requests.sendToServer(Requests.URL.validateStaff,{staff_id:staff_id},function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
};

Requests.receiveFromDispatcher = function(data,callback){
    Requests.postToServer(Requests.URL.receiveFromDispatcher,data,function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
};

Requests.getParcels = function(staff_id, parcel_status,callback){
    Requests.sendToServer(Requests.URL.getHeldParcels,{staff_no:staff_id, status:parcel_status},function(response){
        if(typeof callback == "function"){
            callback(response);
        }
    });
};