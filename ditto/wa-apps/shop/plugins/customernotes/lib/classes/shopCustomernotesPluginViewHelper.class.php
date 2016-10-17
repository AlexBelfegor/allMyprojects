<?php

/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 14.09.14
 * Time: 23:27
 */
class shopCustomernotesPluginViewHelper extends waViewHelper
{
    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private static $plugin;

    private static function getPlugin()
    {
        if (!empty(self::$plugin)) {
            $plugin = self::$plugin;
        } else {
            $plugin = wa()->getPlugin('customernotes');
        }
        return $plugin;
    }

    public static function getCustomerRating($order_id, $rate_id = 0) {
        $nm = new shopCustomernotesNotesModel();
        $rating = $nm->getCustomerRating($order_id);
        return $rating;
    }

    public static function getRatePic($rate) {
        $output = '';
        if ($rate > 0) {
            $output = '<i class="rateUpIcon"></i>';
        }
        elseif ($rate < 0) {
            $output = '<i class="rateDownIcon"></i>';
        }
        else {
            $output = '<i class="emptyIcon"></i>';
        }
        return $output;
    }

}