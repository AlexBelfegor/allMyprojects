<?php
return array(
    'name' => 'Google Tag Manager и Enhanced Ecommerce',
    'description' => 'Поддержка GTM и расширенной электронной торговли для Google Analytics',
    'img' => 'img/kmgtm.png',
    'icons' => array(
        16 => 'img/kmgtm16.png',
        24 => 'img/kmgtm24.png',
        48 => 'img/kmgtm48.png',
        96 => 'img/kmgtm96.png',
    ),
    'version' => '1.0.3',
    'vendor' => '992205',
    'shop_settings' => true,
    'handlers' =>
        array(
            'frontend_head' => 'frontendHeadHandler',
            'frontend_header' => 'frontendHeaderHandler',
            'frontend_checkout' => 'frontendCheckoutHandler',
            'frontend_footer' => 'frontendFooterHandler',
            'frontend_cart' => 'frontendCartHandler',
//            'backend_order_edit' => 'backendOrderEditHandler',
            'backend_orders' => 'backendOrdersHandler',
            'backend_order' => 'backendOrderHandler',
        ),
    'frontend' => true
);
