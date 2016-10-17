<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopQuickorderPluginFrontendGetCartQuickorderController extends waJsonController
{

    public function execute()
    {
        // Получаем сумму заказа
        $cart_model = new shopCart();
        // Если корзина пуста - сообщаем об этом
        if ($cart_model->count()) {
            $total = $cart_model->total();
            $this->response = shop_currency($total, true);
        } else {
            $this->errors = 1;
        }
    }

}