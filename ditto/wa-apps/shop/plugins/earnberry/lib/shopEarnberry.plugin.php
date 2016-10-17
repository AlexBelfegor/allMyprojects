<?php


//error_reporting(E_ALL);
//ini_set('display_errors', true);

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Earnberry'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Earnberry.php';

class shopEarnberryPlugin extends shopPlugin
{
    protected $earnberryApi = false;
    protected $earnberrySettings = array();
    public function addBackendJs()
    {
        if ($this->isOperative())
        {

            return array(
                    'core_li' => '<script type="text/javascript">
                                    earnberry_ajax_actions = {
                                        neworderform: "'.$this->buildUrl(array('action' => 'neworderform')).'",
                                        sendnewtransaction: "'.$this->buildUrl(array('action' => 'sendnewtransaction')).'",
                                        loadordernotification: "'.$this->buildUrl(array('action' => 'loadordernotification')).'",
                                        notificateordertotal: "'.$this->buildUrl(array('action' => 'notificateordertotal')).'",
                                        checkapi: "'.$this->buildUrl(array('action' => 'checkapi')).'",
                                        autocompletebycode: "'.$this->buildUrl(array('action' => 'autocompletebycode')).'"
                                    }
                                  </script>
                                  <script type="text/javascript" src="'.$this->getPluginStaticUrl().'js/earnberry_backend.js?v='.time().'"></script>
                                  <script type="text/javascript" src="/wa-content/js/jquery-ui/jquery.ui.widget.min.js"></script>
                                  <script type="text/javascript" src="/wa-content/js/jquery-ui/jquery.ui.autocomplete.min.js"></script>
                                  '
            );
        }
    }
    public function addFrontendJs()
    {
        if ($this->isOperative())
        {          
            $settings = $this->getEarnberrySettings();
            $analyticsType =  intval($settings['universal_analytics']) > 0 ? 'analytics.universal' : 'analytics.classic';
            $jsCodeEarnberryInit = "
                <script type='text/javascript'>
                    _earnberry = window._earnberry || [];
                    _earnberry.push(['init', '".$settings['api_id']."', '".$analyticsType."']);
                    _earnberry.push(['exec', 'start', false]);
                </script>

            ";
            if ($debugSettings = $this->getDebugSettings())
            {
                $jsCodeEarnberryInit .= "<script type='text/javascript'>_earnberry.push(['set', 'hubUrl', '{$debugSettings['hub_url']}']);</script>";
            }

            $storage = waSystem::getInstance()->getStorage();
            if ($js = $storage->read('earnberry_otc'))
            {
                $storage->del('earnberry_otc');
                $jsCodeEarnberryInit .= "<script type='text/javascript'>{$js}</script>";
            }
            return $jsCodeEarnberryInit;
        }
    }
    public function addFrontendGaJs()
    {
       if ($this->isOperative())
       {
           $jsUrl = 'https://earnberry.net/js/';
           if ($debugSettings = $this->getDebugSettings())
           {
               $jsUrl = $debugSettings['js_url'];
           }
           $jsCodeGAEarnberry =  "
                <script type='text/javascript' src='{$jsUrl}earnberry.js?v=".time()."'></script>
           ";
            return $jsCodeGAEarnberry;
       }
    }
    public function addNotificationForm($order)
    {
        if ($this->isOperative())
        {
            $orderId = $order['id'];
            $orderParams = $this->getOrderTransactionId($orderId);
            if ($orderParams)
            {
                $earnberryOrderId = $orderParams['earnberry_order_id'];
                $api = $this->getEarnberryApi();
                try {
                    $v = wa()->getView();
                    $conversionInfo = $api->conversionInfo($earnberryOrderId);
                    $v->assign('order_data', $order);
                    $v->assign('earnberry_conversion', $conversionInfo);
                    
                    $settings = $this->getEarnberrySettings();
                    $v->assign('lookup_url', 'https://earnberry.net/platform/lookup/'.$settings['api_id'].'?tid='.$earnberryOrderId);

                    $html = $v->fetch($this->getTemplatePath().'_notificationForm.smarty.html');
                    return array(
                        'action_link' => $html
                    );
                }
                catch (EarnberryException $e)
                {
			var_dump($e->getMessage());
                }

            }
        }
    }
    public function orderCreate($data)
    {
        $env = wa()->getEnv();

        if (!$this->isOperative())
        {
            return true;
        }

        $orderId = $data['order_id'];
        $orderModel = new shopOrderModel();
        $orderData = $orderModel->getOrder($orderId);

        if ($orderData)
        {
            $orderParams = $orderData['params'];
            if ('frontend' == $env)
            {
                $api = $this->getEarnberryApi();
                $trackingId = $api->getTrackingId();
                $this->_sendNewTransactionForOrder($orderId, $orderData, $trackingId);
            }
            elseif ('backend' == $env)
            {
                $earnberryRqData = waRequest::post('earnberry');
                if (isset($orderParams['earnberry_order_id']))
                {

                }
                elseif ($earnberryRqData && !isset($earnberryRqData['order_id']))
                {
                    $earnberrySettings = $this->getEarnberrySettings();
                    $markers = isset($earnberrySettings['markers']) ? explode(',', $earnberrySettings['markers']) : array('tel', 'order');
                    if (isset($earnberryRqData['marker']) && is_string($earnberryRqData['marker']))
                    {
                        $marker = $earnberryRqData['marker'];
                    }
                    else
                    {
                        $marker = $markers[0];
                    }
                    if (isset($earnberryRqData['trackingId']) && strlen(trim($earnberryRqData['trackingId'])) > 0)
                    {
                        $this->_sendNewTransactionForOrder($orderId, $orderData, $earnberryRqData['trackingId'], 'earnberry_be_order_', $marker);
                    }
                    elseif (isset($earnberrySettings['celibate_transactions']) && intval($earnberrySettings['celibate_transactions']) == 1)
                    {
                        $this->_sendNewTransactionForOrder($orderId, $orderData, false, 'earnberry_be_order_', $marker);
                    }
                        
                }
                else
                {
                    
                    if ($earnberryRqData && isset($earnberryRqData['order_id']))
                    {
                        $newParam = array('earnberry_order_id' => $earnberryRqData['order_id']);
                        $orderParamsModel = new shopOrderParamsModel();
                        $orderParamsModel->set($orderId, $newParam, false);

                    }
                }

            }
        }
    }
    protected function _sendNewTransactionForOrder($orderId, $orderData, $trackingId, $transactionPrefix = 'earnberry_wa_fe_', $marker = 'order')
    {
        $api = $this->getEarnberryApi();

        $calculatedOrderData = $this->calculateOrder($orderData);
        $transactionProducts = $calculatedOrderData['calculated']['items'];
        $total = $calculatedOrderData['calculated']['income'];
        $grossprofit = $calculatedOrderData['calculated']['grossprofit'];

        $eventData = array($orderId, $orderData, $trackingId, $transactionPrefix, $marker, $this->getEarnberrySettings(), $calculatedOrderData);
        if (($process = wa()->event('earnberry_wrap_transaction', $eventData)) && ($trp = array_shift($process)))
        {
            $t = $trp;
        }
        else
        {
            $t = new EarnberryTransaction();
            $t->setCurrency($orderData['currency']);
            $t->setMarker($marker);

            $t->setTrackingId($trackingId);
            $t->setOrderId($transactionPrefix . $orderId);

            foreach ($transactionProducts as $tp)
            {
                $t->addProduct($tp['sku'], $tp['name'], $tp['category'], $tp['price'], $tp['quantity']);
            }
            $t->setTotal($total);
            $t->setGrossProfit($grossprofit);
        }

        try
        {
            $res = $t->send();
            if ($res && $res['result'] == 'OK')
            {
                $conversionId = $res['orderId'];
                $params = array(
                    'conversion_id' => $conversionId,
                    'object' => $t,
                    'api' => $api
                );
                wa()->event('earnberry_transaction', $params);

                $newParam = array('earnberry_order_id' => $conversionId);
                $orderParamsModel = new shopOrderParamsModel();
                $orderParamsModel->set($orderId, $newParam, false);
            }
        } 
        catch (EarnberryException $e)
        {
            if ($e->getIssueType() == EarnberryException::ISSUE_TYPE_NETWORK_PROBLEM)
            {
                //$storage = waSystem::getInstance()->getStorage();
                //$storage->write('earnberry_otc', $t->generate());
            }
        }
    }

