<?php
return array(
    'name' => "Умная переадресация 301",
    'description' => "Автоматические правила переадресации",
    'img'=>'img/logo.png',
    'version' => '2.0.2',
    'vendor' => 1023936,
    'shop_settings' => true,
    'handlers' => array(
        'frontend_error' => 'frontendError',
		'product_save' => 'productSave',
		'product_delete' => 'productDelete',
		'category_save' => 'categorySave',
		'category_delete' => 'categoryDelete',
		'routing' => 'pageSave',
    ),
);
