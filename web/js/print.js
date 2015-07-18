$(document).ready(function(){
    $("#barcode").barcode(waybill,"code128");
    var waybillelement = document.querySelector(".waybill-wrap");
    setTimeout(function(){
        html2canvas(waybillelement, {
            onrendered: function(canvas) {
                $(waybillelement).addClass('hidden');
                document.body.appendChild(canvas);
                window.print();
                self.close();
            }
        });
    },100);

});
