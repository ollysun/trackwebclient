$(document).ready(function () {
    $("[data-view-parcels]").click(function () {
        var parcels = JSON.parse(this.dataset.parcels);

        var parcelsContainer = $("#parcels").html(parcels.length == 0 ? 'No Parcels' : '');
        parcels.forEach(function (value) {
            parcelsContainer.append('<li><a target="_blank" href="/shipments/view?waybill_number=' + value.waybill_number + ' ">' + value.waybill_number + '</a></li>')
        });
    });
});