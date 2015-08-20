$(document).ready(function(){
    $(".barcode").barcode(waybill,"code128", {barWidth:2, showHRI: false, addQuietZone: false, bgColor: 'transparent', barHeight: 90});
    window.print();
    /* var waybillelement = document.querySelectorAll(".waybill-wrap");
    setTimeout(function(){
        var temp_el = null;
        for(var i =0; i < waybillelement.length; i++){
            temp_el = waybillelement[i];
            html2canvas(waybillelement[i], {
                onrendered: function(canvas) {
                    document.body.appendChild(canvas);
                    //window.print();
                    //self.close();
                }
            });
        }
        $("#main_holder").addClass('hidden');

    },100); */

});
