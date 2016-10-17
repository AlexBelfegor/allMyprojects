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
    $('#filters3000').submit();
}

function f3000_flush_price()
{
    $('.f3000_price_flush').val('');
    $('#filters3000').submit();
}

function f3000_convert_currency(num, rate)
{
    if ('undefined' != typeof(num) && num > 0)
    {
        num = num * rate;
        return num;
    }
    return 0;
}

function f3000_unlock_save()
{
    $('.f3000_save').show();
}

function f3000_spinner(onOff)
{
    if (onOff)
    {
        $('.f3000_spinner').show();
    }
    else
    {
        $('.f3000_spinner').hide();
    }


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

//функция для обновления фильтра
function update_filter3000(callback)
{
    //конвертация валюты перед обновлением фильтра
    f3000_spinner(true);

    $.ajax({
        type: "POST",
        url: Filters3000Core.getUrl(),
        data: $('#filters3000').serializeArray(),

        success: function(data) {

            $(Filters3000Core.getSelector()).html(data);

            f3000_unlock_save();
            if ('function' == typeof(callback))
            {
                callback();
            }
            f3000_spinner(false);
        },
        dataType: 'html'
    });
}

$(document).ready(function() {
    //подсоеденимся к событиям клика на опцию...
    $(document).on('change', '#filters3000 .f3000_value', {}, function(e) {
        //$(this).css({top: });

        var elem = $(this);
        update_filter3000(function() {
            $('.f3000_submit_filter').hide();
            $('#' + elem.attr('data-parent-id')).show('slow');

            return false;
        });
    });

    /*bw*/
    $(document).on('click', '.f3000_value_href', {}, function(e) {

        var elem = $(this),
            href = elem.data('href-id'),
            input = elem.data('input-id');

		if($('#' + input).prop( 'checked' ))
		{
			$('#' + input).removeAttr( 'checked' );
			elem.removeClass("active");
		}
		else
		{
			$('#' + input).attr( 'checked', 'checked' );
			elem.addClass("active");
		}

        update_filter3000(function() {
            $('.f3000_submit_filter').hide();
            $('#'+href).show('slow');

            return false;
        });
    });

    $(document).on('click', '.link-show-more', {}, function(e) {

        var elem = $(this),
            hidden = elem.data('hidden-id');
            filter = elem.data('filter-id');

        if ( elem.hasClass("show") )
        {
        	$("." + hidden).hide();
        	//$("#link_show_more_"+filter).val("");
        	$("#link_show_more_"+filter).removeAttr( 'checked' );
        	elem.removeClass("show");
        	elem.html("Показать все");
        }
        else
        {        	$("." + hidden).show();
        	//$("#link_show_more_"+filter).val(filter);
        	$("#link_show_more_"+filter).attr( 'checked', 'checked' );
        	elem.addClass("show");
        	elem.html("Свернуть");
        }
	});

    //...и кнопке обновления цены
    $(document).on('click', '.price_submit', {}, function() {
        update_filter3000();
    });

    $(document).on('click', '.order_submit', {}, function() {
        var elem = $(this),
            sort = elem.data('sort'),
            order = elem.data('order');

	    $('input[name="sort"]').val(sort);
	    $('input[name="order"]').val(order);

        update_filter3000()

        $('#filters3000').submit();
    });

    $(document).on('f3000_loaded', function() {

    });
});


//конвертация валюты перед отправкой формы
$(document).on('submit', '#filters3000', {}, function(e) {
    return true;
});