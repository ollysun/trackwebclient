$("[data-return]").on('click', function (event) {
    var self = this;
    console.log($(self).data());
    bootbox.dialog({
        message: "<div class='form-group'><label for='comment_text'>Reason</label><input class='form-control' type='text' id='comment_text' /></div>",
        title: "Please state a reason for returning this parcel",
        buttons: {
            danger: {
                label: 'Return',
                className: 'btn btn-danger',
                callback: function () {
                    var comment = $("#comment_text").val();
                    if ($.trim(comment).length > 0) {
                        $("#request-returns [name=waybill_numbers]").val($(self).data('return'));
                        $("#request-returns [name=comment]").val(comment);
                        $("#request-returns").trigger('submit');
                    } else {
                        alert('Please enter a reason for returning parcel');
                        return false;
                    }
                }
            },

            main: {
                label: 'Cancel',
                className: 'btn btn-primary',
                callback: function(){

                }
            }
        }
    });
});


