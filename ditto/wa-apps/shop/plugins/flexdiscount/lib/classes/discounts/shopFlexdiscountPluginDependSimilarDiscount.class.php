<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * При покупке X одинаковых товаров устанавливается скидка на Y товаров из этого списка
 */

class shopFlexdiscountPluginDependSimilarDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = 0;
        $items = $order['items'];

        preg_match_all("/\d+/", $params['value'], $matches);

        if (!empty($matches[0][0])) {
            $user_count1 = $matches[0][0];
            $user_count2 = $matches[0][1];
            if ($user_count1 >= $user_count2) {
                foreach ($items as $item) {
                    // Проверяем принадлежность товара к указанным категории и типу
                    if (!self::itemCheck($params, $item, $categories)) {
                        continue;
                    }

                    $item['quantity'] = (int) $item['quantity'];
                    $mult = floor($item['quantity'] / $user_count1);
                    if ($mult >= 1) {
                        if ($params['discount_percentage']) {
                            $discount += max(0.0, min(100.0, (float) $params['discount_percentage'])) * $item['price'] * $user_count2 * $mult / 100.0;
                        }
                        if ($params['discount'] && $params['discount'] > 0) {
                            $discount += $params['discount'] * $user_count2 * $mult;
                        }
                        if ($params['affiliate'] && $params['affiliate'] > 0) {
                            $affiliate += $params['affiliate'] * $user_count2 * $mult;
                        }
                    }
                }
            }
        }

        return ($discount || $affiliate) ? array("discount" => $discount, "affiliate" => $affiliate) : 0;
    }

}