    public function onOrderRemove($o)
    {
        if (!$this->isOperative())
        {
            return true;
        }
        $orderId = $o['order_id'];
        if ($orderParams = $this->getOrderTransactionId($orderId))
        {
            $transactionId = $orderParams['earnberry_order_id'];
            $api = $this->getEarnberryApi();
            try {
                $api->conversionUpdateTotal($transactionId, 0, 0);
            }
            catch (EarnberryException $e)
            {
               
            }
        }
    }
    public function onOrderComplete($o)
    {
        if (!$this->isOperative())
        {
            return true;
        }
        $orderId = $o['order_id'];
        if ($orderParams = $this->getOrderTransactionId($orderId))
        {
            $transactionId = $orderParams['earnberry_order_id'];
            $api = $this->getEarnberryApi();
            try {
                $orderModel = new shopOrderModel();
                $orderData = $orderModel->getOrder($orderId);
                $calculatedOrder = $this->calculateOrder($orderData);
                $api->conversionUpdateTotal($transactionId, $calculatedOrder['calculated']['income'], $calculatedOrder['calculated']['grossprofit']);
            }
            catch (EarnberryException $e)
            {

            }
        }
    }
    public function getTemplatePath()
    {
        return $this->path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
    }
    public function buildUrl($params)
    {
        $params = array_merge(array('plugin' => 'earnberry'), $params ? $params : array());
        return wa()->getAppUrl().'?'.http_build_query($params);
    }
    public function getEarnberryApi()
    {
        if (!$this->earnberryApi)
        {
            $settings = $this->getEarnberrySettings();
            $api = Earnberry::createInstance($settings['api_id'], $settings['api_key']);
            Earnberry::setEncoding('UTF-8');
            if ($debugSettings = $this->getDebugSettings())
            {
                $api->setApiUrl($debugSettings['api_url']);
            }
        }

        return Earnberry::getInstance();
    }
    public function getOrderWithTransactionId($orderId)
    {

    }
    public function getOrderTransactionId($orderId)
    {
        $orderParamsModel = new shopOrderParamsModel();
        $orderParams = $orderParamsModel->get($orderId);
        if ($orderParams && isset($orderParams['earnberry_order_id']))
        {
            return array('params_all' => $orderParams, 'earnberry_order_id' => $orderParams['earnberry_order_id']);
        }
        return false;
    }
    public function getEarnberrySettings($force = false)
    {
        $defaultSettings = array(
            'api_key' => '',
            'api_id' => '',
            'revenue_calc_type' => '_revenue',
            'revenue_marge_percent' => 100,
            'markers' => 'order,tel',
            'universal_analytics' => '0',
            'celibate_transactions' => '0'
        );

        if (!$this->earnberrySettings || $force)
        {
            $app_settings_model = new waAppSettingsModel();
            $savedSettings = $app_settings_model->get(array('shop', 'earnberry'), null, array());
        }
        else
        {
            $savedSettings = $this->earnberrySettings;
        }

        return array_merge($defaultSettings, $savedSettings);
    }
    public function isOperative()
    {
        $settings = $this->getEarnberrySettings();
        return strlen($settings['api_key']) && strlen($settings['api_id']);
    }
    public function getDebugSettings()
    {
        if (false)
        {
            return array(
                'hub_url' => 'http://eb.dev.ggg.org.ua:8888/cjq',
                'api_url' => 'http://172.16.1.1/',
                'js_url' => 'http://eb.dev.ggg.org.ua:8888/js/'
            );
        }
        return false;
    }
    public function workupOrderItemsForEarnberry($orderItems)
    {
        $earnberrySettings = $this->getEarnberrySettings();
        $settingMargePercent = $earnberrySettings['revenue_marge_percent'];
        $settingPriceMarginPercent = $earnberrySettings['revenue_price_margin'];
        $settingPrice = $earnberrySettings['revenue_calc_type'];

        $transactionProducts = array();
        foreach ($orderItems as $item)
        {
            $transactionProduct = array(
                'sku' => $item['sku_id'].'|'.$item['sku_code'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'category' => '',
            );

            if ($earnberrySettings['universal_analytics'] == 2)
            {
                $evData = array($transactionProduct, $item);
                if ($process = wa()->event('earnberry_workup_product', $evData))
                {
                    $transactionProduct = array_shift($process);
                }
            }

            $productId = $item['product_id'];
            $productModel = new shopProductModel();
            $product = $productModel->getById($productId);

            $categoryModel = new shopCategoryModel();
            if ($product['category_id'] && ($category = $categoryModel->getById($product['category_id'])))
            {
                $transactionProduct['category'] = $category['name'];
            }

            if ('_revenue' == $settingPrice)
            {
                $priceForTotal = floatval($item['price']) - floatval($item['purchase_price']);
            }
            elseif ('_marge_percent' == $settingPrice)
            {
                $priceForTotal = floatval($item['price']) * ($settingMargePercent / 100);
            }
            elseif ('_price_margin' == $settingPrice)
            {
                $priceForTotal = floatval($item['price']) - (floatval($item['price']) / (1 + $settingPriceMarginPercent/100));
            }
            $transactionProduct['price_for_total'] = $priceForTotal;
            $transactionProducts[] = $transactionProduct;
        }
        return $transactionProducts;

    }
    public function calculateOrder($orderData)
    {
        $orderItems = $orderData['items'];
        $total = 0;
        $grossprofit = 0;
        $transactionProducts = $this->workupOrderItemsForEarnberry($orderItems);
        if ($orderData['discount'] > 0)
        {
            $total = $total - $orderData['discount'];
            $grossprofit = $grossprofit - $orderData['discount'];
        }

        foreach($transactionProducts as $tp)
        {
            $total += ($tp['price_for_total'] * $tp['quantity']);
            $grossprofit += ($tp['price'] * $tp['quantity']);
        }
        return array('original' => $orderData, 'calculated' => array('items' => $transactionProducts, 'grossprofit' => round($grossprofit, 2), 'income' => round($total,2)));
    }

}
