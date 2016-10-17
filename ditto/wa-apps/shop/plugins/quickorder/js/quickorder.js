/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */
jQuery(document).ready(function($) {
    $.quickorder = {
        locale: '',
        messages: {
            "The shopping cart is empty": "Корзина пуста",
            "Total": "Сумма заказа",
            "Wait, please. Redirecting": "Идет перенаправление",
            "Something wrong": "Произошла ошибка"
        },
        productFormData: [],
        isCartSubmit: false,
        stopInit: 0,
        aftercallback: "",
        // Всплывающая форма
        dialog: {
            show: function(btn, cartSubmit) {
                $.quickorder.isCartSubmit = cartSubmit ? true : false;
                $("body").children(".quickorder-custom-form").remove();
                btn = $(btn);
                if ($("#cart-form").length) {
                    $.quickorder.productFormData = $("#cart-form").serializeArray() || [];
                } else if (!cartSubmit) {
                    $.quickorder.productFormData = btn.parents("form").serializeArray() || [];
                }
                var quickorderWrap = btn.parent().next(".quickorder-custom-form").clone();
                quickorderWrap.find(":disabled").prop("disabled", false);
                quickorderWrap.appendTo("body").show().find(".quickorder-body").wrap("<form action='" + quickorderWrap.find(".quickorder-wrap").attr('data-action') + "' class='quickorder-form' onsubmit='return false;'></form>");
                $("<div class='quickorder-overlay'></div>").click(function() {
                    $("body > .quickorder-custom-form").remove();
                    $(this).remove();
                }).appendTo("body");
                $.quickorder.autoHeight(quickorderWrap);
                if (cartSubmit) {
                    $.post(cartSubmit, {}, function(response) {
                        var html = $.quickorder.translate("The shopping cart is empty");
                        if (response.status == 'ok' && response.data) {
                            html = "<div class='quickorder-total'>" + $.quickorder.translate("Total") + ": <b>" + response.data + "</b></div>";
                        }
                        quickorderWrap.find(".quickorder-order").html(html);
                        $.quickorder.autoHeight(quickorderWrap);
                    }, "json");
                }
            },
            hide: function(btn) {
                btn = $(btn);
                btn.parents(".quickorder-custom-form").fadeOut(function() {
                    $(this).remove();
                });
                $(".quickorder-overlay").fadeOut(function() {
                    $(this).remove();
                });
            }
        },
        // Обработчик формы
        formHandler: function(btn) {
            var form = btn.parents("form.quickorder-form");
            var errormsg = form.find(".errormsg");
            errormsg.text("");

            btn.next("i.icon16").remove();
            // Проверяем заполнены ли все поля формы
            var required = false;
            $.each(form.find(".f-required"), function(i, elem) {
                elem = $(elem);
                if ($.trim(elem.val()) == '') {
                    required = true;
                    $.quickorder.fieldIsEmpty(elem);
                }
            });
            if (required) {
                return false;
            }
            btn.attr('disabled', 'disabled').after("<i class='icon16 qp loading temp-loader'></i>");
            var data = form.serializeArray();
            var cartData = $.quickorder.productFormData;
            var mergeData = data.concat(cartData);
            var uniqueData = [];
            var tempData = {};
            for (var i = 0; i < mergeData.length; i++) {
                if (typeof tempData[mergeData[i].name] == 'undefined' || (typeof tempData[mergeData[i].name] !== 'undefined' && tempData[mergeData[i].name] !== mergeData[i].value && $.trim(mergeData[i].value) !== '')) {
                    tempData[mergeData[i].name] = mergeData[i].value;
                    uniqueData.push(mergeData[i]);
                }
            }
            if ($.quickorder.isCartSubmit) {
                uniqueData.push({name: "isCartSubmit", value: "1"});
            }
            $.ajax({
                url: form.attr('action'),
                data: uniqueData,
                dataType: "json",
                type: "post",
                success: function(response) {
                    btn.removeAttr('disabled').next(".temp-loader").remove();
                    if (typeof response.errors != 'undefined') {
                        errormsg.append(response.errors + "<br />");
                    } else if (response.status == 'ok' && response.data) {
                        if ($.quickorder.aftercallback) {
                            var func = new Function('params', $.quickorder.aftercallback);
                            func(response.data.params);
                        }
                        if (response.data.redirect) {
                            form.find(".quickorder-body").html("<i class='icon16 qp loading'></i> " + $.quickorder.translate("Wait, please. Redirecting"));
                            location.href = response.data.redirect;
                        } else {
                            form.find(".quickorder-body").html(response.data.text);
                        }
                    } else {
                        btn.after("<i class='icon16 qp no'></i>");
                    }
                },
                error: function() {
                    errormsg.text($.quickorder.translate("Something wrong"));
                    btn.removeAttr('disabled').next(".temp-loader").remove();
                    btn.after("<i class='icon16 qp no'></i>");
                }
            });
            return false;
        },
        // Добавление класса для пустого поля
        fieldIsEmpty: function(field) {
            field.addClass('quickorder-empty-field').click(function() {
                $(this).removeClass("quickorder-empty-field");
            });
        },
        translate: function(message) {
            if (this.locale == 'ru_RU' && typeof this.messages[message] !== 'undefined' && this.messages[message] !== '') {
                return this.messages[message];
            }
            return message;
        },
        init: function() {
            $(document).on({
                click: function() {
                    $.quickorder.formHandler($(this));
                }
            }, ".quickorder-form input[type='submit']");
        },
        autoHeight: function(elem) {
            var quickorder = elem.find(".quickorder-wrap");
            var height = quickorder.height();
            var windowHeight = $(window).height();
            if (height > windowHeight) {
                quickorder.find(".quickorder-body").css('height', (windowHeight - 100) + "px");
            }
            quickorder.center();
        }
    };
    $(function() {
        if ($.quickorder.stopInit == 0) {
            $.quickorder.stopInit++;
            $.quickorder.init();
            // Если выбрали товар, которого нету, то скрываем кнопку быстрого заказа
            $("#product-skus input[type=radio]").click(function() {
                if ($(this).data('disabled')) {
                    $(".quickorder-custom-button").hide();
                } else {
                    $(".quickorder-custom-button").show();
                }
            });
            $("#product-skus input[type=radio]:checked").click();
            $("select.sku-feature").change(function() {
                var key = "";
                $("select.sku-feature").each(function() {
                    key += $(this).data('feature-id') + ':' + $(this).val() + ';';
                });
                var sku = sku_features[key];
                if (sku) {
                    if (sku.available) {
                        $(".quickorder-custom-button").show();
                    } else {
                        $(".quickorder-custom-button").hide();
                    }
                } else {
                    $(".quickorder-custom-button").hide();
                }
            });
            $("select.sku-feature").change();
        }
    });
});
jQuery.fn.center = function() {
    this.css("marginTop", (-1) * Math.max(0, $(this).outerHeight() / 2) + "px");
    this.css("marginLeft", (-1) * Math.max(0, $(this).outerWidth() / 2) + "px");
    return this;
};