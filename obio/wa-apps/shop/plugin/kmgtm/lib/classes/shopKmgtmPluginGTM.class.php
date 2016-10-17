<?php

//todo: add variant == sku
class shopKmgtmPluginGTM
{
    private $id;
    private $data_layer_name;
    private $ee;
    private $return_html;

    public function __construct($id, $ee = false, $dl_name = 'dataLayer', $return_html = false)
    {
        $this->id = $id;
        $this->data_layer_name = $dl_name;
        $this->ee = $ee;
        $this->additional_code = array();
        $this->response = wa()->getResponse();
        $this->return_html = $return_html;
    }

    public function addCode($str)
    {
        $this->additional_code[] = $str;
    }

    public function getJsCode()
    {
        $additional_code = implode(PHP_EOL, $this->additional_code);
        return <<<HTML
<script>
var {$this->data_layer_name} = window.{$this->data_layer_name} || [];
{$additional_code}
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='{$this->data_layer_name}'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','{$this->data_layer_name}','{$this->id}');
</script>
HTML;
    }

    public function getIframeCode()
    {
        return <<<HTML
<noscript><iframe src="//www.googletagmanager.com/ns.html?id={$this->id}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
HTML;
    }

    /**
     * Send Enhanced Ecommerce data with event
     */
    public function addEcSend()
    {
//        window.{$this->data_layer_name} = window.{$this->data_layer_name} || [];
        $html = <<<JS
{$this->data_layer_name}.push({
    'event': 'kmEcommerce'
});
JS;
        $this->addCode($html);
        return $html;
    }

    /**
     * see https://developers.google.com/tag-manager/enhanced-ecommerce#product-impressions
     * @param $vars array
     */
    public function addImpressions($vars)
    {
        /**
         * @var $config shopConfig
         */
        $config = wa('shop')->getConfig();
        $code = "{$this->data_layer_name}.push({'currencyCode':'{$config->getCurrency(false)}','ecommerce':{'impressions':";
        $impressions = array();
        $i = 1;
        foreach ($vars['products'] as $product) {
            $impressions[] = array(
                'name' => $product['name'],
//                'id' => $product['id'],
                'price' => $product['price'],
                'category' => $vars['category']['name'],
                'list' => $vars['category']['name'],
                'position' => $i++
            );
        }
        if ($impressions) {
            $code .= json_encode($impressions);
            $code .= "}});";
            $this->addCode($code);
        }
    }

    /**
     * @param $vars array
     */
    public function addProductImpressions($vars)
    {
        /**
         * @var $config shopConfig
         */
        $config = wa('shop')->getConfig();
        foreach (array($vars['kmgtm']['up'], $vars['kmgtm']['cross']) as $related) {
            $category_model = new shopCategoryModel();
            $code = "{$this->data_layer_name}.push({'currencyCode':'{$config->getCurrency(false)}','ecommerce':{'impressions':";
            $impressions = array();
            $i = 1;
            foreach ($related as $product) {
                $category = $category_model->getById($product['category_id']);
                $impressions[] = array(
                    'name' => $product['name'],
//                    'id' => $product['id'],
                    'price' => $product['price'],
                    'category' => $category['name'],
                    'position' => $i++
                );
            }
            if ($impressions) {
                $code .= json_encode($impressions);
                $code .= "}});";
                $this->addCode($code);
            }
        }
    }

    /**
     * see https://developers.google.com/tag-manager/enhanced-ecommerce#details
     * @param $vars array
     */
    public function addProductView($vars)
    {
        /**
         * @var $product shopProduct
         */
        $product = $vars['product'];
        $category_model = new shopCategoryModel();
        $category = $category_model->getById($product->category_id);
        $code = "{$this->data_layer_name}.push({'ecommerce':{'detail':{'products': [" . json_encode(array(
                'name' => $product['name'],
//                'id' => $product['id'],
                'price' => floatval($product['price']),
                'category' => $category['name'],
            )) . "]}}});";
        $this->addCode($code);
    }


    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-click
     */
    public function addProductClick()
    {
        $html = <<<JS
function kmOnProductClickGTM(product) {
    {$this->data_layer_name}.push({
        'event': 'productClick',
        'ecommerce': {
            'click': {
                'products': [{
                    'name': product.name || '',
                    'price': product.price || undefined,
                    'category': product.category,
                    //'variant': productObj.variant,
                    'position': product.position
                 }]
            }
         },
        'eventCallback': function() {
            document.location = product.url
        }
  });
}
JS;
        $this->addCode($html);
    }

