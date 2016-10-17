<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopFlexdiscountWorkflow
{

    // Все категории с товарами
    private static $categories = array();
    // Действующие скидки
    private static $discounts = array();
    // Обработанные скидки для корзины
    private static $cart_workflow;
    // Текущий заказ
    private static $order;
    // Маски, которые начисляют скидку в валюте на каждый товар, а не на весь заказ
    // Необходимо знать для расчета индивидуальной скидки товара
    private static $discount_masks = array(
        '%num', 'num%num', 'num#num', '>%num', '>%num#'
    );
    // Маски, которые нужно игнорировать при расчете индивидуальной скидки
    private static $ignore_masks = array(
        '>%sum'
    );

    private static function init()
    {
        if (!self::$categories) {
            $scp = new shopCategoryProductsModel();
            self::$categories = $scp->select("product_id, category_id")->fetchAll('category_id', 2);
        }
        if (!self::$discounts) {
            $dbc = new shopFlexdiscountPluginModel();
            self::$discounts = $dbc->getDiscounts();
        }
    }

    public static function getCartWorkflow()
    {
        return self::$cart_workflow;
    }

    public static function getDiscountMasks()
    {
        return self::$discount_masks;
    }
    
    public static function getOrder()
    {
        return self::$order;
    }

    public static function getIgnoreMasks()
    {
        return self::$ignore_masks;
    }

    public static function getCategories()
    {
        if (!self::$categories) {
            self::init();
        }
        return self::$categories;
    }

    public static function getDiscounts()
    {
        if (!self::$discounts) {
            self::init();
        }
        return self::$discounts;
    }

    /**
     * Get cart discounts
     * @return array
     */
    public static function cartWorkflow()
    {
        $shopCart = new shopCart();
        $model = new shopCartItemsModel();

        // Формируем данные из корзины для анализа скидок
        $items = $shopCart->items(false);
        $total = $model->total($shopCart->getCode());
        self::$order = array(
            'total' => $total,
            'items' => $items ? $items : array()
        );
        $params = array(
            'order' => self::$order,
            'contact' => wa()->getUser(),
            'apply' => 0
        );
        self::$cart_workflow = self::process($params);
        return self::$cart_workflow;
    }

    /**
     * Get discounts with possible product 
     * @param array $product
     * @return type
     */
    public static function updateWorkflow($product)
    {
        if (!self::$order) {
            self::cartWorkflow();
        }
        $new_order = self::$order;
        $new_product = $product;
        $new_product['type'] = 'product';
        if (!isset($new_product['product'])) {
            $new_product['product'] = array();
        }
        $new_product['product']['id'] = $new_product['product_id'] = $new_product['id'];
        $new_product['product']['type_id'] = $new_product['type_id'];

        $merge = false;
        // Проверяем есть ли такой товар в корзине, если имеется, то увеличиваем количество
        foreach ($new_order['items'] as $k => $item) {
            if ($item['sku_id'] == $new_product['sku_id']) {
                $new_order['items'][$k]['quantity'] += $new_product['quantity'];
                $merge = true;
            }
        }
        if (!$merge) {
            $new_order['items'][] = $new_product;
        }
        $params = array(
            'order' => $new_order,
            'contact' => wa()->getUser(),
            'apply' => 0
        );
        
        return self::process($params);
    }

    /**
     * @param array $params['order'] - order info array('total' => '', 'items' => array(...)) 
     * @param waAuthUser $params['contact'] - contact info 
     * @param bool $params['apply'] - calculate or apply discount 
     * @param bool $cart_workflow - do we need to save results of workflow to self::$cart_workflow
     * @return array - array('discount' => float, 
     *                       'affiliate'=> int,
     *                       'discount_name' => array of discounts,
     *                       'coupon_used' => array info about used coupon)
     */
    public static function process($params, $cart_workflow = false)
    {
        self::init();
        
        $discount = $affiliate = 0;
        $discount_name = $coupon_used = array();
        // Флаг запрета скидок
        $deny = false;
        $max = array("discount" => 0, "affiliate" => 0);
        
        if (self::$discounts) {
            // Конвертируем цены товаров
            $params['order']['items'] = shopFlexdiscountHelper::convertCurrency($params['order']['items']);
            $coupon_used = array("coupon" => 0, "discount" => 0, "affiliate" => 0);
            $discount_mask = array();
            foreach (self::$discounts as $d) {
                // Конвертируем скидку в валюте
                if ($d['discount']) {
                    $d['discount'] = shop_currency($d['discount'], null, null, false);
                }
                $discount_result = shopFlexdiscountMask::getDiscount($d, $params['order'], self::$categories, $params['contact'], $params['apply']);
                // Если была начислена скидка и не было запрета
                if ($discount_result && !isset($discount_result['deny'])) {
                    $d['value'] = preg_replace("/\D/", "", $d['value']);
                    // Если скидка у данной категории и типа товара уже существует, то применяем скидку,
                    // близкую по значению суммы/количества товара.
                    // Если данной категории и типа не существует - создаем их
                    if (isset($discount_result['items'])) {
                        $discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']][$d['value']] = array(
                            "id" => $d['id'],
                            "discount" => $discount_result['discount'],
                            "affiliate" => $discount_result['affiliate'],
                            "value" => $d['value'],
                            "name" => $d['name'],
                            "coupon" => $discount_result['coupon'],
                            "items" => $discount_result['items'],
                            "params" => array(
                                "discount" => $d['discount'],
                                "discount_percentage" => $d['discount_percentage'],
                                "affiliate" => $d['affiliate'],
                                "mask" => $d['mask'],
                                "code" => $d['code'],
                            ),
                        );
                    } elseif ((isset($discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']]) && ($discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']]['value'] < $d['value'] || $discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']]['discount'] < $discount_result['discount'] || ($discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']]['discount'] == $discount_result['discount'] && $discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']]['affiliate'] < $discount_result['affiliate']))) || !isset($discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']])) {
                        $discount_mask[$d['mask']][$d['category_id'] . "-" . $d['type_id']] = array(
                            "id" => $d['id'],
                            "discount" => $discount_result['discount'],
                            "affiliate" => $discount_result['affiliate'],
                            "value" => $d['value'],
                            "name" => $d['name'],
                            "coupon" => $discount_result['coupon'],
                            "params" => array(
                                "discount" => $d['discount'],
                                "discount_percentage" => $d['discount_percentage'],
                                "affiliate" => $d['affiliate'],
                                "mask" => $d['mask'],
                                "code" => $d['code'],
                            ),
                        );
                    }
                } elseif (isset($discount_result['deny'])) {
                    $deny = true;
                }
            }
            $sm = new shopFlexdiscountSettingsPluginModel();
            $sum = $sm->get('combine');
            // Если были скидки - добавляем их в общий поток скидок
            if ($discount_mask) {
                foreach ($discount_mask as $k => $mask) {
                    foreach ($mask as $c => $t) {
                        // Если необходимо выбрать скидки из разных значений
                        $t_len = count($t);
                        if (!isset($t['name'])) {
                            $items = array();
                            $prev = array();
                            ksort($t);
                            $counter = 0;
                            foreach ($t as $t_value => $t_discount) {
                                if ($prev) {
                                    $diff = array_diff_key($prev['items'], $t_discount['items']);
                                    if ($diff) {
                                        $ii = 0;
                                        $diff_len = count($diff);
                                        $diff_discount = 0;
                                        $diff_affiliate = 0;
                                        foreach ($diff as $diff_key => $diff_val) {
                                            $diff_discount += $diff_val['discount'];
                                            $diff_affiliate += $diff_val['affiliate'];
                                            if ($ii == $diff_len - 1) {
                                                if ($sum == 'sum') {
                                                    $discount += (float) $diff_discount;
                                                    $affiliate += (int) $diff_affiliate;
                                                    $discount_name[$prev['id']] = array("name" => $prev['name'], "discount" => $diff_discount, "affiliate" => $diff_affiliate, "params" => $prev['params']);
                                                    if ($prev['coupon']) {
                                                        $coupon_used['coupon'] = $prev['coupon'];
                                                        $coupon_used['discount'] += $diff_discount;
                                                        $coupon_used['affiliate'] += $diff_affiliate;
                                                    }
                                                } else {
                                                    // Если считается максимум
                                                    if (($max['discount'] < $diff_discount) || ($diff_discount == 0 && $max['affiliate'] < $diff_affiliate)) {
                                                        $max = $prev;
                                                        $max['discount'] = $diff_discount;
                                                        $max['affiliate'] = $diff_affiliate;
                                                    }
                                                }
                                            }
                                            $ii++;
                                        }
                                    }
                                }
                                if ($counter == $t_len - 1) {
                                    $t_discount_sum = $t_affiliate_sum = 0;
                                    foreach ($t_discount['items'] as $tdk => $tdv) {
                                        $t_discount_sum += $tdv['discount'];
                                        $t_affiliate_sum += $tdv['affiliate'];
                                    }
                                    if ($sum == 'sum') {
                                        $discount += (float) $t_discount_sum;
                                        $affiliate += (int) $t_affiliate_sum;
                                        $discount_name[$t_discount['id']] = array("name" => $t_discount['name'], "discount" => $t_discount_sum, "affiliate" => $t_affiliate_sum, "params" => $t_discount['params']);
                                        if ($t_discount['coupon']) {
                                            $coupon_used['coupon'] = $t_discount['coupon'];
                                            $coupon_used['discount'] += $t_discount_sum;
                                            $coupon_used['affiliate'] += $t_affiliate_sum;
                                        }
                                    } else {
                                        // Если считается максимум
                                        if (($max['discount'] < $t_discount_sum) || ($t_discount_sum == 0 && $max['affiliate'] < $t_affiliate_sum)) {
                                            $max = $t_discount;
                                            $max['discount'] = $t_discount_sum;
                                            $max['affiliate'] = $t_affiliate_sum;
                                        }
                                    }
                                }
                                $prev = $t_discount;
                                $counter++;
                            }
                        } else {
                            // Если считается сумма скидок
                            if ($sum == 'sum') {
                                $discount += (float) $t['discount'];
                                $affiliate += (int) $t['affiliate'];
                                $discount_name[$t['id']] = array("name" => $t['name'], "discount" => $t['discount'], "affiliate" => $t['affiliate'], "params" => $t['params']);
                                if ($t['coupon']) {
                                    $coupon_used['coupon'] = $t['coupon'];
                                    $coupon_used['discount'] += $t['discount'];
                                    $coupon_used['affiliate'] += $t['affiliate'];
                                }
                            } else {
                                // Если считается максимум
                                if (($max['discount'] < $t['discount']) || ($t['discount'] == 0 && $max['affiliate'] < $t['affiliate'])) {
                                    $max = $t;
                                }
                            }
                        }
                    }
                }
                // Для максимума запоминаем название скидки и купон
                if ($sum !== 'sum' && $max) {
                    $discount = (float) $max['discount'];
                    $affiliate = (int) $max['affiliate'];
                    $discount_name[$max['id']] = array("name" => $max['name'], "discount" => $discount, "affiliate" => $affiliate, "params" => $max['params']);
                    if ($max['coupon']) {
                        $coupon_used['coupon'] = $max['coupon'];
                        $coupon_used['discount'] = $discount;
                        $coupon_used['affiliate'] = $affiliate;
                    }
                }
            }
        }
        
        $workflow = array(
            "discount_name" => $discount_name,
            "discount" => $discount,
            "affiliate" => $affiliate,
            "coupon_used" => $coupon_used,
            "deny" => $deny,
        );
        if ($cart_workflow) {
            self::$cart_workflow = $workflow;
        }
        return $workflow;
    }

}