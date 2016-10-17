<?php

/**
 * Class shopKmgtmPluginRevertPurchaseController
 * not using for now
 */
class shopKmgtmPluginRevertPurchaseController extends waJsonController
{
    public function execute()
    {
        $order_id = waRequest::post('order_id', false, waRequest::TYPE_INT);
        $domain = waRequest::post('domain');
        $order_old = waRequest::post('order_old');
        $ua_id = shopKmgtmPlugin::retrieveUAid($domain);
        if ($order_id && $order_old && $ua_id) {
            $order_model = new shopOrderModel();
            $order = $order_model->getById($order_id);
            $ua = new shopKmgtmPluginUA($ua_id);
            $ua->addRevert($order);
        }
    }
}