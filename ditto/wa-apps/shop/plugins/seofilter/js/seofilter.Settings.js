$(function () {
    function updateData(item, response)
    {
        $(item+' option').each(function(index, el) {
            $(this).remove();
        });

        if (item == '.seo-features_category-select') {
            var html = "<option value='0'>-- Выбрать --</option>";

            $.each(response.data.values, function(x, val_x) {
                html += "<option value='" + val_x.id + "'>" + val_x.marker + val_x.name + "</option>";
            });

            $('.seo-features_select option').removeClass('filled');
            $.each(response.data.filled_features, function(f, val_f) {
                $('.seo-features_select option[value='+val_f+']').addClass('filled');
            });
        } else {
            var html = "";

            $.each(response.data.values, function(x, val_x) {
                html += "<option value='" + x + "'>" + val_x + "</option>";
            });
        }

        $(item).html(html);

        $('.seo-features_values-select option, .seo-features_category-select option').removeClass('filled');
        $.each(response.data.filled_values, function(f, val_f) {
            $('.seo-features_values-select option[value='+val_f+']').addClass('filled');
        });
        $.each(response.data.filled_categories, function(f, val_f) {
            $('.seo-features_values-select option[value='+val_f+']').addClass('filled');
        });

        if (response.data.seo) {
            fillFields(response.data.seo);
        }
    }

    function fillFields(response)
    {
        $.each(response, function(y, val_y) {
            if (y == 'url') {
                if ($('.seo-features_url input').is(':visible')) {
                    $('.seo-features_url input').hide();
                    $('.seo-features_url span, #seo-features_url-edit').show();
                }

                $('.seo-features_url span').text(val_y);
                $('.seo-features_url input').val(val_y);

                if (val_y == '') {
                    $('#seo-features_url-edit').hide();
                } else {
                    $('#seo-features_url-edit').show();
                }
            } else {
                var field = $('.seo-settings__features').find("*[name='seo-features["+y+"]']");

                if (field.length) {
                    field.val(val_y);

                    if (y == 'seo_desc') {
                        $('.seo-settings__features .redactor-editor').html('<p>'+val_y+'</p>');
                    };
                } else {
                    field.val('');

                    if (y == 'seo_desc') {
                        $('.seo-settings__features .redactor-editor').html('');
                    };
                }
            }
        });
    }

    $.shop.changeListener($('.seo-settings__storefront-select'), function(_select) {
        $('.seo-settings__storefront, .seo-settings-storefront-name').removeClass('seo-settings__storefront_open');
        $('.seo-settings__storefront_key_' + _select.val()).addClass('seo-settings__storefront_open');

        if (_select.val() === '0') {
            var storefront_name = 'general';
            $('.seo-settings-storefront-name').removeClass('seo-settings__storefront_open');
        } else {
            var storefront_name = $(".seo-settings__storefront-select :selected").text();
        }

        $('input[name=current_storefront]').val(storefront_name);

        var feature_id = $('.seo-features_select').val(),
            value_id = $('.seo-features_values-select').val(),
            all_feature_ids = new Array();

        $('.seo-features_select option').each(function() {
            all_feature_ids.push($(this).val());
        });

        $.get(
            '?plugin=seofilter&module=settingsFeatureValues&action=getSeoFeature',
            {
                storefront: storefront_name,
                feature_id: feature_id,
                value_id: value_id,
                category_id: 0,
                all_feature_ids: all_feature_ids
            },
            function(response) {
                updateData(".seo-features_category-select", response);
            },
        'json');
    });

    $.shop.changeListener($('.seo-features_select'), function(_select) {
        var storefront = $('input[name=current_storefront]').val(),
            category_id = $('.seo-features_category-select').val();

        $.get(
            '?plugin=seofilter&module=settingsFeatureValues&action=getSeoFeature',
            {
                storefront: storefront,
                feature_id: _select.val(),
                category_id: category_id
            },
            function(response) {
                updateData(".seo-features_values-select", response);
            },
        'json');
    });

    $.shop.changeListener($('.seo-features_values-select'), function(_select) {
        var storefront = $('input[name=current_storefront]').val(),
            feature_id = $('.seo-features_select').val(),
            category_id = $('.seo-features_category-select').val();

        $.get(
            '?plugin=seofilter&module=settingsFeatureValues&action=getSeoValue',
            {
                storefront: storefront,
                feature_id: feature_id,
                value_id:_select.val(),
                category_id: category_id
            },
            function(response) {
                var category_id = $('.seo-features_category-select').val();

                $('.seo-features_category-select option').removeClass('filled');
                $.each(response.data.filled_categories, function(f, val_f) {
                    $('.seo-features_category-select option[value='+val_f+']').addClass('filled');
                });

                if (response.data.seo) {
                    fillFields(response.data.seo);
                }
            },
        'json');
    });

    $.shop.changeListener($('.seo-features_category-select'), function(_select) {
        var storefront = $('input[name=current_storefront]').val(),
            feature_id = $('.seo-features_select').val(),
            value_id   = $('.seo-features_values-select').val(),
            all_feature_ids = new Array();

        $('.seo-features_select option').each(function() {
            all_feature_ids.push($(this).val());
        });

        $.get(
            '?plugin=seofilter&module=settingsFeatureValues&action=getSeoValue',
            {
                storefront: storefront,
                feature_id: feature_id,
                value_id: value_id,
                category_id: _select.val(),
                all_feature_ids: all_feature_ids
            },
            function(response) {
                if (response.data.seo) {
                    fillFields(response.data.seo);
                }
            },
        'json');
    });

    $.shop.changeListener($('.seo-features_seo-name'), function(_input) {
        $.get(
            '?plugin=seofilter&module=settingsAutocompleteUrl',
            {
                value: _input.val()
            },
            function(response) {
                $('.seo-features_url span').text(response.data.url);
            },
        'json');
    });

    $('#seo-features_url-edit').on('click', function (event) {
        event.preventDefault();
        $(this).hide();
        $(this).siblings('span').hide();
        $(this).siblings('input').show();
    });

    $('.seo-settings__extra').on('click', function (event) {
        event.preventDefault();
        $('.seo-settings__features-extra').fadeToggle(100);
    });

    $('.description').on('click', '#bc_instruction', function(event) {
        event.preventDefault();

        if ($(this).siblings('ul').is(':visible')) {
            $(this).siblings('ul').hide();
        } else {
            $(this).siblings('ul').show();
        }
    });

    $('.helper__var-code').on('click', function () {
        if (window.getSelection) {
            window.getSelection().selectAllChildren(this);
        } else { // старый IE
            var range = document.selection.createRange();
            range.moveToElementText(this);
            range.select();
        }
    });

    $('.seo-features_seo-desc').redactor();
    $('.seo-settings_common-desc').redactor();

    $('#template-link').click(function () {
        $(this).hide();
        $('#template-div').show();
        $('#template-textarea').removeAttr('disabled');
        CodeMirror.fromTextArea(document.getElementById('template-textarea'), {
            mode: "text/javascript",
            tabMode: "indent",
            height: "dynamic",
            lineWrapping: true,
            onChange: function(cm) {
                $('#template-textarea').val(cm.getValue());
            }
        });
        return false;
    });

    $('.seo-settings__form').on('submit', function () {
        var $form = $(this);
        $form.find('button[type=submit]').attr('disabled', 'disabled');
        $('.seo-settings__success-message').remove();

        $.post($form.attr('action'), $form.serialize(), function (response) {
            $('.seo-settings__footer').html($(response).find('.seo-settings__footer').html());

            if ($('.seo-features_url input[type=text]').is(':visible')) {
                $('.seo-features_url span').text( $('.seo-features_url input[type=text]').val() );
                $('.seo-features_url span, #seo-features_url-edit').show();
                $('.seo-features_url input[type=text]').hide();
            } else {
                $('.seo-features_url input[type=text]').val( $('.seo-features_url span').text() );
                $('#seo-features_url-edit').show();
            }
        }, 'html');

        return false;
    });
});
