<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopFlexdiscountPluginHelper extends shopFlexdiscountMask
{

    // Обработанные скидки для товара
    private static $product_workflow = array();

    /**
     * Get active discounts
     * @return array[name, discount]|array
     */
    public static function getUserDiscounts()
    {
        $user_discounts = array();
        if (shopDiscounts::isEnabled('flexdiscount')) {
            $storage_info = wa()->getStorage()->get('flexdiscount-discounts');
            if ($storage_info) {
                foreach ($storage_info as $k => $v) {
                    $user_discounts[$k]['name'] = shopFlexdiscountHelper::secureString($v['name']);
                    $user_discounts[$k]['discount'] = shop_currency($v['discount'], true);
                    $user_discounts[$k]['discount_html'] = shop_currency_html($v['discount'], true);
                    $user_discounts[$k]['affiliate'] = (int) $v['affiliate'];
                }
            }
            if ($user_discounts) {
                // Передаем данные в блок
                return shopFlexdiscountHelper::getBlock('flexdiscount.discounts', array(
                            'discounts' => $user_discounts
                ));
            }
        }
    }

    /**
     * Get product possible discount
     * @param array $product
     * @param string $view_type - type of display
     * @param int $sku_id - product sku ID
     * @return string - HTML
     */
    public static function getProductDiscounts($product, $view_type = null, $sku_id = '')
    {
        if (shopDiscounts::isEnabled('flexdiscount') && !empty($product)) {
            $product = ($product instanceof shopProduct) ? $product->getData() : $product;
            $return_tail = '';
            $return = "<span class='flexdiscount-product-discount product-id-" . $product['id'] . "' data-view-type='" . ($view_type ? shopFlexdiscountHelper::secureString($view_type) : '1') . "'";
            if (!$sku_id) {
                $sku_id = $product['sku_id'];
                $return .= " data-sku-id='0'";
            } else {
                $return .= " data-sku-id='" . $sku_id . "'";
            }
            $return .= ">";
            $return_tail .= "</span>";

            // Если товар уже обрабатывался, то возвращаем его данные
            if (!empty(self::$product_workflow[$product['id']][$sku_id])) {
                $workflow = self::$product_workflow[$product['id']][$sku_id];
            } else {
                $workflow = self::getCurrentDiscount($product, $sku_id, array(), 1, false);
            }
            // Передаем данные в блок
            return $return . shopFlexdiscountHelper::getBlock('flexdiscount.product.discounts', array(
                        'workflow' => $workflow,
                        'discounts' => $workflow['items'],
                        'view_type' => $view_type
                    )) . $return_tail;
        }
    }

    /**
     * Get user affiliate
     * @return string - HTML
     */
    public static function getUserAffiliate()
    {
        if (shopDiscounts::isEnabled('flexdiscount') && shopAffiliate::isEnabled()) {
            $user_affiliate = 0;
            $storage_info = wa()->getStorage()->get('flexdiscount-discounts');
            if ($storage_info) {
                foreach ($storage_info as $v) {
                    $user_affiliate += (int) $v['affiliate'];
                }
            }
            return shopFlexdiscountHelper::getBlock('flexdiscount.affiliate', array(
                        'affiliate' => $user_affiliate
            ));
        }
    }

    /**
     * Get availible discounts
     * If isset product, then get discounts only to it
     * @param array|shopProduct $product
     * @param string $view_type - type of display
     * @return string - HTML
     */
    public static function getAvailibleDiscounts($product = null, $view_type = null)
    {
        if (shopDiscounts::isEnabled('flexdiscount')) {
            $user_discounts = $user_categories = array();
            $return = $return_tail = '';
            $deny = false;
            $discounts = shopFlexdiscountWorkflow::getDiscounts();
            $contact_id = wa()->getUser()->getId();
            $coupon_info = shopFlexdiscountMask::getCouponInfo();
            // Если информация о купоне отсутствует
            if (!$coupon_info) {
                // Получаем действующий купон
                $coupon_code = wa()->getStorage()->get("flexdiscount-coupon");
                $coupon_info = shopFlexdiscountHelper::couponCheck($coupon_code);
            }

            if ($contact_id) {
                // Получаем категории, к которым принадлежит пользователь
                $wcc = new waContactCategoriesModel();
                $categories = $wcc->getContactCategories($contact_id);
                if ($categories) {
                    foreach ($categories as $c) {
                        $user_categories[$c['id']] = $c['id'];
                    }
                }
            }
            if ($product) {
                $product = ($product instanceof shopProduct) ? $product->getData() : $product;
                $new_product = $product;
                $new_product['type'] = 'product';
                if (!isset($new_product['product'])) {
                    $new_product['product'] = array();
                }
                $new_product['product']['id'] = $new_product['product_id'] = $new_product['id'];
                $new_product['product']['type_id'] = $new_product['type_id'];
            }
            $discount_masks = shopFlexdiscountWorkflow::getDiscountMasks();

            // Если не существует дерева категорий, то создаем его
            shopFlexdiscountMask::getCategoryTree();

            // Отсеиваем скидки, которые не распространяются на товар
            foreach ($discounts as $k => $d) {
                // Проверка принадлежности пользователя к категории контакта
                if ($d['contact_category_id'] && $user_categories) {
                    if (!isset($user_categories[$d['contact_category_id']])) {
                        unset($discounts[$k]);
                        continue;
                    }
                } elseif ($d['contact_category_id'] && !$user_categories) {
                    unset($discounts[$k]);
                    continue;
                }

                if ($d['mask'] == '-') {
                    unset($discounts[$k]);
                    $deny = true;
                    continue;
                }
                // Проверка времени действия скидки
                if (isset($d['expire_datetime']) && strtotime($d['expire_datetime']) < time()) {
                    unset($discounts[$k]);
                    continue;
                }
                // Если скидка закреплена за другим купоном, то прерываем ее обработку
                if ($coupon_info && $d['coupon_id'] && $coupon_info['id'] !== $d['coupon_id']) {
                    unset($discounts[$k]);
                    continue;
                } elseif (!$coupon_info && $d['coupon_id']) {
                    unset($discounts[$k]);
                    continue;
                }

                // Получаем значение масок скидок
                preg_match_all("/\d+/", $d['value'], $matches);
                $mask_value = array();
                // Первое значение маски
                if (!empty($matches[0][0])) {
                    $mask_value[] = $matches[0][0];
                }
                // Второе значение маски
                if (!empty($matches[0][1])) {
                    $mask_value[] = $matches[0][1];
                }
                // Третье значение маски (минимальная цена)
                if (!empty($matches[0][2])) {
                    $mask_value[] = $matches[0][2];
                }
                $discounts[$k]['mask_value'] = $mask_value;
                if ($product) {
                    if (!shopFlexdiscountMask::itemCheck($d, $new_product, shopFlexdiscountWorkflow::getCategories())) {
                        unset($discounts[$k]);
                        continue;
                    }
                }
            }
            // Если после проверок скидки еще остались
            if ($discounts) {
                // Округление скидок
                $settings = shopFlexdiscountHelper::getSettings();
                $round = isset($settings['round']) ? $settings['round'] : 'not';
                if ($product) {
                    $primary_currency = wa('shop')->getConfig()->getCurrency(true);
                    // Вычисляем значение скидок для каждого варианта товара
                    // Если вариантов нету, то мы находимся в каталоге
                    if (!empty($product['skus'])) {
                        foreach ($product['skus'] as $sku_id => $sku) {
                            foreach ($discounts as $k => $d) {
                                $discount = $sku['primary_price'] * $d['discount_percentage'] / 100 + (in_array($d['mask'], $discount_masks) ? $d['discount'] : 0);
                                $product_discount_price = $sku['primary_price'] - $discount;
                                $discount = ($round == 'ceil' ? ceil($discount) : ($round == 'floor' ? floor($discount) : $discount));
                                $product_discount_price = ($round == 'ceil' ? ceil($product_discount_price) : ($round == 'floor' ? floor($product_discount_price) : $product_discount_price));
                                $user_discounts[$sku_id][$d['id']] = array(
                                    'name' => $d['name'] ? $d['name'] : '',
                                    'discount' => shop_currency($discount, $primary_currency, null),
                                    'discount_html' => shop_currency_html($discount, $primary_currency, null),
                                    'price' => shop_currency($product_discount_price, $primary_currency, null),
                                    'price_html' => shop_currency_html($product_discount_price, $primary_currency, null),
                                    'params' => array(
                                        'discount' => shop_currency($d['discount'], $product['currency'], null),
                                        'discount_html' => shop_currency_html($d['discount'], $product['currency'], null),
                                        'discount_percentage' => $d['discount_percentage'],
                                        'affiliate' => $d['affiliate'],
                                        'value' => $d['mask_value'],
                                        'code' => $d['code'],
                                    )
                                );
                            }
                        }
                    } else {
                        $return .= "<span class='flexdiscount-show-all'>";
                        foreach ($discounts as $k => $d) {
                            $discount = $product['price'] * $d['discount_percentage'] / 100 + (in_array($d['mask'], $discount_masks) ? $d['discount'] : 0);
                            $product_discount_price = $product['price'] - $discount;
                            $discount = ($round == 'ceil' ? ceil($discount) : ($round == 'floor' ? floor($discount) : $discount));
                            $product_discount_price = ($round == 'ceil' ? ceil($product_discount_price) : ($round == 'floor' ? floor($product_discount_price) : $product_discount_price));
                            $user_discounts[0][$d['id']] = array(
                                'name' => $d['name'] ? $d['name'] : '',
                                'discount' => shop_currency($discount, $primary_currency, null),
                                'discount_html' => shop_currency_html($discount, $primary_currency, null),
                                'price' => shop_currency($product_discount_price, $primary_currency, null),
                                'price_html' => shop_currency_html($product_discount_price, $primary_currency, null),
                                'params' => array(
                                    'discount' => shop_currency($d['discount'], $product['currency'], null),
                                    'discount_html' => shop_currency_html($d['discount'], $product['currency'], null),
                                    'discount_percentage' => $d['discount_percentage'],
                                    'affiliate' => $d['affiliate'],
                                    'value' => $d['mask_value'],
                                    'code' => $d['code'],
                                )
                            );
                        }
                        $return_tail .= "</span>";
                    }
                } else {
                    $return .= "<span class='flexdiscount-show-all'>";
                    foreach ($discounts as $k => $d) {
                        $user_discounts[0][$d['id']] = array(
                            'name' => $d['name'] ? $d['name'] : '',
                            'discount' => null,
                            'discount_html' => null,
                            'price' => null,
                            'price_html' => null,
                            'params' => array(
                                'discount' => shop_currency($d['discount'], null, null),
                                'discount_html' => shop_currency_html($d['discount'], null, null),
                                'discount_percentage' => $d['discount_percentage'],
                                'affiliate' => $d['affiliate'],
                                'value' => $d['mask_value'],
                                'code' => $d['code'],
                            )
                        );
                    }
                    $return_tail .= "</span>";
                }
            }
            // Передаем данные в блок
            return $return . shopFlexdiscountHelper::getBlock('flexdiscount.available', array(
                        'discounts' => $user_discounts,
                        'view_type' => $view_type,
                        'deny' => $deny
                    )) . $return_tail;
        }
    }

    /**
     * Get all workflow of discounts for product. Uses for calculating workflow.
     * TODO: Do i need to use template or just calculate?
     * @param array|shopProduct $product
     * @param int $sku_id
     * @param array $params - vars to assign
     * @param int $quantity
     * @param bool $return_html - return HTML or data
     * @return string
     */
    public static function getCurrentDiscount($product, $sku_id = '', $params = array(), $quantity = 1, $return_html = true)
    {
        if (shopDiscounts::isEnabled('flexdiscount') && !empty($product)) {
            $product = ($product instanceof shopProduct) ? $product->getData() : $product;
            // Если передан вариант товара, то заменяем данные
            if ($sku_id) {
                $sku_model = new shopProductSkusModel();
                $sku = $sku_model->getSku($sku_id);
                if ($sku) {
                    $product['sku_id'] = $sku['id'];
                    $product['price'] = shop_currency($sku['price'], $product['currency'], wa('shop')->getConfig()->getCurrency(true), false);
                }
            }
            //Если этот товар уже обрабатывался, то вернем его данные
            if (!empty(self::$product_workflow[$product['id']][$product['sku_id']])) {
                if ($return_html) {
                    // Передаем данные в блок
                    $params['workflow'] = self::$product_workflow[$product['id']][$product['sku_id']];
                    return shopFlexdiscountHelper::getBlock('flexdiscount.current', $params);
                } else {
                    return self::$product_workflow[$product['id']][$product['sku_id']];
                }
            }
            // Изменяем количество товара
            if ($quantity) {
                $product['quantity'] = $quantity;
            }
            $workflow = array(
                'discount' => 0,
                'affiliate' => 0,
                'product_id' => $product['id'],
                'items' => array()
            );
            $discount = 0;
            $affiliate = 0;
            // Получаем значения скидок для корзины
            $cart_workflow = shopFlexdiscountWorkflow::getCartWorkflow();
            // Если такая обработка еще не выполнялась, то производим ее
            if (!$cart_workflow) {
                $cart_workflow = shopFlexdiscountWorkflow::cartWorkflow();
            }

            // Цена товара приходит в основной валюте. У товара указана валюта, указанной цены за него
            // Нам необходимо сконвертировать цену в текущую валюту
            // На входе основная валюта - на выходе текущая
            $product = shopFlexdiscountHelper::convertCurrency($product, wa('shop')->getConfig()->getCurrency(true), wa('shop')->getConfig()->getCurrency(false));
            // Имитируем наличие переданного товара в корзине
            $update_workflow = shopFlexdiscountWorkflow::updateWorkflow($product);
            if ($cart_workflow['discount'] < $update_workflow['discount'] || $cart_workflow['affiliate'] < $update_workflow['affiliate']) {
                // Получаем маски, которые начисляют скидки на каждый товар, а не на общую сумму.
                // Необходимо для расчета индивидуальной скидки товара
                $discount_masks = shopFlexdiscountWorkflow::getDiscountMasks();
                $ignore_masks = shopFlexdiscountWorkflow::getIgnoreMasks();
                // Округление скидок
                $settings = shopFlexdiscountHelper::getSettings();
                $round = isset($settings['round']) ? $settings['round'] : 'not';
                // Выполняем перебор новых скидок
                foreach ($update_workflow['discount_name'] as $k => $discount_name) {
                    if (!in_array($discount_name['params']['mask'], $ignore_masks)) {
                        // Если такая скидка уже применялась
                        if (isset($cart_workflow['discount_name'][$k])) {
                            // Определяем была ли применена скидка в денежной форме
                            if ($cart_workflow['discount_name'][$k]['discount'] < $discount_name['discount']) {
                                $workflow['items'][$k] = $discount_name;
                                $workflow['items'][$k]['discount'] = ($discount_name['discount'] - $cart_workflow['discount_name'][$k]['discount']) / $product['quantity']; //$product['price'] * $discount_name['params']['discount_percentage'] / (100*$update_product_count) + (in_array($discount_name['params']['mask'], $discount_masks) ? $discount_name['params']['discount'] : 0);
                                $discount += $workflow['items'][$k]['discount'];

                                $workflow['items'][$k]['discount'] = ($round == 'ceil' ? ceil($workflow['items'][$k]['discount']) : ($round == 'floor' ? floor($workflow['items'][$k]['discount']) : $workflow['items'][$k]['discount']));
                                $workflow['items'][$k]['discount'] = shop_currency($workflow['items'][$k]['discount'], $product['currency'], null);
                                $workflow['items'][$k]['discount_html'] = shop_currency_html($workflow['items'][$k]['discount'], $product['currency'], null);
                            }
                            // Определяем была ли применена скидка в бонусной форме
                            if ($cart_workflow['discount_name'][$k]['affiliate'] < $discount_name['affiliate']) {
                                if (!isset($workflow['items'][$k])) {
                                    $workflow['items'][$k] = $discount_name;
                                }
                                $workflow['items'][$k]['affiliate'] = (in_array($discount_name['params']['mask'], $discount_masks) ? $discount_name['params']['affiliate'] : 0);
                                $affiliate += $workflow['items'][$k]['affiliate'];
                            }
                        } else {
                            $workflow['items'][$k] = $discount_name;
                            $workflow['items'][$k]['discount'] = $discount_name['discount'] / $product['quantity']; //$product['price'] * $discount_name['params']['discount_percentage'] / (100*$update_product_count) + (in_array($discount_name['params']['mask'], $discount_masks) ? $discount_name['params']['discount'] : 0);
                            $workflow['items'][$k]['affiliate'] = (in_array($discount_name['params']['mask'], $discount_masks) ? $discount_name['params']['affiliate'] : 0);
                            $discount += $workflow['items'][$k]['discount'];
                            $affiliate += $workflow['items'][$k]['affiliate'];

                            $workflow['items'][$k]['discount'] = ($round == 'ceil' ? ceil($workflow['items'][$k]['discount']) : ($round == 'floor' ? floor($workflow['items'][$k]['discount']) : $workflow['items'][$k]['discount']));
                            $workflow['items'][$k]['discount'] = shop_currency($workflow['items'][$k]['discount'], $product['currency'], null);
                            $workflow['items'][$k]['discount_html'] = shop_currency_html($workflow['items'][$k]['discount'], $product['currency'], null);
                        }
                    }
                }
                // Цена товара со скидкой
                $product_discount_price = $product['price'] - $discount;

                $discount = ($round == 'ceil' ? ceil($discount) : ($round == 'floor' ? floor($discount) : $discount));
                $workflow['discount'] = shop_currency($discount, $product['currency'], null);
                $workflow['discount_html'] = shop_currency_html($discount, $product['currency'], null);
                $workflow['affiliate'] = $affiliate;

                $product_discount_price = ($round == 'ceil' ? ceil($product_discount_price) : ($round == 'floor' ? floor($product_discount_price) : $product_discount_price));
                $workflow['clear_price'] = ($product_discount_price > 0) ? shop_currency($product_discount_price, $product['currency'], null, 0) : 0;
                $workflow['price'] = ($product_discount_price > 0) ? shop_currency($product_discount_price, $product['currency'], null) : 0;
                $workflow['price_html'] = ($product_discount_price > 0) ? shop_currency_html($product_discount_price, $product['currency'], null) : 0;

                // Запоминаем обработанные скидки для товара
                self::$product_workflow[$product['id']][$product['sku_id']] = $workflow;
            }
            $workflow['deny'] = $cart_workflow['deny'];
            /**
             * Содержимое $workflow
             *   Массив, содержащий информацию о возможных скидках к товару
             *   array(
             *       'discount' => общая скидка для товара,
             *       'discount_html' => общая скидка для товара с символом рубля,
             *       'affiliate' => количество бонусов,
             *       'clear_price' => чистая цена без валют,
             *       'price' => цена товара со скидкой,
             *       'price_html' => цена товара со скидкой с символом рубля,
             *       'product_id' => id товара,
             *       'items' => array( значения примененных скидок
             *              'discount' => скидка,
             *              'discount_html' => скидка с символом рубля,
             *              'affiliate' => бонус,
             *              'name' => название скидки,
             *              'params' => array( информация о правиле скидок
             *                  'discount' => абсолютное значение скидки в валюте,
             *                  'discount_percentage' => процентное значение скидки,
             *                  'affiliate' => бонусы,
             *                  'mask' => маска правила скидок,
             *                  'code' => символьный код скидки,
             *              )
             *          ),
             *        'deny' => (bool) Был ли хоть раз применен запрет скидки для товара
             *       )
             *   )
             */
            if ($return_html) {
                // Передаем данные в блок
                $params['workflow'] = $workflow;
                return shopFlexdiscountHelper::getBlock('flexdiscount.current', $params);
            } else {
                return $workflow;
            }
        }
    }

    /**
     * Get new product price with discount
     * @param aray $product
     * @param int $sku_id
     * @param bool $html - uses for ruble symbol
     * @return string
     */
    public static function price($product, $sku_id = '', $html = 0)
    {
        // Если плагин будет отключен, то выведется обычная цена.
        // Это сделано на случай, если пользователь заменит стандартный вывод цены в шаблоне
        if (!empty($product)) {
            $product = ($product instanceof shopProduct) ? $product->getData() : $product;
            $return = '';
            $return_tail = '';
            if (shopDiscounts::isEnabled('flexdiscount')) {

                $settings = shopFlexdiscountHelper::getSettings();
                if (isset($settings['ruble']) && $settings['ruble'] == 'html') {
                    $html = 1;
                } else {
                    $html = 0;
                }

                $return .= "<span class='flexdiscount-price product-id-" . $product['id'] . "'";
                if (!$sku_id) {
                    $sku_id = $product['sku_id'];
                    $return .= " data-sku-id='0'";
                } else {
                    $return .= " data-sku-id='" . $sku_id . "'";
                }
                $return_tail .= "</span>";

                // Если товар уже обрабатывался, то возвращаем его данные
                if (!empty(self::$product_workflow[$product['id']][$sku_id])) {
                    $workflow = self::$product_workflow[$product['id']][$sku_id];
                } else {
                    $workflow = self::getCurrentDiscount($product, $sku_id, array(), 1, false);
                }
                if (!empty($workflow['price'])) {
                    $return .= " data-price='" . $workflow['clear_price'] . "'>";
                    return $return . ($html ? $workflow['price_html'] : $workflow['price']) . $return_tail;
                }
            }
            if (!empty($sku_id) && !empty($product['skus'][$sku_id])) {
                $price = $product['skus'][$sku_id]['price'];
                $price = $html ? shop_currency_html($product['skus'][$sku_id]['price'], $product['currency'], wa('shop')->getConfig()->getCurrency(false)) : shop_currency($product['skus'][$sku_id]['price'], $product['currency'], wa('shop')->getConfig()->getCurrency(false));
                $return .= ' data-price="' . shop_currency($product['skus'][$sku_id]['price'], $product['currency'], wa('shop')->getConfig()->getCurrency(true), 0) . '"';
            } else {
                $price = $html ? shop_currency_html($product['price'], wa('shop')->getConfig()->getCurrency(true), wa('shop')->getConfig()->getCurrency(false)) : shop_currency($product['price'], wa('shop')->getConfig()->getCurrency(true), wa('shop')->getConfig()->getCurrency(false));
                $return .= ' data-price="' . shop_currency($product['price'], wa('shop')->getConfig()->getCurrency(true), wa('shop')->getConfig()->getCurrency(false), 0) . '"';
            }
            $return .= ">";
            return $return . $price . $return_tail;
        }
        return '';
    }

}
