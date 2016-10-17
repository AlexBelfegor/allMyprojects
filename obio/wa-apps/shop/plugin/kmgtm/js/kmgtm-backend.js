(function () {
    kmgtm = window.kmgtm || {};
    kmgtm.ajaxSuccess_setup = false;
    if (!kmgtm.ajaxSuccess_setup) {
        $(document).ajaxSuccess(function (e, j, a, d) {
            kmgtm.ajaxSuccess_setup = true;
            if (kmgtm.current_domain) {
                if (kmgtm.current_action_regexp && a.url === "?module=workflow&action=prepare" && kmgtm.current_action_regexp.test(a.data)) {
                    var order = a.data.match(kmgtm.current_action_regexp);
                    if (order.length && order.length === 2 && order[1] !== undefined) {
                        $.post(
                            '?plugin=kmgtm&module=revertPurchase',
                            {
                                order_id: order[1],
                                domain: kmgtm.current_domain
                            },
                            function () {
                            },
                            'json'
                        );
                    }
                }/* kmgtm.current_order_id  ???
                 else if (kmgtm.current_order_id === 0 && a.url === "?module=order&action=save" && d.status === 'ok' && d.data.order !== undefined) {
                    $.post(
                        '?plugin=kmgtm&module=addPurchase',
                        {
                            order: d.data.order,
                            domain: kmgtm.current_domain
                        },
                        function () {},
                        'json'
                    );
                } else if (kmgtm.current_order_id && a.url === "?module=order&action=save" && d.status === 'ok' && d.data.order !== undefined) {
                    $.post(
                        '?plugin=kmgtm&module=changePurchase',
                        {
                            order: kmgtm.current_order_id,
                            domain: kmgtm.current_domain,
                            order_old: kmgtm.current_order
                        },
                        function () {},
                        'json'
                    );
                }*/
            }
        });
    }
}());