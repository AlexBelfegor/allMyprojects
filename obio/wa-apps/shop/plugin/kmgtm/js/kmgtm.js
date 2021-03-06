'use strict';
var kmgtm = function (params) {
    $('#product-list .product-list, .related .product-list').on('click', 'li a', function (e) {
        var that = $(this),
            url = that.attr('href'),
            all = that.closest('.product-list'),
            wrapper = that.closest('li'),
            category = $('.category-name'),
            image = wrapper.find('img[src*="/wa-data/public/shop/products/"]'),
            id = image.length ? image.attr('src').match(/\/wa-data\/public\/shop\/products\/(\d+)\//) : [],
            position = 0;

        try {
            all.find('li a').each(function () {
                position++;
                if ($(this).attr('href') === url) {
                    return false;
                }
            });
            var product = {
                'url': url,
                'id': id.length && id.length == 2 ? id[1] : '',
                'name': that.attr('title'),
                'category': category.length ? category.text().trim() : '',
                'position': position
            };
            params.ua && kmOnProductClickUA(product);
            params.gtm && kmOnProductClickGTM(product);
        } catch (err) {
            window.console && console.log(err);
        }
    });

    var kmgtmCartQ = {};
    $('input[name^="quantity["]').each(function () {
        var that = $(this),
            item_id = parseInt(that.attr('name').match(/quantity\[(\d+)\]/)[1]);
        kmgtmCartQ[item_id] = parseInt(that.val());
    });
    $(document).ajaxSuccess(function (e, j, a, d) {
        if (a.url === "save/" || a.url === "delete/") {
            var item_id = a.data.match(/id=(\d+)/),
                quantity = parseInt(d.data.q),
                dlt = (a.url === "delete/") ? 1 : 0;

            quantity = quantity || a.data.match(/quantity=(\d+)/);
            quantity = (quantity && quantity.length && quantity.length === 2 && quantity[1] !== undefined) ? parseInt(quantity[1]) : quantity;
            item_id = (item_id.length && item_id.length === 2 && item_id[1] !== undefined) ? parseInt(item_id[1]) : false;
            quantity = dlt ? kmgtmCartQ[item_id] : quantity;
            var quantity_old = kmgtmCartQ[item_id] !== undefined ? kmgtmCartQ[item_id] : 0;
            if (item_id && quantity) {
                $.post(
                    params.url,
                    {
                        item_id: item_id,
                        quantity: quantity,
                        quantity_old: quantity_old,
                        delete: dlt
                    },
                    function (r) {
                        var product = r.data;
                        kmgtmCartQ[item_id] = quantity;
                        try {
                            params.ua && kmOnProductAddRemoveToCartUA(product);
                            params.gtm && kmOnProductAddRemoveToCartGTM(product);
                        } catch (err) {
                            window.console && console.info(err);
                        }
                    },
                    'json'
                );
            }
        } else if (/\/cart\/add\//.test(a.url)) {
            var product_id = a.data.match(/product_id=(\d+)/),
                sku_id = a.data.match(/sku_id=(\d+)/);

            product_id = (product_id.length && product_id.length === 2 && product_id[1] !== undefined) ? parseInt(product_id[1]) : false;
            sku_id = (sku_id && sku_id.length && sku_id.length === 2 && sku_id[1] !== undefined) ? parseInt(sku_id[1]) : false;
            if (product_id) {
                $.post(
                    params.url,
                    {
                        product_id: product_id,
                        sku_id: sku_id
                    },
                    function (r) {
                        var product = r.data;
                        try {
                            params.ua && kmOnProductAddRemoveToCartUA(product);
                            params.gtm && kmOnProductAddRemoveToCartGTM(product);
                        } catch (err) {
                            window.console && console.info(err);
                        }
                    },
                    'json'
                );
            }
        }
    });
};