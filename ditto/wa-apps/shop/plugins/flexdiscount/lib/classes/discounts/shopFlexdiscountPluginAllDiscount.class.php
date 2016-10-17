<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * Скидка на всю категорию или тип товара, вне зависимости от количества купленных товаров
 */

class shopFlexdiscountPluginAllDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = $price = $quantity = 0;
        $items = $order['items'];
        $mask_info = self::getMaskInfo();

        foreach ($items as $item) {
            // Проверяем принадлежность товара к указанным категории и типу
            if (!self::itemCheck($params, $item, $categories)) {
                continue;
            }
            $price += $item['price'] * $item['quantity'];
            $quantity += (int) $item['quantity'];
        }

        if ($price) {
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
