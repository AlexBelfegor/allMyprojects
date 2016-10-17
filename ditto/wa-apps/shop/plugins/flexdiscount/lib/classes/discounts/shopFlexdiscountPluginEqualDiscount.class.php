<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * При покупке Х любых товаров устанавливается общая скидка
 */

class shopFlexdiscountPluginEqualDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = $quantity = $price = 0;
        $items = $order['items'];
        $mask_info = self::getMaskInfo();

        preg_match("/\d+/", $params['value'], $matches);
        $user_count = reset($matches);

        foreach ($items as $item) {
            // Проверяем принадлежность товара к указанным категории и типу
            if (!self::itemCheck($params, $item, $categories)) {
                continue;
            }

            $quantity += (int) $item['quantity'];
            $price += $item['price'] * $item['quantity'];
        }
        if ($quantity == $user_count) {
            if ($params['discount_percentage']) {
                $discount += max(0.0, min(100.0, (float) $params['discount_percentage'])) * $price / 100.0;
            }
            if ($params['discount'] && $params['discount'] > 0) {
                $discount += $params['discount'] * (!empty($mask_info['discountEachItem']) && $params['discounteachitem'] ? $quantity : 1);
            }
            if ($params['affiliate'] && $params['affiliate'] > 0) {
                $affiliate += $params['affiliate'] * (!empty($mask_info['discountEachItem']) && $params['discounteachitem'] ? $quantity : 1);
            }
        }

        return ($discount || $affiliate) ? array("discount" => $discount, "affiliate" => $affiliate) : 0;
    }

}
