$(document).ready(function () {
    $("button[data-target='#editModal']").on('click', function (event) {
        target = $(this).attr('data-target');
        _id = $(this).attr('data-id');
        _plan_id = $(this).attr('data-billing-plan-id');
        $(target + " input[name='onforward_name']").val($("td[class='n" + _id + "']").text());
        $(target + " input[name='onforward_code']").val($("td[class='c" + _id + "']").text());
        $(target + " textarea[name='onforward_desc']").val($("td[class='d" + _id + "']").text());
        $(target + " input[name='onforward_percentage']").val(parseFloat($("td[class='p" + _id + "']").text()));
        $(target + " input[name='onforward_amount']").val($("td[class='a" + _id + "']").text());
        $(target + " input[name='billing_plan_id']").val(_plan_id);
        $(target + " input[name='id']").val(_id);
        calculateAmount('#editModal', '[name="onforward_amount"]', '[name="onforward_percentage"]', '[readonly]');
    });

    calculateAmount('#myModal', '[name="onforward_amount"]', '[name="onforward_percentage"]', '[readonly]');

    function calculateAmount(formSelector, basePriceElementSelector, percentageElementSelector, amountElementSelector) {
        var form = $(formSelector);

        var bPEle = form.find(basePriceElementSelector),
            pEle = form.find(percentageElementSelector),
            aEle = form.find(amountElementSelector);

        setAmount();

        $(formSelector + ' ' + basePriceElementSelector + ', ' + formSelector + ' ' + percentageElementSelector).off('keyup change', setAmount).on('keyup change', setAmount);

        function setAmount() {
            var bP = bPEle.val(),
                p = pEle.val();

            if (bP === '')
                bP = '0';
            if (p === '')
                p = '0';

            var amount = bPEle.val() * (1 + (pEle.val() / 100));
            aEle.val(amount.toFixed(2));
        }
    }

    $("#reset_onforwarding_btn").unbind('click').click(function () {
        var billing_plan_id = $(this).data('billing_plan_id');
        bootbox.dialog({
            message: "Do you want to reset all charges to zero?",
            title: "Reset Onforwarding Charges",
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.post('resetonforwarding', {billing_plan_id: billing_plan_id});
                    }
                },
                info: {
                    label: "No",
                    className: "btn-danger",
                    callback: function () {
                    }
                }
            }
        });
    });
});