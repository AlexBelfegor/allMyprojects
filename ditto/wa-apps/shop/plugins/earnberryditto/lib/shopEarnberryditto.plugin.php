<?php

class shopEarnberrydittoPlugin extends shopPlugin
{
    public function workupProduct($params)
    {
        $item = $params[1];
        $transactionProduct = $params[0];
        $model = new shopProductFeaturesModel();
        if ($v = $model->getValues($item['product_id'], $item['sku_id']))
        {
            $transactionProduct['sku'] = $item['product_id'];
            if (isset($v['brand']))
            {
                $transactionProduct['brand'] = $v['brand'];
            }
            if (isset($v['size']) && intval($v['size']) > 0)
            {
                $transactionProduct['variant'] = $v['size'];
            }
            elseif (isset($v['material_sumki']))
            {
                $transactionProduct['variant'] = 'none';
            }
        }
        return $transactionProduct;
    }
    public function wrapTransaction($params)
    {
        /**
         *
         * Params array content:
         *
         * $orderId, $orderData, $trackingId, $transactionPrefix, $marker, $this->getEarnberrySettings(), $calculatedOrderData
         */
        if ($params[5]['universal_analytics'] == 2 && $params[2])
        {
            $calculatedOrderData = $params[6];

            $transactionProducts = $calculatedOrderData['calculated']['items'];
            $total = $calculatedOrderData['calculated']['income'];
            $grossprofit = $calculatedOrderData['calculated']['grossprofit'];
            $orderData = $params[1];
            $marker = $params[4];
            $trackingId = $params[2];
            $transactionPrefix = $params[3];
            $orderId = $params[0];

            $t = new EarnberryTransactionPurchase();
            $t->setPageview('ditto.ua', '/checkout/success/', 'Спасибо!');
            $t->setAffiliation('ditto.ua');

            $t->setCurrency($orderData['currency']);
            $t->setMarker($marker);

            $t->setTrackingId($trackingId);
            $t->setOrderId($transactionPrefix . $orderId);

            foreach ($transactionProducts as $tp)
            {
                $t->addProduct($tp['sku'], $tp['name'], $tp['category'], $tp['brand'], $tp['variant'], false, $tp['price'], $tp['quantity']);
            }
            $t->setTotal($total);
            $t->setGrossProfit($grossprofit);
            return $t;
        }
        else
        {
            return false;
        }
    }
}