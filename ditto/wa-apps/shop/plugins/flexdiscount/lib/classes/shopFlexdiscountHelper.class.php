<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopFlexdiscountHelper
{

    private static $settings;

    /**
     * Convert currencies
     * @param array $products
     * @param string $in_currency
     * @param string $out_currency
     * @return array
     */
    public static function convertCurrency($products, $in_currency = null, $out_currency = null)
    {
        if ($products) {
            if (!$out_currency) {
                $out_currency = wa('shop')->getConfig()->getCurrency(false);
            }
            if (is_array($products) && !isset($products['price'])) {
                foreach ($products as $k => $p) {
                    $products[$k]['price'] = (float) shop_currency($p['price'], $in_currency ? $in_currency : $p['currency'], $out_currency, false);
                    $products[$k]['quantity'] = (int) $p['quantity'];
                    $products[$k]['currency'] = $out_currency;
                }
            } else {
                if (!$in_currency) {
                    $in_currency = $products['currency'];
                }
                $products['price'] = (float) shop_currency($products['price'], $in_currency, $out_currency, false);
                $products['currency'] = $out_currency;
            }
        }
        return $products;
    }

    public static function secureString($str, $mode = ENT_QUOTES, $charset = 'UTF-8')
    {
        return htmlentities($str, $mode, $charset);
    }

    /**
     * Check coupon life
     * @param string $coupon
     * @return false|array
     */
    public static function couponCheck($coupon)
    {
        if ($coupon) {
            $scm = new shopFlexdiscountCouponPluginModel();
            $coupon = $scm->getByField("code", $coupon);
            $today = time();
            // Если срок действия купона истек
            if ($coupon['expire_datetime'] && ($today > strtotime($coupon['expire_datetime']))) {
                return false;
            }
            // Если достигнут предел по количеству использований купона
            if ($coupon['limit'] > 0 && $coupon['used'] >= $coupon['limit']) {
                return false;
            }
            return $coupon;
        }
        return false;
    }

    /**
     * Get code of block
     * @param string $id - block ID
     * @param array $params - assign vars
     * @return string - HTML
     */
    public static function getBlock($id, $params = array())
    {
        wa('site');
        $site_block_model = new siteBlockModel();
        $block = $site_block_model->getById($id);
        if ($block) {
            if ($params) {
                wa()->getView()->assign($params);
            }
            return wa()->getView()->fetch('string:' . $block['content']);
        } else {
            return '';
        }
    }

    /**
     * Get plugin settings
     * @return type
     */
    public static function getSettings()
    {
        if (!self::$settings) {
            $sm = new shopFlexdiscountSettingsPluginModel();
            self::$settings = $sm->getSettings();
        }
        return self::$settings;
    }
    
     /**
     * Check whether user field-mask is equal to mask or not
     * @param string $value - user mask
     * @return boolean|string
     */
    public static function isValidMask($mask, $value)
    {
        // Если выбраны "Скидка на всё", либо "Отмена скидок" тогда прерываем проверку
        if ($mask == "=" || $mask == "-") {
            return $mask;
        }
        // Создаем из переданного значения пользователя маску
        $user_mask = preg_replace(array("/\d+/", "/\s/"), array("D", ""), $value);
        $mask = preg_replace(array("/\d+/", "/\w+/"), "D", $mask);

        $value = preg_replace("/\s/", "", $value);

        // Если маски не равны
        if ($user_mask !== $mask) {
            // Проверяем есть ли у маски хеш на конце
            $hash = substr($mask, -1);
            if ($hash == '#') {
                // Если у значения пользователя нету хеша на конце, то добавляем его
                if (substr($user_mask, -1) !== '#') {
                    $user_mask .= "#";
                    if ($user_mask !== $mask) {
                        return false;
                    } else {
                        $value .= "#";
                    }
                }
            } else {
                return false;
            }
        }
        // Делаем проверку на ноль
        preg_match("/\d+/", $value, $matches);
        if (empty($matches[0])) {
            return false;
        }
        return $value;
    }

}