    /**
     * see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-click
     */
    public function addProductAddRemoveToCart()
    {
        /**
         * @var $config shopConfig
         */
        $config = wa('shop')->getConfig();
        $html = <<<JS
function kmOnProductAddRemoveToCartGTM(product) {
    var product_data = {
                'products': [{
                    'name': product.name,
                    'category': product.category,
                    'variant': product.variant,
                    'price': product.price,
                    'quantity': product.quantity
                 }]
            };
    var data = {};
    if (product.action === 'add') {
        data = {
            'event': 'addToCart',
            'ecommerce': {
                'currencyCode': '{$config->getCurrency(false)}',
                'add': product_data
            }
        };

    } else {
        data = {
            'event': 'removeFromCart',
            'ecommerce': {
                'currencyCode': '{$config->getCurrency(false)}',
                'add': product_data
            }
        };
    }
    {$this->data_layer_name}.push(data);
    {$this->addEcSend()}
}
JS;
        $this->addCode($html);
    }

    public function addCartProducts($items = array())
    {
        // hack (no need to add this data separatly (only with checkout step data)
        if (empty($items) || isset($items['items']) || isset($items['SCRIPT_NAME'])) {
            return "";
        }
//        $cart = new shopCart();
//        if (isset($items['items']) && !empty($items)) {
//            $items = $items['items'];
//        }
//        $items = !empty($items) ? $items : $cart->items(false);

        $products = array();
        foreach ($items as $item) {
//            $sku = $item['type'] == 'product' ? $item['sku_code'] : '';
            $product = shopKmgtmPluginHelper::getAddRemoveProductData($item);
            $products[] = array(
                'name' => $product['name'],
                'variant' => $product['variant'],
//                    'id' => $item['id'],
                'price' => $product['price'],
                'category' => $product['category'],
                'quantity' => $product['quantity']
            );
        }
        return $products;
    }

    /**
     * see https://developers.google.com/tag-manager/enhanced-ecommerce#purchases
     * @param $vars
     */
    public function addAddTransaction($vars)
    {
        if (isset($vars['order']) && wa()->getStorage()->get('shop/kmgtm_ua_success_order_id') != $vars['order']['id']) {
            wa()->getStorage()->set('shop/kmgtm_ua_success_order_id', $vars['order']['id']);
            /**
             * @var $config shopConfig
             */
            $config = wa('shop')->getConfig();
            $title = $config->getGeneralSettings('name');
            if (!$title) {
                $app = wa()->getAppInfo();
                $title = $app['name'];
            }

            $products = json_encode($this->addCartProducts($vars['order']['items']));
            $code = "{$this->data_layer_name}.push({'ecommerce':{'purchase': {'actionField': " . json_encode(array(
                    'id' => $vars['order']['id'],
                    'affiliation' => $title,
                    'revenue' => shopKmgtmPluginHelper::formatPrice($vars['order']['total']),
                    'tax' => shopKmgtmPluginHelper::formatPrice($vars['order']['tax']),
                    'shipping' => shopKmgtmPluginHelper::formatPrice($vars['order']['shipping'])
                )) . ", 'products': " . $products . "}}});";
            $this->addCode($code);
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
            $cart = new shopCart();
            $items = isset($vars['items']) ? $vars['items'] : $cart->items(false);
            $products = json_encode($this->addCartProducts($items));
            $html = <<<HTML
<script>
(function(){
var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
        {$this->data_layer_name}.push({
            'event': 'checkout',
            'ecommerce': {
              'checkout': {
                'actionField': {
                    'step': {$vars['checkout_step_n']}},
                    'option': form.find('input[name="payment_id"]:checked').closest('label').text().trim()
                },
                'products': {$products}
            }
        });
        {$this->data_layer_name}.push({
            'event': 'kmEcommerce',
            'ecommerceStep': '{$step}'
        });
    });
}());
</script>
HTML;
        }
        return $html;
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
            $cart = new shopCart();
            $items = isset($vars['items']) ? $vars['items'] : $cart->items(false);
            $products = json_encode($this->addCartProducts($items));
            $html = <<<HTML
<script>
(function(){
var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
        {$this->data_layer_name}.push({
            'event': 'checkout',
            'ecommerce': {
              'checkout': {
                'actionField': {
                    'step': {$vars['checkout_step_n']}},
                },
                'products': {$products}
            }
        });
        {$this->data_layer_name}.push({
            'event': 'kmEcommerce',
            'ecommerceStep': '{$step}'
        });
    });
}());
</script>
HTML;
        }
        return $html;
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
            $cart = new shopCart();
            $items = isset($vars['items']) ? $vars['items'] : $cart->items(false);
            $products = json_encode($this->addCartProducts($items));
            $html = <<<HTML
