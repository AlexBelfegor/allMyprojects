<?php

return array(
    'name' => 'Кпопка вверх',
    'description' => 'Кнопка прокрутки страницы вверх, полный комплект настроек',
    'img'=>'img/Backtop.png',
    'version' => '1.1.1',
    'vendor' => 965055,
    'shop_settings' => true,
    'frontend'    => true,
    'icons'=>array(
        16 => 'img/Backtop.png',
    ),
    'handlers' => array(
        'frontend_header' => 'frontendHeader',
        'frontend_head' => 'frontendHead'
    ),
);
