<?php

class shopKmgtmPlugin extends shopPlugin
{
    private $view_vars = array();
    private $domain_settings = array();

    public function getCurrentDomainSettings($domain = false)
    {
        $domain = $domain ? $domain : wa()->getRouting()->getDomain();
        $this->domain_settings = $this->getSettings($domain);
        $this->domain_settings['ua_id'] = self::retrieveUAid($domain);
        if (!$this->getUAid()) {
            $this->domain_settings['ua_ee'] = false;
        }
        return $this->domain_settings;
    }

    private function isOn()
    {
        return $this->getSettings('enable');
    }

    public function getDefaultDomain()
    {
        return $this->getSettings('default_domain');
    }

    private function getRefundButtonsIds()
    {
        return ifset($this->domain_settings['refund_button_ids'], 'delete, refund');
    }

    private function getGTMId()
    {
        $gtm_id = ifset($this->domain_settings['gtm_id']);
        $gtm_use = $this->getGTMuse();
        if ($gtm_use && !empty($gtm_id) && preg_match('/GTM-[a-zA-Z\d]{4,8}/', $gtm_id)) {
            return $gtm_id;
        } else {
            return false;
        }
    }

    private function getGTMee()
    {
        return ifset($this->domain_settings['gtm_ee'], false);
    }

    private function getGTMuse()
    {
        return ifset($this->domain_settings['use_gtm'], false);
    }

    private function getGTMPageType()
    {
        return ifset($this->domain_settings['gtm_type'], false);
    }

    private function getGTMLogged()
    {
        return ifset($this->domain_settings['gtm_logged'], false);
    }

    private function getOneStep()
    {
        $path = wa()->getConfig()->getPluginPath('onestep').'/lib/config/plugin.php';
        return file_exists($path);
    }

    public static function retrieveUAid($domain = false)
    {
        $domain = $domain ? $domain : wa()->getRouting()->getDomain();
        $domain_config_path = wa('site')->getConfig()->getConfigPath('domains/'.$domain.'.php', true, 'site');
        $ua_id = false;
        if (file_exists($domain_config_path)) {
            /**
             * @var $domain_config array
             */
            $domain_config = include($domain_config_path);
            // if google analytics
            if (isset($domain_config['google_analytics']) && is_array($domain_config['google_analytics']) && !empty($domain_config['google_analytics']['universal']) && !empty($domain_config['google_analytics']['code'])) {
                $ua_id = $domain_config['google_analytics']['code'];
            }
        }
        return $ua_id;
    }

    private function getUAid()
    {
        return ifset($this->domain_settings['ua_id'], false);
    }

    private function getUAee()
    {
        return ifset($this->domain_settings['ua_ee'], false);
    }

    private function getDataLayerName()
    {
        $this->domain_settings['datalayer_name'] = !empty($this->domain_settings['datalayer_name']) ? $this->domain_settings['datalayer_name'] : 'dataLayer';
        return $this->domain_settings['datalayer_name'];
    }

    private function getUAAdditionalCode()
    {
        return ifset($this->domain_settings['ua_additional_code'], false);
    }

    /**
     * @param $product shopProduct
     * @return array
     */
    private function getCrossUpSelling($product)
    {
        $this->view_vars['kmgtm']['cross'] = array();
        $this->view_vars['kmgtm']['up'] = array();
        $count = ifset($this->domain_settings['crossselling_count'], 12);
        if ($count) {
            $this->view_vars['kmgtm']['cross'] = $product->crossSelling($count);
        }
        $count = ifset($this->domain_settings['upselling_count'], 12);
        if ($count) {
            $this->view_vars['kmgtm']['up'] = $product->upSelling($count);
        }
    }

