<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * При покупке больше X одинаковых товаров устанавливается скидка на все товары из этого списка
 */

class shopFlexdiscountPluginMoreSimilarDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = 0;
        $result = $products = array();
        $items = $order['items'];

        preg_match("/\d+/", $params['value'], $matches);
        $user_count = reset($matches);

        // Настройки
        $sm = new shopFlexdiscountSettingsPluginModel();
        $count_method = $sm->get('count_method');
        $count_items = count($items);

        foreach ($items as $item_id => $item) {
            // Проверяем принадлежность товара к указанным категории и типу
            if (!self::itemCheck($params, $item, $categories)) {
                continue;
            }
            $products[$item['product_id']]['quantity'][] = $item['quantity'];
            $products[$item['product_id']]['price'][] = $item['price'] * $item['quantity'];
            $item['quantity'] = (int) $item['quantity'];
            // Если было выбрано значение настроек "подсчет количества товаров по артикулу" 
            if ($count_method !== 'product') {
                if ($user_count < $item['quantity']) {
                    if ($count_items > 1) {
                        $result[$item_id] = array('discount' => 0, 'affiliate' => 0);
                    }
                    if ($params['discount_percentage']) {
                        $discount_value = max(0.0, min(100.0, (float) $params['discount_percentage'])) * $item['price'] * $item['quantity'] / 100.0;
                        if ($count_items > 1) {
                            $result[$item_id]['discount'] += $discount_value;
                        }
                        $discount += $discount_value;
                    }
                    if ($params['discount'] && $params['discount'] > 0) {
                        $discount_value = $params['discount'] * $item['quantity'];
                        if ($count_items > 1) {
                            $result[$item_id]['discount'] += $discount_value;
                        }
                        $discount += $discount_value;
                    }
                    if ($params['affiliate'] && $params['affiliate'] > 0) {
                        $affiliate_value = $params['affiliate'] * $item['quantity'];
                        if ($count_items > 1) {
                            $result[$item_id]['affiliate'] += $affiliate_value;
                        }
                        $affiliate += $affiliate_value;
                    }
                }
            }
        }
        // Суммируем артикулы у товаров
        if ($count_method == 'product') {
            foreach ($products as $id => $value) {
                $quantity = array_sum($value['quantity']);
                if ($user_count < $quantity) {
                    if ($count_items > 1) {
                        $result[$id] = array('discount' => 0, 'affiliate' => 0);
                    }
                    if ($params['discount_percentage']) {
                        $discount_value = max(0.0, min(100.0, (float) $params['discount_percentage'])) * array_sum($value['price']) / 100.0;
                        if ($count_items > 1) {
                            $result[$id]['discount'] += $discount_value;
                        }
                        $discount += $discount_value;
                    }
                    if ($params['discount'] && $params['discount'] > 0) {
                        $discount_value = $params['discount'] * $quantity;
                        if ($count_items > 1) {
                            $result[$id]['discount'] += $discount_value;
                        }
                        $discount += $discount_value;
                    }
                    if ($params['affiliate'] && $params['affiliate'] > 0) {
                        $affiliate_value = $params['affiliate'] * $quantity;
                        if ($count_items > 1) {
                            $result[$id]['affiliate'] += $affiliate_value;
                        }
                        $affiliate += $affiliate_value;
                    }
                }
            }
        }

        return ($discount || $affiliate) ? ($result ? array('items' => $result, 'discount' => $discount, 'affiliate' => $affiliate) : array("discount" => $discount, "affiliate" => $affiliate)) : 0;
    }

}
