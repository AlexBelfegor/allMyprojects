<?php

class shopKmgtmPluginFrontendChangePurchaseController extends waJsonController
{
    public function execute()
    {
        $item_id = waRequest::post('item_id', false, waRequest::TYPE_INT);
        $quantity = waRequest::post('quantity', false, waRequest::TYPE_INT);
        $quantity_old = waRequest::post('quantity_old', false, waRequest::TYPE_INT);
        $product_id = waRequest::post('product_id', false, waRequest::TYPE_INT);
        $sku_id = waRequest::post('sku_id', false, waRequest::TYPE_INT);
        $delete = waRequest::post('delete', false);

        if ($item_id && $quantity !== false && $quantity_old !== false) {
//            if ($delete) {
//                $quantity_old++;
//            }
            if ($delete || ($quantity != $quantity_old)) {
                $cart_items_model = new shopCartItemsModel();
                $item = $cart_items_model->getById($item_id);
                $this->response = shopKmgtmPluginHelper::getAddRemoveProductData(array(
                    'product_id' => $item['product_id'],
                    'sku_id' => $item['sku_id'],
                    'quantity' => $quantity,
                    'quantity_old' => $quantity_old,
                    'delete' => $delete
                ));
            }
        } elseif ($product_id) {
            $this->response = shopKmgtmPluginHelper::getAddRemoveProductData(array(
                'product_id' => $product_id,
                'sku_id' => $sku_id,
                'quantity' => 1,
                'quantity_old' => 0,
                'delete' => $delete
            ));
        }
    }
}