    private function getType()
    {
        $types = array();
        if (isset($this->view_vars['category']) && !empty($this->view_vars['category']) &&
            isset($this->view_vars['products']) && !empty($this->view_vars['products'])
        ) {
            $this->view_vars['kmgtm']['page_category'] = 'category';
            $types = array(
                'Ecommerce' => array(
                    'EcPlugin',
                    'Impressions',
                    'EcSend',
                    'ProductClick',
                    'ProductAddRemoveToCart'
                )
            );
        } elseif (isset($this->view_vars['action']) && $this->view_vars['action'] == 'product' &&
            isset($this->view_vars['product']) && $this->view_vars['product'] instanceof shopProduct
        ) {
            $this->view_vars['kmgtm']['page_category'] = 'product';
            $this->getCrossUpSelling($this->view_vars['product']);
            $types = array(
                'Ecommerce' => array(
                    'EcPlugin',
                    'ProductImpressions',
                    'ProductView',
                    'EcSend',
                    'ProductClick',
                    'ProductAddRemoveToCart'
                )
            );
        } elseif (isset($this->view_vars['action']) && $this->view_vars['action'] == 'cart') {
            $this->view_vars['kmgtm']['page_category'] = 'cart';
            $types = array(
                'Ecommerce' => array(
                    'EcPlugin',
                    'ProductAddRemoveToCart'
                )
            );
        } elseif (isset($this->view_vars['action']) && $this->view_vars['action'] == 'default') {
            $this->view_vars['kmgtm']['page_category'] = 'default';
            $types = array(
                'Ecommerce' => array(
                    'EcPlugin',
                    'ProductClick',
                    'ProductAddRemoveToCart'
                )
            );
        } elseif (isset($this->view_vars['action']) && $this->view_vars['action'] == 'checkout' && wa()->getStorage()->get('shop/kmgtm_order')) {
            $this->view_vars['order'] = wa()->getStorage()->get('shop/kmgtm_order');
            wa()->getStorage()->del('shop/kmgtm_order'); // only once
            $this->view_vars['kmgtm']['page_category'] = 'checkout success';
            $types = array(
                'Ecommerce' => array(
                    'AddTransaction',
                )
            );
        } elseif (isset($this->view_vars['checkout_current_step'])) {
            $this->view_vars['kmgtm']['page_category'] = 'checkout';
            $types = $this->getCheckoutType();
        } elseif (isset($this->view_vars['action']) && $this->view_vars['action'] == 'compare') {
            $this->view_vars['kmgtm']['page_category'] = 'compare';
        } elseif ($this->getOneStep() && wa()->getStorage()->get('shop/kmgtm_order')) {
            $cart = wa()->getStorage()->get('shop/kmgtm_order');
            $this->view_vars['items'] = $cart['items'];
            wa()->getStorage()->del('shop/kmgtm_order'); // only once
            $types = array(
                'Ecommerce' => array(
                    'EcPlugin',
                    'CartProducts',
                    'OneStepCheckout'
                )
            );
        }

        if ($this->getGTMPageType()) {
            $types[] = 'PageCategory';
        }
        if ($this->getGTMLogged()) {
            $this->view_vars['kmgtm']['user_status'] = wa()->getUser()->isAuth() ? 'logged' : 'not logged';
            $types[] = 'UserStatus';
        }
        return $types;
    }

    private function getCheckoutType()
    {
        $types = array();
        $steps = isset($this->view_vars['checkout_steps']) ? array_keys($this->view_vars['checkout_steps']) : false;
        if ($steps) {
            $this->view_vars['checkout_step_n'] = array_search($this->view_vars['checkout_current_step'], $steps);
            $this->view_vars['checkout_step_n'] = $this->view_vars['checkout_step_n'] === 0 ? 1 : $this->view_vars['checkout_step_n'] + 1;
        }
        if ($this->view_vars['checkout_current_step'] == 'contactinfo') {
            $types = array(
//                'CartProducts',
                'Ecommerce' => array(
                    'ContactInfoStep',
                )
            );
        } elseif ($this->view_vars['checkout_current_step'] == 'payment') {
            $types = array(
//                'CartProducts',
                'Ecommerce' => array(
                    'PaymentStep',
                )
            );
        } elseif ($this->view_vars['checkout_current_step'] == 'shipping') {
            $types = array(
//                'CartProducts',
                'Ecommerce' => array(
                    'ShippingStep',
                )
            );
        }  elseif ($this->view_vars['checkout_current_step'] == 'confirmation') {
            $types = array(
//                'CartProducts',
                'Ecommerce' => array(
                    'ConfirmationStep',
                )
            );
        }
        if ($steps && $this->view_vars['checkout_step_n'] === 1) {
            array_unshift($types['Ecommerce'], 'CartProducts');
            array_unshift($types['Ecommerce'], 'EcPlugin');
        }
        return $types;
    }

    private function getCode($return_html = false)
    {
        $this->getCurrentDomainSettings();

        $view = wa()->getView();
        $html_gtm = $html_ua = array();
        $this->view_vars = $view->getVars();

        $types = $this->getType();
        // let`s do that sht
        // what to insert?
        // impressions on category page
        $gtm_id = $this->getGTMId();

        if ($gtm_id) {
            $gtm = new shopKmgtmPluginGTM($gtm_id, $this->getGTMee(), $this->getDataLayerName(), $return_html);

            foreach ($types as $type => $methods) {
                if (is_array($methods)) {
                    $method = 'add' . $type;
                    if ($type && method_exists($gtm, $method)) {
                        $html_gtm[] = $gtm->{$method}($this->view_vars, $methods);
                    }
                } else {
                    $method = 'add' . $methods;
                    if ($methods && method_exists($gtm, $method)) {
                        $html_gtm[] = $gtm->{$method}($this->view_vars, $method);
                    }
                }
            }
            if (!$return_html) {
                $html_gtm[] = $gtm->getJsCode();
            }
        }
        if ($this->getUAee()) {
            $ua = new shopKmgtmPluginUA($this->getUAid(), $this->getUAee(), $return_html);
            $ua->addCode($this->getUAAdditionalCode());

            if ($return_html) {
                $html_ua[] = '<script>';
            }
            foreach ($types as $type => $methods) {
                if (is_array($methods)) {
                    $method = 'add' . $type;
                    if ($type && method_exists($ua, $method)) {
                        $html_ua[] = $ua->{$method}($this->view_vars, $methods);
                    }
                } else {
                    $method = 'add' . $methods;
                    if ($methods && method_exists($ua, $method)) {
                        $html_ua[] = $ua->{$method}($this->view_vars, $method);
                    }
                }
            }
            if ($return_html) {
                $html_ua[] = '</script>';
            }
        }

        return $return_html ? array_merge($html_gtm, $html_ua) : $html_gtm;
    }

