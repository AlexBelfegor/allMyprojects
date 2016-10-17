<?php

return array(
    'name' => 'Earnberry',
    'description' => 'Интеграция с сервисом аналитики Earnberry.com',
    'vendor'=>'earnberry.com',
    'version'=>'0.3.1',
    'img'=> '',
    'frontend'    => false,
    'shop_settings' => true,
    'icons' => array(

    ),
    'handlers' => array(
        'backend_menu' => 'addBackendJs',
        'order_action.create' => 'orderCreate',
        'order_action.complete' => 'onOrderComplete',
        'frontend_head' => 'addFrontendJs',
        'frontend_footer' => 'addFrontendGaJs',
        'backend_order' => 'addNotificationForm',
        'order_action.delete' => 'onOrderRemove'
    ),
);
//EOF