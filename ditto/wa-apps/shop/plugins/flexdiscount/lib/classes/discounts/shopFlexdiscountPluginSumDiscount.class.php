<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 * 
 * При покупке товаров на сумму, большую чем X, устанавливается скидка на определенную категорию, определенный тип товаров
 */

class shopFlexdiscountPluginSumDiscount extends shopFlexdiscountMask
{

    public function execute($params, $order, $categories, $contact, $apply)
    {
        $discount = $affiliate = $price = $total_price = $quantity = 0;
        $items = $order['items'];
        $mask_info = self::getMaskInfo();

        foreach ($items as $item) {
            if (!self::denyCheck($item, $categories, $params['set_id'], $contact->getId())) {
                continue;
            }
            $total_price += $item['price'] * $item['quantity'];
            // Проверяем принадлежность товара к указанным категории и типу
            if (!self::itemCheck($params, $item, $categories, false)) {
                continue;
            }

            $price += $item['price'] * $item['quantity'];
            $quantity += (int) $item['quantity'];
        }

        $param_v = shop_currency(substr($params['value'], 1), null, null, false);
        if ($total_price > $param_v) {
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
