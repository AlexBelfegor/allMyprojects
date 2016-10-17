<?php

class shopEarnberryHelper
{
    public static function getTrackingPlaceholder($product)
    {
        $plugin = wa()->getPlugin('earnberry');
        if ($plugin)
        {
            if ($plugin->isOperative())
            {
                $api = $plugin->getEarnberryApi();

                return $product['id'].'-<span class="earnberry-tracking-id-c">'.$api->getTrackingId().'</span>';
            }
        }
    }

    public static function getTrackingPlaceholderOnlyId()
    {
        $plugin = wa()->getPlugin('earnberry');
        if ($plugin)
        {
            if ($plugin->isOperative())
            {
                $api = $plugin->getEarnberryApi();

                return "-".$api->getTrackingId();
            }
        }
    }

}