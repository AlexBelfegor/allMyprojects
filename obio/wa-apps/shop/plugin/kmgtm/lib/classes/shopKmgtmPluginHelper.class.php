<?php

class shopKmgtmPluginHelper
{
    public static function formatPrice($price)
    {
        return str_replace(',', '.', (float)$price);
    }

    public static function getAddRemoveProductData($item)
    {
        $product = new shopProduct($item['product_id']);
        if ($item['sku_id']) {
            $sku = $product->skus[$item['sku_id']];
        } else {
            $sku = reset($product->skus);
        }
        $variant = $sku['id'] . (!empty($sku['sku']) ? " - " . $sku['sku'] : ((!empty($sku['name']) ? " / " . $sku['name'] : "")));
//        shopKmgtmPluginHTTPTransport::load(self::measurment_endpoint, 'POST', $params);
        $category_model = new shopCategoryModel();
        $category = $category_model->getById($product->category_id);
        $action = (isset($item['quantity_old']) && $item['quantity'] < $item['quantity_old']) ? 'remove' : 'add';
        $item['quantity_old'] = !isset($item['quantity_old']) ? 0 : $item['quantity_old'];
        return array(
            'action' => $action,
//                    'id' => $product->id,
            'name' => $product->name,
            'price' => shopKmgtmPluginHelper::formatPrice($sku['price']),
            'category' => $category['name'],
            'quantity' => $item['delete'] ? $item['quantity'] : abs($item['quantity'] - $item['quantity_old']),
            'variant' => $variant
        );
    }

}