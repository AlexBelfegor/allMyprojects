<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * Скидка  на каждый i-й одинаковый товар
 */

class shopFlexdiscountPluginEverySimilarDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = 0;
        $items = $order['items'];

        preg_match("/\d+/", $params['value'], $matches);
        $user_count = reset($matches);

        foreach ($items as $item) {
            // Проверяем принадлежность товара к указанным категории и типу
            if (!self::itemCheck($params, $item, $categories)) {
                continue;
            }
            $item['quantity'] = (int) $item['quantity'];
            $mult = floor($item['quantity'] / $user_count);
            if ($mult >= 1) {
                if ($params['discount_percentage']) {
                    $discount += max(0.0, min(100.0, (float) $params['discount_percentage'])) * $item['price'] * $mult / 100.0;
                }
                if ($params['discount'] && $params['discount'] > 0) {
                    $discount += $params['discount'] * $mult;
                }
                if ($params['affiliate'] && $params['affiliate'] > 0) {
                    $affiliate += $params['affiliate'] * $mult;
                }
            }
        }

        return ($discount || $affiliate) ? array("discount" => $discount, "affiliate" => $affiliate) : 0;
    }

}
