$(document).ready(function () {

    //PRODUCT FILTERING
    var f = function () {

        var ajax_form_callback = function (f) {
            var fields = f.serializeArray();
            var params = [];
            for (var i = 0; i < fields.length; i++) {
                if (fields[i].value !== '') {
                    params.push(fields[i].name + '=' + fields[i].value);
                }
            }
            var url = '?' + params.join('&');
            $(window).lazyLoad && $(window).lazyLoad('sleep');
            $('#product-list').html('<img src="' + f.data('loading') + '">');
            $.get(url+'&_=_', function(html) {
                var tmp = $('<div></div>').html(html);
                $('#product-list').html(tmp.find('#product-list').html());
                if (!!(history.pushState && history.state !== undefined)) {
                    window.history.pushState({}, '', url);
                }
                $(window).lazyLoad && $(window).lazyLoad('reload');
            });
        };

        $('.filters.ajax form input').change(function () {
            ajax_form_callback($(this).closest('form'));
        });
        $('.filters.ajax form').submit(function () {
            ajax_form_callback($(this));
            return false;
        });

        $('.filters .slider').each(function () {
            if (!$(this).find('.filter-slider').length) {
                $(this).append('<div class="filter-slider"></div>');
            } else {
                return;
            }
            var min = $(this).find('.min');
            var max = $(this).find('.max');
            var min_value = parseFloat(min.attr('placeholder'));
            var max_value = parseFloat(max.attr('placeholder'));
            var step = 1;
            var slider = $(this).find('.filter-slider');
            if (slider.data('step')) {
                step = parseFloat(slider.data('step'));
            } else {
                var diff = max_value - min_value;
                if (Math.round(min_value) != min_value || Math.round(max_value) != max_value) {
                    step = diff / 10;
                    var tmp = 0;
                    while (step < 1) {
                        step *= 10;
                        tmp += 1;
                    }
                    step = Math.pow(10, -tmp);
                    tmp = Math.round(100000 * Math.abs(Math.round(min_value) - min_value)) / 100000;
                    if (tmp && tmp < step) {
                        step = tmp;
                    }
                    tmp = Math.round(100000 * Math.abs(Math.round(max_value) - max_value)) / 100000;
                    if (tmp && tmp < step) {
                        step = tmp;
                    }
                }
            }
            slider.slider({
                range: true,
                min: parseFloat(min.attr('placeholder')),
                max: parseFloat(max.attr('placeholder')),
                step: step,
                values: [parseFloat(min.val().length ? min.val() : min.attr('placeholder')),
                    parseFloat(max.val().length ? max.val() : max.attr('placeholder'))],
                slide: function( event, ui ) {
                    var v = ui.values[0] == $(this).slider('option', 'min') ? '' : ui.values[0];
                    min.val(v);
                    v = ui.values[1] == $(this).slider('option', 'max') ? '' : ui.values[1];
                    max.val(v);
                },
                stop: function (event, ui) {
                    min.change();
                }
            });
            min.add(max).change(function () {
                var v_min =  min.val() === '' ? slider.slider('option', 'min') : parseFloat(min.val());
                var v_max = max.val() === '' ? slider.slider('option', 'max') : parseFloat(max.val());
                if (v_max >= v_min) {
                    slider.slider('option', 'values', [v_min, v_max]);
                }
            });
        });
    };
    f();

});

