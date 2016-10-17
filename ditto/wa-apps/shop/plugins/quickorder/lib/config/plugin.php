<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */
return array(
    'name' => _wp('1-Click Ordering'),
    'description' => _wp('1-Click ordering lets you skip the shopping cart'), 
    'img' => 'img/quickorder.png',
    'vendor' => '969712',
    'version' => '1.3.8.1',
    'shop_settings' => true,
    'frontend' => true,
    'icons' => array(
        16 => 'img/quickorder.png',
        24 => 'img/quickorder24.png',
    ),
    'handlers' => array(
        'frontend_product' => 'frontendProduct',
        'frontend_head' => 'frontendHead',
        'frontend_cart'=> 'frontendCart',
    ),
);
