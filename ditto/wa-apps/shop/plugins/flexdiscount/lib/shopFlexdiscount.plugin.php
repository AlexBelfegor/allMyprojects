<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopFlexdiscountPlugin extends shopPlugin
{

    // Купон, содержащий сумму скидки.
    private static $coupon;
    // Сумма бонусов
    private static $affiliate;
    // Информация о товаре
    private static $product;
    // Информация о валютах
    private static $currency;

    public function frontendHead($e)
    {
        if (shopDiscounts::isEnabled('flexdiscount')) {

            // Получаем настройки
            $settings = shopFlexdiscountHelper::getSettings();

            // Если у нас имеется информация о товаре, то добавим ее в JS объект
            if (self::$product) {
                $product = self::$product;
                if ($product->sku_type == shopProductModel::SKU_TYPE_SELECTABLE) {
                    $features_selectable = $product->features_selectable;

                    $product_features_model = new shopProductFeaturesModel();
                    $sku_features = $product_features_model->getSkuFeatures($product->id);

                    $sku_selectable = array();
                    foreach ($sku_features as $sku_id => $sf) {
                        if (!isset($product->skus[$sku_id])) {
                            continue;
                        }
                        $sku_f = "";
                        foreach ($features_selectable as $f_id => $f) {
                            if (isset($sf[$f_id])) {
                                $sku_f .= $f_id . ":" . $sf[$f_id] . ";";
                            }
                        }
                        $sku_selectable[$sku_f] = array(
                            'id' => $sku_id,
                        );
                    }
                }
            }
            $currency = waCurrency::getInfo(wa()->getConfig()->getCurrency(false));
            $locale = waLocale::getInfo(wa()->getLocale());
            self::$currency = array(
                'code' => $currency['code'],
                'sign' => $currency['sign'],
                'sign_html' => !empty($currency['sign_html']) ? $currency['sign_html'] : $currency['sign'],
                'sign_position' => isset($currency['sign_position']) ? $currency['sign_position'] : 1,
                'sign_delim' => isset($currency['sign_delim']) ? $currency['sign_delim'] : ' ',
                'decimal_point' => $locale['decimal_point'],
                'frac_digits' => $locale['frac_digits'],
                'thousands_sep' => $locale['thousands_sep'],
            );

            // Урл для проверки купона
            $url = wa()->getRouteUrl('shop/frontend/couponAdd');
            // Урл для обновления скидки
            $refresh_url = wa()->getRouteUrl('shop/frontend/updateDiscount');
            return "<link rel='stylesheet' href='" . wa()->getAppStaticUrl('shop') . "plugins/flexdiscount/css/flexdiscountFrontend.css" . "'>
                <script type='text/javascript' src='" . wa()->getAppStaticUrl('shop') . "plugins/flexdiscount/js/flexdiscountFrontend.js'></script>
                    <script type='text/javascript'>
                    $(function() {
                        $.flexdiscountFrontend.init({ 
                            url: '" . $url . "',
                            refreshUrl: '" . $refresh_url . "',
                            ruble: '" . $settings['ruble'] . "'
                            " . (!empty($sku_selectable) ? ", features: " . json_encode($sku_selectable) : "") . "    
                            " . (!empty(self::$currency) ? ", currency: " . json_encode(self::$currency) : "") . "    
                        });
                    });
                </script>";
        }
    }

    public function frontendProduct($product)
    {
        self::$product = $product;

        if (shopDiscounts::isEnabled('flexdiscount')) {
            $html = '';
            // Получаем настройки
            $settings = shopFlexdiscountHelper::getSettings();
            // Вывод цены со скидкой
            if (!empty($settings['enable_price_output'])) {
                $html .= '<div style="margin: 10px 0;">Цена со скидкой <span class="price">' . shopFlexdiscountPluginHelper::price($product) . '</span></div>';
            }
            // Вывод действующих скидок 
            if (!empty($settings['flexdiscount_product_discounts']['value'])) {
                $html .= shopFlexdiscountPluginHelper::getProductDiscounts($product, !empty($settings['flexdiscount_product_discounts']['value']) ? $settings['flexdiscount_product_discounts']['type'] : '');
            }
            // Вывод доступных скидок
            if (!empty($settings['flexdiscount_avail_discounts']['value'])) {
                $html .= shopFlexdiscountPluginHelper::getAvailibleDiscounts($product, !empty($settings['flexdiscount_avail_discounts']['value']) ? $settings['flexdiscount_avail_discounts']['type'] : '');
            }
            $output = array();
            $output['cart'] = $html;
            $output['menu'] = '';
            $output['block_aux'] = '';
            $output['block'] = '';
            return $output;
        }
    }

    public function frontendCart()
    {
        $html = '';
        if (shopDiscounts::isEnabled('flexdiscount')) {
            // Получаем настройки
            $settings = shopFlexdiscountHelper::getSettings();
            // Вывод формы для ввода купонов 
            if (!empty($settings['enable_frontend_cart_hook'])) {
                $html .= shopFlexdiscountHelper::getBlock('flexdiscount.form');
            }
            // Вывод примененных скидок
            if (!empty($settings['enable_flexdiscount_discounts'])) {
                $html .= shopFlexdiscountPluginHelper::getUserDiscounts();
            }
        }
        return $html;
    }

    public function orderCalculateDiscount($params)
    {
        $workflow = array(
            "discount" => 0,
            "affiliate" => 0,
            "discount_name" => array()
        );
        // Если скидка включена
        if (shopDiscounts::isEnabled('flexdiscount') && !empty($params['order']['items'])) {
            // Выполняем обработку скидок
            $workflow = shopFlexdiscountWorkflow::process($params, true);
            // Увеличиваем значение использования купона
            if ($workflow['coupon_used'] && $workflow['coupon_used']['coupon']) {
                $cm = new shopFlexdiscountCouponPluginModel();
                $cm->useOne($workflow['coupon_used']['coupon']);
                self::$coupon = $workflow['coupon_used'];
            }
        }
        // Записываем действующие скидки в массив
        wa()->getStorage()->set("flexdiscount-discounts", $workflow['discount_name']);
        // Сохраняем бонусы для будущей обработки
        if (shopAffiliate::isEnabled()) {
            self::$affiliate = $workflow['affiliate'];
        }
        // Если общая скидка больше суммы заказа, то не допускаем ухода цены в минус
        $cart_discount = $params['order']['total'] - $workflow['discount'];
        $workflow['discount'] = $cart_discount > 0 ? $workflow['discount'] : $params['order']['total'];
        // Изменяем сессию с суммой заказа
        // TODO УДАЛИТЬ, ЭТО ВЛИЯЕТ НА ПЛАГИН С БОНУСАМИ
//        $data = wa()->getStorage()->get('shop/cart');
//        $data['total'] = ($cart_discount > 0) ? $cart_discount : 0.0;
//        wa()->getStorage()->set('shop/cart', $data);

        return $workflow['discount'] ? $workflow['discount'] : 0.0;
    }

    public function backendSettingsDiscounts()
    {
        $enabled = shopDiscounts::isEnabled('flexdiscount');
        $type = array(
            "id" => "flexdiscount",
            "name" => "Гибкие скидки",
            "url" => "?plugin=flexdiscount&module=settings",
            "status" => ($enabled ? true : false)
        );
        return array('flexdiscount' => $type);
    }

    public function orderActionCreate($data)
    {
        if (shopDiscounts::isEnabled('flexdiscount')) {
            // Если был использован купон и заказ оформлен, то запоминаем заказ и присваиваем его купону
            if (!empty(self::$coupon['coupon']) && !empty($data['order_id'])) {
                $sfcom = new shopFlexdiscountCouponOrderPluginModel();
                $sfcom->insert(array(
                    "coupon_id" => (int) self::$coupon['coupon'],
                    "order_id" => (int) $data['order_id'],
                    "discount" => (float) self::$coupon['discount'],
                    "affiliate" => (shopAffiliate::isEnabled() ? (int) self::$coupon['affiliate'] : 0),
                    "datetime" => date("Y-m-d H:i:s"),
                ));
            }
            // Если были начислены бонусы за заказ, запоминаем их
            if (!empty(self::$affiliate) && !empty($data['order_id']) && !empty($data['contact_id']) && shopAffiliate::isEnabled()) {
                $sfam = new shopFlexdiscountAffiliatePluginModel();
                $sfam->insert(array(
                    "contact_id" => $data['contact_id'],
                    "order_id" => (int) $data['order_id'],
                    "affiliate" => (int) self::$affiliate,
                    "status" => 0
                ));
            }
        }
    }

    public function orderActionApplyAffiliate($data)
    {
        if (shopDiscounts::isEnabled('flexdiscount') && shopAffiliate::isEnabled()) {
            $sfam = new shopFlexdiscountAffiliatePluginModel();
            if ($sfam->isEnabled($data['order_id'])) {
                $affiliate = $sfam->getByOrder($data['order_id']);
                if ($affiliate) {
                    // Обновляем количество бонусов у пользователя
                    $shop_aff_trans = new shopAffiliateTransactionModel();
                    $shop_aff_trans->applyBonus($affiliate['contact_id'], $affiliate['affiliate'], $data['order_id'], "Бонус Гибких скидок");
                    $sfam->done($data['order_id']);
                }
            }
        }
    }

    public function orderActionCancelAffiliate($data)
    {
        if (shopDiscounts::isEnabled('flexdiscount') && shopAffiliate::isEnabled()) {
            $sfam = new shopFlexdiscountAffiliatePluginModel();
            if (!$sfam->isEnabled($data['order_id'])) {
                $affiliate = $sfam->getByOrder($data['order_id']);
                if ($affiliate) {
                    // Обновляем количество бонусов у пользователя
                    $shop_aff_trans = new shopAffiliateTransactionModel();
                    $shop_aff_trans->applyBonus($affiliate['contact_id'], -$affiliate['affiliate'], $data['order_id'], "Отмена бонуса Гибких скидок");
                    $sfam->cancel($data['order_id']);
                }
            }
        }
    }

    /**
     * Get path to file
     * @param string $file - filename or path
     * @return string - protected path to file
     */
    public static function path($file)
    {
        $path = wa()->getDataPath('plugins/flexdiscount/' . $file, false, 'shop', true);
        if (!file_exists($path)) {
            waFiles::copy(dirname(__FILE__) . '/config/' . $file, $path);
        }
        return $path;
    }

}
