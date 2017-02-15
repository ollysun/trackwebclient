var ViewModel = function() {
    var self = this;


    self.countries = ko.observableArray();

    self.viewcountries = function (zone_id) {
        self.countries([]);
        $.ajax({
            url: '/intlbilling/countriesbyzone?zone_id=' + zone_id,
            type: 'GET',
            success: function (result) {
                if (result.status == 'success') {
                    self.countries(result.data);
                    console.log(result.data);
                } else {
                    alert('Unable to fetch countries for this zone');
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    };
}

ko.applyBindings(new ViewModel());