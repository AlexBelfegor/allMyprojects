f3000_price_locked_tm = false;
f3000_ajax_locked = false;
function f3000_flush_price()
{
    $('.f3000_price_flush').val('');
    submit_filters3000();
}
function f3000_unlock_save()
{
    $('.f3000_something_changed').show();
}
function f3000_flush_value(filterId, valueId)
{
    if ('undefined' == typeof valueId)
    {
        $('.f3000_' + filterId).attr('checked', false);
    }
    else
    {
        $('.f3000_' + filterId + '_' + valueId).attr('checked', false);
    }
    submit_filters3000();
}

$(document).ready(function() {
    stop_submit = function(e)
    {
        e.stopPropagation();
        return false;
    }
    $(document).on('submit', '#filters3000', {}, function(e) {
        f3000_apply_currency_hack();
        return true;
    });
    $(document).on('change', '#filters3000 .f3000_value', {}, function() {

        submit_filters3000()

    });
    $(document).on('keydown', '.f3000_price', {}, function() {
        var selector = 'input[name=' + $(this).attr('name') + ']';
        if (f3000_price_locked_tm)
        {
            clearTimeout(f3000_price_locked_tm);
        }

        f3000_price_locked_tm = setTimeout(function() {
            submit_filters3000(function() {

                $(selector).focus();

            });
        }, 2000);
    });
    $(document).on('click', '.price_submit', {}, function() {
        submit_filters3000();
    });

});

function f3000_convert_currency(num, rate)
{
    if ('undefined' != typeof(num) && num > 0)
    {
        num = num * rate;
        return num;
    }
    return 0;
}

function f3000_apply_currency_hack()
{
    var curValMin = $('input[name=price_min]').val();
    var curValMax = $('input[name=price_max]').val();
    if (curValMin) {
        $('input[name=price_min]').val(f3000_convert_currency(curValMin, Filters3000Core.currency_rate));
    }
    if (curValMax) {
        $('input[name=price_max]').val(f3000_convert_currency(curValMax, Filters3000Core.currency_rate));
    }
}

function submit_filters3000(callback)
{
    if (f3000_price_locked_tm)
    {
        clearTimeout(f3000_price_locked_tm);
    }

    if (!f3000_ajax_locked)
    {
        f3000_ajax_locked = true;
        f3000_apply_currency_hack();
        $.ajax({
            type: "POST",
            url: Filters3000Core.getUrl(),
            data: $('#filters3000').serializeArray(),

            success: function(data) {
                $(Filters3000Core.getSelector()).html(data);
                f3000_unlock_save();
                if ('function' == typeof(callback))
                {
                    callback(this);
                }
            },
            dataType: 'html'
        }).always(function() {
                f3000_ajax_locked = false;
            });
    }
}

