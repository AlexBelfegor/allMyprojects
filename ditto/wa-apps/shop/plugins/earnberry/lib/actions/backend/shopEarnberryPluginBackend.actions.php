<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

class shopEarnberryPluginBackendActions extends waJsonActions
{
    public function defaultAction()
    {

    }
    public function autocompletebycodeAction()
    {
        $this->plugin();
        $codeWithId = waRequest::get('term', '', waRequest::TYPE_STRING_TRIM);

        $parsed = explode('-', $codeWithId);

        $response = array('result' => 'EMPTY');
        if ($parsed && sizeof($parsed) == 2)
        {
            $id = $parsed[0];
            $code = $parsed[1];

            $product_model = new shopProductModel();
            $product = $product_model->getById($id);

            if ($product)
            {
                $response = array('result' => 'OK', 'name' => $product['name'], 'code' => $code);
            }

        }
        $this->response = $response;
    }
    public function checkapiAction()
    {
        $apiKey = waRequest::get('apikey');
        $apid = waRequest::get('apid');

        $this->plugin();

        $response = array('result' => 'ERROR', 'error_text' => 'Incorrect params');
        if ($apid && $apiKey)
        {
            $api = Earnberry::createInstance($apid, $apiKey);
            if ($debugSettings = $this->plugin()->getDebugSettings())
            {
                $api->setApiUrl($debugSettings['api_url']);
            }
            try
            {
                $meta = $api->meta('platform.id');
                if ($meta['id'] == $apid)
                {
                    $response = array('result' => 'OK');
                }
                else
                {
                    $response = array('result' => 'ERROR', 'error_text' => 'ApiId doesnot equals to platform associated with apikey.');
                }
            }
            catch (EarnberryException $e)
            {
                $response = array('result' => 'ERROR', 'error_text' => $e->getMessage());
            }
        }
        $this->response = $response;
    }
    public function loadordernotificationAction()
    {
        $orderId = waRequest::get('orderId');
        $v = wa()->getView();

        $orderModel = new shopOrderModel();
        $error = false;


        if ($orderId && ($orderData = $orderModel->getOrder($orderId)))
        {

            $orderParams = $this->plugin()->getOrderTransactionId($orderId);
            if ($orderParams)
            {
                $earnberryOrderId = $orderParams['earnberry_order_id'];

                if ($earnberryOrderId)
                {
                    $api = $this->plugin()->getEarnberryApi();
                    try
                    {
                        $conversionInfo = $api->conversionInfo($earnberryOrderId);
                        $tplPath = $this->plugin()->getTemplatePath();
                        $v->assign('order_data', $orderData);

                        $calculatedOrder = $this->plugin()->calculateOrder($orderData);


                        $v->assign('order_gp', $calculatedOrder['calculated']['grossprofit']);
                        $v->assign('order_income', $calculatedOrder['calculated']['income']);

                        $v->assign('earnberry_conversion', $conversionInfo);
                        $html = $v->fetch($tplPath.'_notificationAjaxForm.smarty.html');
                        $this->response = array(
                            'html' => $html
                        );
                    }
                    catch (EarnberryException $e)
                    {
                        $error = $e->getMessage();
                    }

                }
            }

        }

        if ($error)
        {

            $html = $error;
            $this->response = array(
                'html' => $html
            );
        }
    }
    public function notificateordertotalAction()
    {
        $response = array('result' => 'ERROR', 'error_text' => 'Incorrect request method');
        if (waRequest::getMethod() == 'post')
        {
            //csrf check
            $response = array('result' => 'ERROR', 'error_text' => 'CSRF check failed');
            if (trim(waRequest::cookie('_csrf', 2)) == trim(waRequest::post('_csrf', 1)))
            {
                $earnberryData = waRequest::post('earnberry', array());
                $response = array('result' => 'ERROR', 'error_text' => 'Incorrect request data');
                if (is_array($earnberryData) && isset($earnberryData['transaction_id']) && isset($earnberryData['notified_total']))
                {
                    $transactionId = $earnberryData['transaction_id'];
                    $notifiedTotal = $earnberryData['notified_total'];
                    $notifiedGp = $earnberryData['notified_grossprofit'];

                    if ($transactionId)
                    {
                        try
                        {
                            $api = $this->plugin()->getEarnberryApi();
                            $res = $api->conversionUpdateTotal($transactionId, is_numeric($notifiedTotal) ?  $notifiedTotal : false, is_numeric($notifiedGp) ? $notifiedGp : false);
                            $response =  array('result' => 'OK');
                        }
                        catch (EarnberryException $e)
                        {
                            $response = array('result' => 'ERROR', 'error_text' => $e->getMessage());
                        }
                    }
                }
            }
        }
        $this->response = $response;
    }
    public function neworderformAction()
    {
        $v = wa()->getView();
        //$v->clearAllAssign();
        $settings = $this->plugin()->getEarnberrySettings();

        $tplPath = $this->plugin()->getTemplatePath();
        $data = false;
        if ($transactionId = waRequest::get('transactionId'))
        {
            $api = $this->plugin()->getEarnberryApi();
            try
            {
                $data = $api->conversionInfo($transactionId);
            }
            catch (EarnberryException $e)
            {
                $data = false;
            }
        }
        $v->assign('earnberry_settings', $settings);
        $v->assign('transactionData', $data);
        $v->assign('markers', explode(',', $settings['markers']));

        $html = $v->fetch($tplPath.'_transactionForm.smarty.html');
        $this->response = array(
            'html' => $html
        );
    }
    public function sendnewtransactionAction()
    {
        $response = array('result' => 'ERROR', 'error_text' => 'Incorrect request method');
        if (waRequest::getMethod() == 'post')
        {

            $this->plugin()->getEarnberryApi();
            // _revenue or _marge_percent
            $earnberrySettings = $this->plugin()->getEarnberrySettings();
            $settingMargePercent = $earnberrySettings['revenue_marge_percent'];
            $settingPriceMarginPercent = $earnberrySettings['revenue_price_margin'];
            $settingPrice = $earnberrySettings['revenue_calc_type'];
            $data = waRequest::post();

            $response = array('result' => 'ERROR', 'error_text' => 'Incorrect params');
            if (isset($data['earnberry']) && isset($data['product']))
            {
                //csrf check
                $response = array('result' => 'ERROR', 'error_text' => 'CSRF check failed');
                if (trim(waRequest::cookie('_csrf', 2)) == trim(waRequest::post('_csrf', 1)))
                {

                    $products = $data['product']['add'];
                    $skus = $data['sku']['add'];
                    $quantities = $data['quantity']['add'];
                    $prices = $data['price']['add'];
                    $discount = $data['discount'];

                    $transactionCurrency = wa()->getConfig()->getCurrency(false);

                    $transactionProducts = array();

                    $total = 0;
                    $grossprofit = 0;

                    foreach ($products as $key => $productId)
                    {
                        $skuId = $skus[$key];
                        $transactionProduct = array(
                            'sku' => $skuId,
                            'price' => $prices[$key],
                            'quantity' => $quantities[$key]['product'],
                            'category' => '',

                        );
                        if (is_numeric($skuId))
                        {
                            if (array_key_exists($skuId, $transactionProducts))
                            {
                                //only unique skus should be provided in GA transaction code;
                                // https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce?hl=ru#Guidelines
                                $transactionProducts[$skuId]['quantity'] += intval($transactionProduct['quantity']);
                                continue;
                            }
                            $skuModel = new shopProductSkusModel();
                            $sku = $skuModel->getById($skuId);
                            $productModel = new shopProductModel();
                            $product = $productModel->getById($productId);

                            $categoryModel = new shopCategoryModel();
                            if ($product['category_id'] && ($category = $categoryModel->getById($product['category_id'])))
                            {
                                $transactionProduct['category'] = $category['name'];
                            }

                            $productName = $product['name'].' '.$sku['name'];
                            $transactionProduct['sku'] = $skuId.'|'.$sku['sku'];
                            $transactionProduct['name'] = $productName;

                            $productCurrency = $product['currency'];

                            if ($transactionCurrency != $productCurrency)
                            {
                                $sku['price'] = shop_currency($sku['price'], $productCurrency, null, false);
                                $sku['purchase_price'] = shop_currency($sku['purchase_price'], $productCurrency, null, false);
                            }
                            $transactionProduct['price'] = $sku['price'];
                            if ($settingPrice == '_revenue')
                            {
                                $priceForTotal = floatval($sku['price']) - floatval($sku['purchase_price']);
                            }
                            elseif ($settingPrice == '_marge_percent')
                            {
                                $priceForTotal = floatval($sku['price']) * $settingMargePercent / 100;
                            }
                            elseif ($settingPrice == '_price_margin')
                            {
                                $priceForTotal = floatval($sku['price']) - (floatval($sku['price']) / (1 + $settingPriceMarginPercent/100));
                            }
                            $transactionProduct['price_for_total'] = $priceForTotal;
                            $transactionProducts[$skuId] = $transactionProduct;
                        }
                    }
                    if (is_numeric($discount) && $discount > 0)
                    {
                        $total = $total - $discount;
                        $grossprofit = $grossprofit - $discount;
                    }
                    $earnberryData = $data['earnberry'];
                    $t = new EarnberryTransaction();

                    $t->setCurrency($transactionCurrency);
                    $t->setMarker($earnberryData['marker']);
                    $t->setTrackingId($earnberryData['trackingId']);

                    foreach($transactionProducts as $tp)
                    {
                        $t->addProduct($tp['sku'], $tp['name'], $tp['category'],$tp['price'], $tp['quantity']);
                        $total += ($tp['price_for_total'] * $tp['quantity']);
                        $grossprofit += ($tp['price'] * $tp['quantity']);
                    }
                    $t->setTotal($total);
                    $t->setGrossProfit($grossprofit);

                    $response = array('result' => 'ERROR');
                    try {
                        $res = $t->send();
                        if ($res && $res['result'] == 'OK')
                        {
                            $conversionId = $res['orderId'];
                            $response['result'] = 'OK';
                            $response['id'] = $conversionId;

                            $params = array(
                                'conversion_id' => $conversionId,
                                'object' => $t,
                                'api' => $this->plugin()->getEarnberryApi()
                            );
                            wa()->event('earnberry_transaction', $params);
                        }
                    }
                    catch (EarnberryException $e)
                    {
                        $response['error_text'] = $e->getMessage();
                    }

                    $this->response = $response;
                }
            }
        }
        $this->response = $response;
    }
    private function plugin()
    {
        static $plugin;
        if (!$plugin) {
            $plugin = wa()->getPlugin('earnberry');
        }
        return $plugin;
    }
}