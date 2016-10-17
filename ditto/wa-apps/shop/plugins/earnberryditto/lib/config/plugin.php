<?php

return array(
    'name' => 'Earnberry Ditto',
    'description' => 'Интеграция с сервисом аналитики Earnberry.com',
    'vendor'=>'earnberry inc.',
    'version'=>'0.0.1',
    'img'=> '',
    'frontend'    => false,
    'shop_settings' => true,
    'icons' => array(

    ),
    'handlers' => array(
        'earnberry_workup_product' => 'workupProduct',
        'earnberry_wrap_transaction' => 'wrapTransaction'
    ),
);
//EOF