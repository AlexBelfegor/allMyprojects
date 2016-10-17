<?php

class shopKmgtmPluginAddPurchaseController extends waJsonController
{
    public function execute()
    {
        $order = waRequest::post('order', false, waRequest::TYPE_ARRAY);
        $domain = waRequest::post('domain');
        $ua_id = shopKmgtmPlugin::retrieveUAid($domain);
        if ($order && $ua_id) {
            $ua = new shopKmgtmPluginUA($ua_id);

            $ua->addPurchase($order);
        }
    }
}