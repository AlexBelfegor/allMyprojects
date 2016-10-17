$(function () {

    function updateCart(data)
    {
        $(".cart-total").html(data.total);
        $(".cart-discount").html('&minus; ' + data.discount);
    }

    // add to cart block: services
    $(".services input:checkbox").click(function () {
        var obj = $('select[name="service_variant[' + $(this).closest('tr').data('id') + '][' + $(this).val() + ']"]');
        if (obj.length) {
            if ($(this).is(':checked')) {
                obj.removeAttr('disabled');
            } else {
                obj.attr('disabled', 'disabled');
            }
        }
    });

    $(".cart input.qty").change(function () {
        var that = $(this);
        if (that.val() > 0) {
            var tr = that.closest('tr');
            if (that.val()) {
                $.post('save/', {id: tr.data('id'), quantity: that.val()}, function (response) {
                    tr.find('.item-total').html(response.data.item_total);
                    if (response.data.q) {
                        that.val(response.data.q);
                    }
                    updateCart(response.data);
                }, "json");
            }
        } else {
            that.val(1);
        }
    });

    $(".cart a.delete").click(function () {
        var tr = $(this).closest('tr');
        $.post('delete/', {id: tr.data('id')}, function (response) {
            tr.remove();
            updateCart(response.data);
        }, "json");
    });

    $(".cart input.qty").change(function () {
        var that = $(this);
        if (that.val() > 0) {
            var tr = that.closest('tr');
            if (that.val()) {
                $.post('save/', {id: tr.data('id'), quantity: that.val()}, function (response) {
                    tr.find('.item-total').html(response.data.item_total);
                    if (response.data.q) {
                        that.val(response.data.q);
                    }
                    updateCart(response.data);
                }, "json");
            }
        } else {
            that.val(1);
        }
    });

    $(".cart .services input:checkbox").change(function () {
        var div = $(this).closest('div');
        if ($(this).is(':checked')) {
           var parent_id = $(this).closest('tr').data('id')
           var data = {parent_id: parent_id, service_id: $(this).val()};
           var variants = $('select[name="service_variant[' + parent_id + '][' + $(this).val() + ']"]');
           if (variants.length) {
               data['service_variant_id'] = variants.val();
           }
           $.post('add/', data, function(response) {
               div.data('id', response.data.id);
               updateCart(response.data);
           }, "json");
        } else {
           $.post('delete/', {id: div.data('id')}, function (response) {
               div.data('id', null);
               updateCart(response.data);
           }, "json");
        }
    });

    $(".cart .services select").change(function () {
        $.post('save/', {id: $(this).closest('div').data('id'), 'service_variant_id': $(this).val()}, function (response) {
            updateCart(response.data);
        }, "json");
    });

    $("#cancel-affiliate").click(function () {
        $(this).closest('form').append('<input type="hidden" name="use_affiliate" value="0">').submit();
        return false;
    });

    /*bw*/
    $('.cart .plus, .cart .minus').click(function ()
    {
		var type = $(this).data('type');
		var that = $(this).parent().find('input');

	    if(type == 'plus')
	    {
	    	that.val(parseInt(that.val()) + 1);
        }
        else
        {
        	that.val(parseInt(that.val()) - 1);
        }

        if (that.val() > 0) {
            var tr = that.closest('tr');
            if (that.val()) {
                $.post('save/', {id: tr.data('id'), quantity: that.val()}, function (response) {
                    tr.find('.item-total').html(response.data.item_total);

                    if (response.data.q) {
                        that.val(response.data.q);
                    }

                    updateCart(response.data);
                }, "json");
            }
        } else {
            that.val(1);
        }
    });

});