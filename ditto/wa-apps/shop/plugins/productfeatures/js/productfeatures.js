$('#productfeatures-plugin').click(function () {
    var products = $.product_list.getSelectedProducts(true);
    if (!products.count) {
        alert($_('Please select at least one product'));
        return false;
    }

    $.post('?plugin=productfeatures&module=dialog', products.serialized, function (response) {
        $('#productfeatures-plugin-dialog').waDialog({
            title: $_('Features'),
            content: response,
            buttons: '<input type="submit" value="' + $_('Save') + '" class="button green" /> или <a href="#" class="cancel">отмена</a>',
            onLoad: function () {
                $('.productfeatures-count').html(products.count);
                if (products.count % 10 == 1 && products.count % 100 != 10) {
                    $('.productfeatures-w').html('товара');
                } else {
                    $('.productfeatures-w').html('товаров');
                }
            },
            onSubmit: function (d) {
                $.post('?plugin=productfeatures&module=save', $(this).serializeArray().concat(products.serialized), function (response) {
                    d.trigger('close');
                }, 'json');
                return false;
            }
        });
    });
    return false;
});