<?php
class shopGtmDimension
{
    //get brand
    public static function getFeaturesProd($id = 0, $get_array = false)
    {
        $features = Array();

        $brand = "";

        if($id)
        {
            $product_model = new shopProductModel();
            $product = $product_model->getById($id);
            $product = new shopProduct($product);

            if($get_array == false)
            {
                if (isset($product->features['brand']))
                {
                    $brand = $product->features['brand'];
                }
            }
            else
            {
                if (count($product->features))
                {
                    $features = $product->features;
                }
            }
        }
        return $get_array?$features:$brand;
    }
}