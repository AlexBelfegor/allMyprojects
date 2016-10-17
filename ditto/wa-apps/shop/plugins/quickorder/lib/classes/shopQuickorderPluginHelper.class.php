<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopQuickorderPluginHelper
{

    /**
     * Get product image
     * @param array $product
     * @param string $size - e.g 50x50
     * @param array $attributes
     * @return string
     */
    public static function productImgHtml($product, $size, $attributes = array())
    {
        if (!$product['image_id']) {
            if (!empty($attributes['default'])) {
                return '<img src="' . $attributes['default'] . '">';
            }
            return '';
        }
        if (!empty($product['image_desc']) && !isset($attributes['alt'])) {
            $attributes['alt'] = htmlspecialchars($product['image_desc']);
        }
        if (!empty($product['image_desc']) && !isset($attributes['title'])) {
            $attributes['title'] = htmlspecialchars($product['image_desc']);
        }
        $html = '<img';
        foreach ($attributes as $k => $v) {
            if ($k != 'default') {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= ' src="' . shopImage::getUrl(array(
                    'product_id' => $product['id'], 'id' => $product['image_id'], 'ext' => $product['ext']), $size) . '">';
        return $html;
    }

    /**
     * Convert primary currency to the current
     * @param float|int $price
     * @param string $in_currency
     * @return float
     */
    public static function convertCurrency($price, $in_currency = null)
    {
        if ($price) {
            $price = (float) shop_currency($price, $in_currency, null, false);
        }
        return $price;
    }

}