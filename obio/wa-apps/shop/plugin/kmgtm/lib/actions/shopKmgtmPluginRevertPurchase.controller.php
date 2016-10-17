<?php

class shopKmgtmPluginRevertPurchaseController extends waJsonController
{
    public function execute()
    {
        $order_id = waRequest::post('order_id', false, waRequest::TYPE_INT);
        $domain = waRequest::post('domain');
        $ua_id = shopKmgtmPlugin::retrieveUAid($domain);
        if ($order_id && $ua_id) {
            $order_model = new shopOrderModel();
            $order = $order_model->getById($order_id);
//            $order_params_model = new shopOrderParamsModel();
//            $order['params'] = $order_params_model->get($order_id);
//            $order_items_model = new shopOrderItemsModel();
//            $order['items'] = $order_items_model->getByField('order_id', $order_id, true);

            $ua = new shopKmgtmPluginUA($ua_id);
            $ua->addRevert($order);
        }
    }
}