<script>
(function(){
var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
    debugger;
        {$this->data_layer_name}.push({
            'event': 'checkout',
            'ecommerce': {
              'checkout': {
                'actionField': {
                    'step': {$vars['checkout_step_n']}},
                    'option': form.find('input[name="shipping_id"]:checked').closest('label').text().trim()
                },
                'products': {$products}
            }
        });
        {$this->data_layer_name}.push({
            'event': 'kmEcommerce',
            'ecommerceStep': '{$step}'
        });
    });
}());
</script>
HTML;
        }
        return $html;
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
            $cart = new shopCart();
            $items = isset($vars['items']) ? $vars['items'] : $cart->items(false);
            $products = json_encode($this->addCartProducts($items));
            $html = <<<HTML
<script>
(function(){
var form = $('[data-step-id="{$step}"]').closest('form');
    form.on('submit', function(){
        {$this->data_layer_name}.push({
            'event': 'checkout',
            'ecommerce': {
              'checkout': {
                'actionField': {
                    'step': {$vars['checkout_step_n']}},
                },
                'products': {$products}
            }
        });
        {$this->data_layer_name}.push({
            'event': 'kmEcommerce',
            'ecommerceStep': '{$step}'
        });
    });
}());
</script>
HTML;
        }
        return $html;
    }

    public function addEcommerce($vars, $methods = array())
    {
        if ($this->ee && is_array($methods)) {
            $code = array();
            foreach ($methods as $m) {
                $method = 'add' . $m;
                if ($method && method_exists($this, $method)) {
                    $code[] = $this->{$method}($vars);
                }
            }
            if ($this->return_html) {
                return implode(PHP_EOL, $code);
            }
        }
        return '';
    }

    public function addPageCategory($vars)
    {
        if (isset($vars['kmgtm']['page_category']) && !empty($vars['kmgtm']['page_category'])) {
            $category = htmlspecialchars($vars['kmgtm']['page_category']);
            $code = <<<JS
{$this->data_layer_name}.push({'page_category':'{$category}'});
JS;
            $this->addCode($code);
        }
    }

    public function addUserStatus($vars)
    {
        if (isset($vars['kmgtm']['user_status']) && !empty($vars['kmgtm']['user_status'])) {
            $code = <<<JS
{$this->data_layer_name}.push({'user_status':'{$vars['kmgtm']['user_status']}'});
JS;
            $this->addCode($code);
        }
    }

    private function getOneCheckoutCode()
    {
        return <<<JS
$('.checkout-step').each(function(){
    steps[$(this).data('step')] = false;
});
var checkoutstepHandler = function() {
    var step_no = 0,
        step_wrapper_current = $(this).closest('.checkout-step'),
        step_name_current = step_wrapper_current.data('step');

    step_wrapper_current.find('input').off('.kmgtmgtm'); // only once per one payment/shippment
    for (var step in steps) {
        if (steps.hasOwnProperty(step)) {
            step_no++;
            var option = $('.checkout-step[data-step="' + step + '"]').find('input[name="' + step + '_id"]:checked').closest('label').text().trim();
            //if (!steps[step] && !stop_next) {
            //    steps[step] = true;
            sendOneStepCheckoutStep(step_no, step, option);
            //}
            if (step === step_name_current) {
                break;
            }
        }
    }
};
var bindtoInputs = function() {
    $('body').on('focus.kmgtmgtm', '.checkout-step input', checkoutstepHandler);
};
$('body')
    .on('change.kmgtmgtm', '.checkout-step :radio', function() {
        checkoutstepHandler.call(this);
        bindtoInputs();
    })
    .on('submit.kmgtm', 'form.checkout-form', function (){
        setTimeout(function(){
            checkoutstepHandler(true);
        }, 100);
    });
bindtoInputs();
JS;
    }

    public function addOneStepCheckout($vars)
    {
        $cart = new shopCart();
        $items = isset($vars['items']) ? $vars['items'] : $cart->items(false);
        $products = json_encode($this->addCartProducts($items));

        return <<<HTML
<script>
(function(){
    var steps = {};
    $(document).ready(function(){
        var sendOneStepCheckoutStep = function(step, step_name, option){
            {$this->data_layer_name}.push({
                'event': 'checkout',
                'ecommerce': {
                  'checkout': {
                    'actionField': {
                        'step': step,
                        'option': option
                    },
                    'products': {$products}
                  }
                }
            });
            {$this->data_layer_name}.push({
                'event': 'kmEcommerce',
                'ecommerceStep': step_name
            });
            console.info('checkout', step, option);
        };
        {$this->getOneCheckoutCode()}
    });
}());
</script>
HTML;

    }
}