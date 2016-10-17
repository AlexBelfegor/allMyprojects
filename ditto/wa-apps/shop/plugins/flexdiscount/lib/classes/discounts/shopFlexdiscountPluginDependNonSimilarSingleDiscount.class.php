<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * При покупке X любых товаров устанавливается скидка Y товаров самой низкой цены из этого списка
 */

class shopFlexdiscountPluginDependNonSimilarSingleDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = $quantity = 0;
        $items = $order['items'];
        $product_price = array();

        preg_match_all("/\d+/", $params['value'], $matches);
        if (!empty($matches[0][0])) {
            $user_count1 = $matches[0][0];
            $user_count2 = $matches[0][1];
            foreach ($items as $item) {
                // Проверяем принадлежность товара к указанным категории и типу
                if (!self::itemCheck($params, $item, $categories)) {
                    continue;
                }
                $product_price[(string) $item['price']] = array(
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                );

                $quantity += (int) $item['quantity'];
            }
            $mult = floor($quantity / $user_count1);
            // Если была хотя бы одна низкая цена
            if ($mult >= 1 && count($product_price) > 1) {
                ksort($product_price);
                $price = reset($product_price);
                $mult *= $user_count2;
                if ($mult > $price['quantity']) {
                    $mult = $price['quantity'];
                }
                if ($params['discount_percentage']) {
                    $discount += max(0.0, min(100.0, (float) $params['discount_percentage'])) * $price['price'] * $mult / 100.0;
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
