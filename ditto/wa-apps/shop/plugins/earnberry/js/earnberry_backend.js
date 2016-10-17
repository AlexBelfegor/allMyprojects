$(window).load(function() {

    $.earnberry_new_order = {
        status: 0,
        transactionId: false,
        locked: false,
        code: false,
        formLoaded: false,
        init: function() {
            if (true)
            {
                this.status = 1;
                this.loadForm();
                var self = this;
                $(document).off('click', '#earnberry_do_transaction_button').on('click', '#earnberry_do_transaction_button', function() {
                    if (!self.locked)
                    {
                        self.createTransaction();
                    }

                });
            }
        },
        loadForm: function()
        {
            var url = earnberry_ajax_actions.neworderform;
            if (this.transactionId)
            {
                url = url + '&transactionId=' + this.transactionId;
            }
            $('#earnberry_order_form').remove();
            $.getJSON(url, function(json) {
                $('#order-items tfoot').append(json.data.html);
                $.earnberry_new_order.formLoaded = true;
                $.earnberry_new_order.loadCode();
            });
        },
        loadCode: function()
        {
            if ($.earnberry_new_order.formLoaded && $.earnberry_new_order.code)
            {
                $('#earnberry_order_form input.earnberry_tracking_id').val($.earnberry_new_order.code);
            }
        },
        lockForm: function()
        {
            $('input[type=submit]').attr('disabled', 'disabled');
            this.locked = true;
        },
        unlockForm: function()
        {
            $('input[type=submit]').attr('disabled', false);
            this.locked = false;
        },
        createTransaction: function()
        {
            var self = this;
            var url = earnberry_ajax_actions.sendnewtransaction;
            var form = $('#order-edit-form');
            //this.loadForm();
            $.ajax({
                url: url,
                data: form.serializeArray(),
                dataType: 'json',
                type: 'post',
                success: function (response) {
                    var data = response.data;
                    if (data.result == 'OK')
                    {
                        self.transactionId = data.id;
                        self.loadForm();
                    }
                    else
                    {
                        $('<div>' + data.error_text + '</div>').waDialog({
                            'buttons': '<input type="submit" value="OK" />',
                            onSubmit: function (d) {
                                d.trigger('close');
                                return false;
                            }
                        });
                    }
                },
                error: function () {

                }
            }).always(function() {
                self.unlockForm();
            });
        },
        disattachForm: function()
        {
            $(document).off('autocompleteselect', '#orders-add-autocomplete');
            $(document).off('autocompletesearch', '#orders-add-autocomplete');
        },
        attachToForm: function()
        {
            $(document).off('autocompleteselect', '#orders-add-autocomplete');
            $(document).on('autocompleteselect', '#orders-add-autocomplete', function(e, ui) {
                if (window.location.hash.match('#/orders/new/'))
                {
                    $.earnberry_new_order.init();
                }
            });
            $(document).off('autocompletesearch', '#orders-add-autocomplete');
            $(document).on('autocompletesearch', '#orders-add-autocomplete', function(e, ui) {

                if ($(this).data('-eb-protect') === true)
                {
                    $(this).data('-eb-protect', false);
                    return true;
                }
                var term = $.trim($(this).val());

                var rgx = new RegExp('^@(.+)');
                var force_normal_search = rgx.exec(term.toString());

                var self = this;
                if (typeof(force_normal_search) == 'object' && force_normal_search != null)
                {
                    //$(self).val();
                    var v = force_normal_search[1];

                    if (v.length < $(self).autocomplete( "option", "minLength" ))
                    {
                        return false;
                    }

                    $(self).data('-eb-protect', true);
                    $(self).autocomplete("search", v);

                    return false;

                }
                else
                {

                    var earnberry_product_code_regexp = new RegExp('^([0-9]+)-([а-яА-Яa-zA-Z0-9]+)$');
                    var matches = term.match(earnberry_product_code_regexp);

                    if (typeof(matches) == 'object' && matches != null)
                    {
                        e.preventDefault();

                        var url = earnberry_ajax_actions.autocompletebycode + '&term=' + term;
                        $.getJSON(url, function(json) {
                            if (json.data.result == 'OK')
                            {
                                //$(self).val(json.data.name);
                                $(self).data('-eb-protect', true);
                                $(self).autocomplete("search", json.data.name);
                                $.earnberry_new_order.code = json.data.code;
                                $.earnberry_new_order.loadCode();
                            }
                            else
                            {
                                $(self).data('-eb-protect', true);
                                $(self).autocomplete("search", term);
                            }
                        });
                        return false;
                    }
                }

            });
        }

    }



    $.earnberry_new_order.hashChangeHandler = function(e)
    {
        if (! this.location.hash.match('#/orders/new/'))
        {
            $.earnberry_new_order.transactionId = false;
            $.earnberry_new_order.code = false;
            $.earnberry_new_order.disattachForm();
            //$(window).off('hashchange', $.earnberry_new_order.hashChangeHandler);
        }
        else
        {
            $.earnberry_new_order.attachToForm();
        }

    }
    $(window).on('hashchange', $.earnberry_new_order.hashChangeHandler);
    if (this.location.hash.match('#/orders/new/'))
    {
        $.earnberry_new_order.attachToForm();
    }

});