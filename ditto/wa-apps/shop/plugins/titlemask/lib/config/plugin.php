<?php
/**
 * @author Плагины Вебасист <info@wa-apps.ru>
 * @link http://wa-apps.ru/
 */
return array(
    'name' => 'Title Mask',
    'description' => 'Позволяет массово назначать meta-теги на ключевых страницах по определенным правилам',
    'version'=>'2.9.1',
    'vendor' => 809114,
    'img'=>'img/titlemask.png',
    'shop_settings' => true,
    'handlers' => array(
        'frontend_category' => 'titleCategory',
        'frontend_product' => 'titleProduct',
        'frontend_head' => 'frontendHead',
        'category_save' => 'categorySave',
        'backend_category_dialog' => 'categoryDialog',
    )
);