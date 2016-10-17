<?php

class shopFlexdiscountHelperBW extends shopFlexdiscountMask
{
    private static $product_workflow = array();

    public static function CheckDiscount($product, $sku_id = '', $html = 0)
    {
        if (!empty($product))
        {
            $product = ($product instanceof shopProduct) ? $product->getData() : $product;

            if (shopDiscounts::isEnabled('flexdiscount'))
            {
                if (!empty(self::$product_workflow[$product['id']][$sku_id]))
                {
                    $workflow = shopFlexdiscountPluginHelper::$product_workflow[$product['id']][$sku_id];
                }
                else
                {
                    $workflow = shopFlexdiscountPluginHelper::getCurrentDiscount($product, $sku_id, array(), 1, false);
                }

                //print_r($workflow);
                if (!empty($workflow['discount']))
                {
                    return 1;
                }
            }
        }

        return 0;
    }

}
