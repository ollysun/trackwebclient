function validate(parentSelector) {
    var isValid = true;
    var inputs = $(parentSelector).find('.validate');
    $(inputs).each(function() {
        validity = validateFxn.apply(this);
        if(!validity) {
            isValid = false;
        }
    });
    return isValid;
}
function validateFxn() {
    var input = $(this),
        isValid = true;
    var val = jQuery.trim(input.val()),
        formgroup = input.closest('.form-group');
    var msg = '';

    formgroup.removeClass('has-error has-success').find('.help-block.help-validation-error').remove();
    if(input.hasClass('required') && val === '')
    {
        msg = 'Required field';
        isValid = false;
    }
    else if(input.hasClass('email'))
    {
        var em = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,12}(?:\.[a-z]{2})?)$/i;
        if(!em.test(val))
        {
            msg = 'Invalid entry';
            isValid = false;
        }
    }
    else if(input.hasClass('integer'))
    {
        var test = /^[-+]?\d+$/;
        if(!test.test(val))
        {
            msg = 'Invalid entry';
            isValid = false;
        }
    }
    else if(input.hasClass('number'))
    {
        var ph = /^[0-9]+(\.[0-9][0-9]?)?$/;
        if(!ph.test(val))
        {
            msg = 'Invalid entry';
            isValid = false;
        }
    }
    else if(input.hasClass('phone'))
    {
        var ph = /^(234|0)[0-9]{7,10}$/;
        if(!ph.test(val))
        {
            msg = 'Invalid entry';
            isValid = false;
        }
    }
    else if (input.hasClass('name'))
    {
        var filter = /^([a-zA-Z]+)([a-zA-Z\.\-\' ]*)([a-zA-Z]+)$/;
        if(!filter.test(val))
        {
            msg = 'Invalid entry';
            isValid = false;
        }

    }
    else if(input.hasClass('match'))
    {
        var match = (input.attr('match'));
        if($(match).val() !== val)
        {
            msg = 'Entries mismatch';
            isValid = false;
        }
    }
    else if(input.find("input[type=radio]").length>0 && input.find("input[type=radio]:checked").length==0)
    {
        msg = 'Required field';
        isValid = false;
    }
    if(msg !== ''){
        var errorMsg = '<div class="help-block help-validation-error" style="margin:0">'+msg+'</div>';
        formgroup.addClass('has-error');
        if(formgroup.find('.help-block').length > 0) {
            $(errorMsg).insertBefore(formgroup.find('.help-block')[0]);
        }
        else {
            formgroup.append(errorMsg);
        }
        if(!input.hasClass('active-validate')) {
            input.one('blur.CP.form.validate change.CP.form.validate',validateFxn);
        }
    }
    return isValid;
}
(function($){
    $('.validate-form .active-validate')
        .off('blur.CP.form.validate change.CP.form.validate').on('blur.CP.form.validate change.CP.form.validate', validateFxn);
    $('.validate-form').on('submit.CP.form.validate',function(event){
        var valid = validate(this);
        if (!valid) {
            $(this).trigger('enable.CP.form.submitButton');
        }
        return valid;
    });
})(jQuery);