<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * При покупке более(или равно) X наборов (по одной уникальной единице) товаров из указанной категории начисляется скидка на все товары указанной категории
 */

class shopFlexdiscountPluginMoreEqualSetDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = 0;
        $affiliate = 0;
        $price = 0;
        $items = $order['items'];
        $user_products = array();
        preg_match("/\d+/", $params['value'], $matches);
        $user_count = reset($matches);

        $hash = '';
        // Узнаем количество товаров в категории
        if ($params['category_id']) {
            $hash = 'category/' . $params['category_id'];
        }
        $pc = new shopProductsCollection($hash);
        if ($params['type_id']) {
            waRequest::setParam('type_id', array($params['type_id']));
        }
        $pr = $pc->getProducts();
        $products_count = count($pr);

        foreach ($items as $item) {
            // Проверяем принадлежность товара к указанным категории и типу
            if (!self::itemCheck($params, $item, $categories)) {
                continue;
            }
            if (!isset($user_products[$item['product_id']])) {
                $user_products[$item['product_id']] = $item['quantity'];
            } else {
                $user_products[$item['product_id']] += $item['quantity'];
            }
            $price += (int) $item['price'] * $item['quantity'];
        }
        $count_user_products = count($user_products);
        // Если количество купленных товаров совпадает с количеством товаров в категории, т.е был
        // куплен набор и количество наборов превышает(или равно) заданный, то продолжаем обработку
        if ($count_user_products == $products_count && min($user_products) >= $user_count) {
            if ($params['discount_percentage']) {
                $discount += max(0.0, min(100.0, (float) $params['discount_percentage'])) * $price / 100.0;
            }
            if ($params['discount'] && $params['discount'] > 0) {
                $discount += $params['discount'];
            }
            if ($params['affiliate'] && $params['affiliate'] > 0) {
                $affiliate += $params['affiliate'];
            }
        }
        return ($discount || $affiliate) ? array("discount" => $discount, "affiliate" => $affiliate) : 0;
    }

}
