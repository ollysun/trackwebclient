$("[data-return]").on('click', function (event) {
    var self = this;
    var reasons = $(self).data().reasons;

      bootbox.dialog({
        message: "<div class='form-group'>" +
        "<label for='comment_text'>Reason</label>" +
        "<select class='form-control' type='text' id='comment_text' ></select>" +

        "</div>" +
        "<div class='form-group'>" +
        "<label for='note'>Extra Note</label>" +
        "<textarea class='form-control' id='extra_note_text' ></textarea>" +

        "</div>",
        title: "Please state a the negative status for this parcel",
        buttons: {
            danger: {
                label: 'Add Status',
                className: 'btn btn-danger',
                callback: function () {
                    var comment = $("#comment_text").val();
                    var note = $("#extra_note_text").val();

                    if ($.trim(comment).length > 0) {
                        $("#request-returns [name=waybill_numbers]").val($(self).data('return'));
                        $("#request-returns [name=comment]").val(comment);
                        $("#request-returns [name=extra_note]").val(note);
                        $("#request-returns [name=attempted_delivery]").val($(self).data('attempted_delivery'));
                        $("#request-returns").trigger('submit');
                    } else {
                        alert('Please enter a negative');
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

        var select = document.getElementById("comment_text");
        var options = reasons;
        for (var i = 0; i < options.length; i++) {
            var opt = options[i].meaning_of_status;
            var el = document.createElement("option");
            el.textContent = opt;
            el.value = opt;
            select.appendChild(el);
        }


});


