function validate($parent)
{
    $($parent+' .has-error .help-block').remove();
    $($parent+' .has-error').removeClass('has-error');
    $($parent+' .has-success').removeClass('has-success');
    var hasError = false;

    $($parent+' .validate').each(function()
    {
        var msg = '';
        var val = jQuery.trim($(this).val());

        if($(this).hasClass('required') && val == '')
        {
            msg = 'Required field';
            hasError = true;
        }
        else if($(this).hasClass('email'))
        {
            var em = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
            if(!em.test(val))
            {
                msg = 'Invalid entry';
                hasError = true;
            }
        }
        else if($(this).hasClass('integer'))
        {
            var test = /^[-+]?\d+$/;
            if(!test.test(val))
            {
                msg = 'Invalid entry';
                hasError = true;
            }
        }
        else if($(this).hasClass('number'))
        {
            var ph = /^[0-9]+(\.[0-9][0-9]?)?$/;
            if(!ph.test(val))
            {
                msg = 'Invalid entry';
                hasError = true;
            }
        }
        else if($(this).hasClass('phone'))
        {
            var ph = /^(234|0)[0-9]{10}$/;
            if(!ph.test(val))
            {
                msg = 'Invalid entry';
                hasError = true;
            }
        }
        else if($(this).hasClass('match'))
        {
            var $match = ($parent+' '+$(this).attr('match'));
            if($($match).val()!=val)
            {
                msg = 'Entries mismatch';
                hasError = true;
            }
        }
        else if($(this).find("input[type=radio]").length>0 && $(this).find("input[type=radio]:checked").length==0)
        {
            msg = 'Required field';
            hasError = true;
        }
        if(msg != ''){
            if($(this).parent().hasClass('input-group')){
                $(this).parent().parent().append('<div class="help-block no-margin clearfix">'+msg+'</div>');
                $(this).parent().parent().addClass('has-error');
            }
            else{
                $(this).parent().append('<div class="help-block no-margin clearfix">'+msg+'</div>');
                $(this).parent().addClass('has-error');
            }
        }
        else{
            $(this).parent().removeClass('has-error').addClass('has-success');
        }
    });
    if(!hasError)
    {
        return true;
    }
    return false;
}