    public function frontendHeadHandler()
    {
        if ($this->isOn()) {
            return implode(PHP_EOL, $this->getCode());
        }
        return "";
    }

    public function frontendHeaderHandler()
    {
        if (!$this->isOn()) {
            return '';
        }

        $this->getCurrentDomainSettings();

        $gtm_id = $this->getGTMId();
        if ($gtm_id) {
            $gtm = new shopKmgtmPluginGTM($gtm_id);
            return $gtm->getIframeCode();
        } else {
            return "";
        }
    }

    public function frontendFooterHandler()
    {
        if ($this->isOn()) {
            $this->getCurrentDomainSettings();
            $html_ua = $this->getUAee();
            $html_gtm = $this->getGTMId();
            if ($html_ua || $html_gtm) {
                $params = array(
                    'url' => wa()->getRouteUrl('shop/frontend/changePurchase/'),
                    'ua' => $html_ua,
                    'gtm' => $html_gtm
                );
                $view = wa()->getView();
                $view->assign('assets_path', $this->getPluginStaticUrl());
                $view->assign('params', json_encode($params));

                return $view->fetch($this->path . '/templates/footer.html');
            }
        }
        return "";
    }

    public function frontendCheckoutHandler($params)
    {
        if ($this->isOn()/* && !$this->getOneStep()*/) {
            if ($params['step'] !== 'success') {
                return implode(PHP_EOL, $this->getCode(true));
            } else {
                wa()->getStorage()->set('shop/kmgtm_order', wa()->getView()->getVars('order'));
            }
        }
        return "";
    }

    public function frontendCartHandler()
    {
        /*if ($this->isOn() && $this->getOneStep()) {
            wa()->getStorage()->set('shop/kmgtm_order', wa()->getView()->getVars('cart'));
            $html = implode(PHP_EOL, $this->getCode(true));
            return $html;
        }*/
        return "";
    }

    public function backendOrdersHandler()
    {
        if ($this->isOn()) {
            return array(
                'sidebar_section' => '<script src="' . $this->getPluginStaticUrl() . 'js/kmgtm-backend.min.js" type="application/javascript"></script>'
            );
        }
        return array();
    }

    public function backendOrderHandler($order)
    {
        if ($this->isOn()) {
//            $domain = isset($order['params']['storefront']) ? $order['params']['storefront'] : $this->getDefaultDomain();
            $this->getCurrentDomainSettings();
            $html_ua = $this->getUAee();
            if ($html_ua) {
                $refund_ids = $this->getRefundButtonsIds();
                $html = "";
                if ($refund_ids) {
                    $actions = implode('|', array_map('strtolower', array_map('trim', explode(',', $refund_ids))));
                    $order = json_encode($order);
                    $html = <<<HTML
<script>
window.kmgtm = {
    current_domain: true,
    current_action_regexp: new RegExp("action_id=(?:{$actions})&id=(\\\d+)&.*"),
    current_order: {$order}
};
</script>
HTML;
                }
            } else {
                $html = <<<HTML
<script>
window.kmgtm = {
    current_domain: false,
    current_action_regexp: false
};
</script>
HTML;
            }
            return array(
                'info_section' => $html
            );
        }
        return "";
    }

    /**
     * not using for now
     * @param $order
     * @return array
     */
    public function backendOrderEditHandler($order)
    {
        if ($this->isOn()) {
            $domain = isset($order['params']['storefront']) ? $order['params']['storefront'] : $this->getDefaultDomain();
            $order_id = isset($order['id']) ? $order['id'] : 0;
            $this->getCurrentDomainSettings($domain);
            $html_ua = $this->getUAee();
            $html = <<<HTML
<script>
window.kmgtm = {
    current_domain: false,
    current_order_id: false
};
</script>
HTML;
            if ($html_ua) {
                $html = <<<HTML
<script>
window.kmgtm = {
    current_domain: '{$domain}',
    current_order_id: {$order_id}
};
</script>
HTML;
            }

            return array(
                'related' => $html
            );
        }
        return array();
    }
}
