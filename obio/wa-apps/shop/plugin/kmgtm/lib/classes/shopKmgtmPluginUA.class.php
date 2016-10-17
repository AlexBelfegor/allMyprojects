<?php
//todo: add variant == sku
class shopKmgtmPluginUA
{
    CONST measurment_endpoint = 'https://www.google-analytics.com/collect';
    private $measerement_default_data;
    private $id;
    private $cid;
    private $additional_code;
    private $ee;
    private $return_html;
    private $html;
    /**
     * @var $response waResponse
     */
    private $response;

    public function __construct($id, $ee = false, $return_html = false)
    {
        $this->id = $id;
        $this->cid = $this->parseCookie();
        $this->measerement_default_data = array(
            'v' => 1,
            'tid' => $this->id,
            'cid' => $this->cid
        );
        $this->additional_code = array();
        $this->ee = $ee;
        $this->response = wa()->getResponse();
        $this->return_html = $return_html;
    }

    private function parseCookie()
    {
        $cookie_ga = wa()->getRequest()->cookie('_ga');
        if (!empty($cookie_ga) && wa()->getEnv() === 'frontend') {
            list($version, $domainDepth, $cid1, $cid2) = explode('.', $cookie_ga, 4);
            $contents = array('version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1 . '.' . $cid2);
            $cid = $contents['cid'];
        } else {
            $cid = $this->generateCID();
        }
        return $cid;
    }

    private function generateCID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Adding js code to universal analytics (using waResponse) or return string with code
     * @param $str
     * @return bool
     */
    public function addCode($str)
    {
        $this->html = $str;
        if (!$this->return_html) {
            $this->response->addGoogleAnalytics($str);
            return false;
        } else {
            return $this->html;
        }
    }

    public function addEcPlugin()
    {
        $html = "ga('require', 'ec');";
        return $this->addCode($html);
    }

    /**
     * Send Enhanced Ecommerce data with event
     */
    public function addEcSend()
    {
        $html = "ga('send', 'event', 'kmEcommerce', 'send', '', {nonInteraction: true});";
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-impression
     * @param $vars array
     * @return array|bool
     */
    public function addImpressions($vars)
    {
        $impressions = array();
        $i = 1;
        foreach ($vars['products'] as $product) {
            $impressions[] = "ga('ec:addImpression', " . json_encode(array(
                    'name' => $product['name'],
//                    'id' => $product['id'],
                    'price' => $product['price'],
                    'category' => $vars['category']['name'],
                    'list' => $vars['category']['name'],
                    'position' => $i++
                )) . ");";
        }
        if ($impressions) {
            return $this->addCode(implode(PHP_EOL, $impressions));
        }
        return false;
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-detail-view
     * @param $vars array
     * @return array|bool
     */
    public function addProductImpressions($vars)
    {
        $impressions = array();
        if (isset($vars['kmgtm'])) {
            $category_model = new shopCategoryModel();
            foreach (array($vars['kmgtm']['up'], $vars['kmgtm']['cross']) as $related) {
                $i = 1;
                foreach ($related as $product) {
                    $category = $category_model->getById($product['category_id']);
                    $impressions[] = "ga('ec:addImpression', " . json_encode(array(
                            'name' => $product['name'],
//                            'id' => $product['id'],
                            'price' => $product['price'],
                            'category' => $category['name'],
                            'position' => $i++
                        )) . ");";
                }
            }
        }
        if ($impressions) {
            return $this->addCode(implode(PHP_EOL, $impressions));
        }
        return false;
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-detail-view
     * @param $vars array
     * @return array|bool
     */
    public function addProductView($vars)
    {
        /**
         * @var $product shopProduct
         */
        $product = $vars['product'];
        $category_model = new shopCategoryModel();
        $category = $category_model->getById($product->category_id);
        $impressions = array();
        $impressions[] = "ga('ec:addProduct', " . json_encode(array(
                'name' => $product['name'],
//                'id' => $product['id'],
                'price' => floatval($product['price']),
                'category' => $category['name'],
            )) . ");";
        $impressions[] = "ga('ec:setAction', 'detail');";
        if ($impressions) {
            return $this->addCode(implode(PHP_EOL, $impressions));
        }
        return false;
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-click
     */
    public function addProductClick()
    {
        $html = <<<JS
function kmOnProductClickUA(product) {
    ga('ec:addProduct', {
        //'id': product.id,
        'name': product.name || '',
        'category': product.category || '',
        'position': product.position || ''
    });
    ga('ec:setAction', 'click');

    // Send click with an event, then send user to product page.
    ga('send', 'event', 'kmUX', 'click', '', {
        hitCallback: function() {
            document.location = product.url;
        }
    });
}
JS;
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-click
     */
    public function addProductAddRemoveToCart()
    {
        $html = <<<JS
function kmOnProductAddRemoveToCartUA(product) {
    ga('ec:addProduct', {
       'name': product.name,
       'category': product.category,
       'variant': product.variant,
       'price': product.price,
       'quantity': product.quantity
    });
    ga('ec:setAction', product.action);
    ga('send', 'event', 'kmEcommerce', 'cart', product.action + ' cart', '', {nonInteraction: true});
}
JS;
        return $this->addCode($html);
    }

    /**
     * Deletes from GA js array data about standart ecommerce
     */
    public function addClearTransaction()
    {
        $html = <<<JS
function kmDeleteOldGaEcommerceData()
{
    if (ga !== 'undefined' && ga.q !== 'undefined') {
        for(var i = 0; i < ga.q.length; i++) {
            if (ga.q[i].length > 0 && /ecommerce:/.test(ga.q[i][0])) {
                delete(ga.q[i]);
            }
        }
    }
}
kmDeleteOldGaEcommerceData();
JS;
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#transaction
     * @param $vars
     * @return bool
     */
    public function addAddTransaction($vars)
    {
        if (isset($vars['order']) && wa()->getStorage()->get('shop/kmgtm_gtm_success_order_id') != $vars['order']['id']) {
            wa()->getStorage()->set('shop/kmgtm_gtm_success_order_id', $vars['order']['id']);
            /**
             * @var $config shopConfig
             */
            $config = wa('shop')->getConfig();
            $title = $config->getGeneralSettings('name');
            if (!$title) {
                $app = wa()->getAppInfo();
                $title = $app['name'];
            }

            $this->addClearTransaction();
            $this->addEcPlugin();
            $this->addCartProducts($vars['order']['items']);
            $html[] = "ga('ec:setAction', 'purchase', " . json_encode(array(
                    'id' => $vars['order']['id'],
                    'affiliation' => $title,
                    'revenue' => shopKmgtmPluginHelper::formatPrice($vars['order']['total']),
                    'tax' => shopKmgtmPluginHelper::formatPrice($vars['order']['tax']),
                    'shipping' => shopKmgtmPluginHelper::formatPrice($vars['order']['shipping'])
                )) . ");";
            $html[] = "ga('send', 'event', 'kmEcommerce', 'send', 'purchase', {nonInteraction: true})";
            return $this->addCode(implode(PHP_EOL, $html));
        }
        return false;
    }

    public function addCartProducts($items = array())
    {
        $cart = new shopCart();
        if (!empty($items) && isset($items['items'])) {
            $items = $items['items'];
        } else {
            $items = $cart->items(false);
        }
//        $items = !empty($items) ? $items : $cart->items(false);

        $products = array();
        foreach ($items as $item) {
//            $sku = $item['type'] == 'product' ? $item['sku_code'] : '';
            $product = shopKmgtmPluginHelper::getAddRemoveProductData($item);
            $products[] = "ga('ec:addProduct', " . json_encode(array(
                    'name' => $product['name'],
                    'variant' => $product['variant'],
//                    'id' => $item['id'],
                    'price' => $product['price'],
                    'category' => $product['category'],
                    'quantity' => $product['quantity']
                )) . ");";
        }
        if ($products) {
            return $this->addCode(implode(PHP_EOL, $products));
        } else {
            return "";
        }
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#checkout-process
     * @param $vars
     * @return bool
     */
    public function addPaymentStep($vars)
    {
        $html = "";
        $step = "payment";
        if (!empty($vars['checkout_step_n'])) {
            $html = <<<JS
(function(){
    var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
        if (!$(this).find('em.errormsg').length) {
            ga('ec:setAction','checkout', {
                'step': {$vars['checkout_step_n']},
                'option': form.find('input[name="payment_id"]:checked').closest('label').text().trim()
            });
            ga('send', 'event', 'kmEcommerce', '{$step}', '', {nonInteraction: true});
        }
    });
}());
JS;
        }
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#checkout-process
     * @param $vars
     * @return bool
     */
    public function addContactInfoStep($vars)
    {
        $html = "";
        $step = "contactinfo";
        if (!empty($vars['checkout_step_n'])) {
            $html = <<<JS
(function(){
    var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
        if (!$(this).find('em.errormsg').length) {
            ga('ec:setAction','checkout', {
                'step': {$vars['checkout_step_n']}
            });
            ga('send', 'event', 'kmEcommerce', '{$step}', '', {nonInteraction: true});
        }
    });
}());
JS;
        }
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#checkout-process
     * @param $vars
     * @return bool
     */
    public function addShippingStep($vars)
    {
        $html = "";
        $step = "shipping";
        if (!empty($vars['checkout_step_n'])) {
            $html = <<<JS
(function(){
    var form = $('[data-step-id="{$step}"]').closest('form');

    form.on('submit', function(){
        if (!$(this).find('em.errormsg').length) {
            ga('ec:setAction','checkout', {
                'step': {$vars['checkout_step_n']},
                'option': form.find('input[name="shipping_id"]:checked').closest('label').text().trim()
            });
            ga('send', 'event', 'kmEcommerce', '{$step}', '', {nonInteraction: true});
        }
    });
}());
JS;
        }
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#checkout-process
     * @param $vars
     * @return bool
     */
    public function addConfirmationStep($vars)
    {
        $html = "";
        $step = "confirmation";
        if (!empty($vars['checkout_step_n'])) {
            $html = <<<JS
(function(){
    var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
        if (!$(this).find('em.errormsg').length) {
            ga('ec:setAction','checkout', {
                'step': {$vars['checkout_step_n']}
            });
            ga('send', 'event', 'kmEcommerce', '{$step}', '', {nonInteraction: true});
        }
    });
}());
JS;
        }
        return $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#enhancedecom
     * @param $vars
     * @throws waException
     */
    public function addRevert($vars)
    {
        $params = $this->measerement_default_data + array(
                't' => 'event',
                'ec' => 'kmEcommerce',
                'ea' => 'Refund',
                'ni' => 1,
                'pa' => 'refund',
                'ti' => $vars['id'],
            );
        shopKmgtmPluginHTTPTransport::load(self::measurment_endpoint, 'POST', $params);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#enhancedecom
     * @param $order
     * @throws waException
     */
    public function addPurchase($order)
    {
        $params = $this->measerement_default_data + array(
                't' => 'event',
                'ec' => 'kmEcommerce',
                'ea' => 'Transaction',
                'ni' => 1,
                'ti' => $order['id_str'],
                'ta' => $order['params']['storefront'],
                'tr' => shopKmgtmPluginHelper::formatPrice($order['total']),
                'tt' => shopKmgtmPluginHelper::formatPrice($order['tax']),
                'ts' => shopKmgtmPluginHelper::formatPrice($order['shipping']),
                'pa' => 'purchase'
            );
        $i = 0;
        foreach ($order['items'] as $item) {
            $i++;
//            $params['pr' . $i . 'id'] = $item['sku_id'];
            $params['pr' . $i . 'nm'] = $item['sku_code'];
//            $params['pr' . $i . 'ca'] = $item['category'];
//            $params['pr' . $i . 'va'] = $item['category'];
        }
        shopKmgtmPluginHTTPTransport::load(self::measurment_endpoint, 'POST', $params);
    }

    public function addEcommerce($vars, $methods = array())
    {
        $code = array();
        if ($this->ee) {
            foreach ($methods as $m) {
                $method = 'add' . $m;
                if ($method && method_exists($this, $method)) {
                    $code[] = $this->{$method}($vars);
                }
            }
        }
        return $this->addCode(implode(PHP_EOL, $code));
    }



    private function getOneCheckoutCode()
    {
        return <<<JS
$('.checkout-step').each(function(){
    steps[$(this).data('step')] = false;
});
var checkoutstepHandler = function(e, all) {
    var step_no = 0,
        step_wrapper_current = $(this).closest('.checkout-step'),
        step_name_current = step_name_current || step_wrapper_current.data('step');

    all = all || false;

    //$(this).off('.kmgtm');
    for (var step in steps) {
        if (steps.hasOwnProperty(step)) {
            step_no++;
            var stp_wrpr = $('.checkout-step[data-step="' + step + '"]'),
                option = stp_wrpr.find('input[name="' + step + '_id"]:checked').closest('label').text().trim();
            if (!stp_wrpr.find('em.errormsg').length) {
            //if (!steps[step] && !stop_next) {
            //    steps[step] = true;
                sendOneStepCheckoutStep(step_no, step, option);
            //}
                if (!all && step === step_name_current) {
                    break;
                }
            }
        }
    }
};
$('body')
    .on('change.kmgtm', '.checkout-step input[type="checkbox"], .checkout-step input[type="radio"]', checkoutstepHandler)
    .on('focus.kmgtm', '.checkout-step input[type="text"], .checkout-step textarea', checkoutstepHandler)
    .on('submit.kmgtm', 'form.checkout-form', function (){
        setTimeout(function(){
            checkoutstepHandler(true);
        }, 100);
    });
JS;
    }

    public function addOneStepCheckout()
    {
        return <<<JS
(function(){
    var steps = {};
    $(document).ready(function(){
        var sendOneStepCheckoutStep = function(step, step_name, option){
            ga('ec:setAction','checkout', {
                'step': step,
                'option': option
            });
            ga('send', 'event', 'kmEcommerce', step_name, '', { nonInteraction: true });
            console.info({
                'step': step,
                'option': option
            });
        };
        {$this->getOneCheckoutCode()}
    });
}());
JS;

    }
}