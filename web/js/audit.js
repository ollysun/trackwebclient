/**
 * Created by ELACHI on 10/2/2016.
 */

$('.btnShowEditDetail').click(function(){
    $('#username').html($(this).data('username'));
    $('#date').html($(this).data('start-time'));
    $('#ipAddress').html($(this).data('ip-address'));
    $('#userAgent').html($(this).data('client'));
    $('#service').html($(this).data('service'));
    $('#actionName').html($(this).data('action-name'));
    var parameters = $(this).data('parameters');
    var sn = 0;
    for (var key in parameters){
        var row = "<tr><td>" + (++sn) + "</td><td>" + (key) + "</td><td>" + (parameters[key]) + "</td></tr>";
        $('#parameters').append(row);
    }
    console.log(parameters);
})