<?php

return array(
    'name' => 'Callback',
    'version' => '1.0',
    'description' => 'Callback',
    'vendor' => 'blazewebart.com',
    'frontend'    => true,
    'shop_settings' => true,
	'handlers' => array(
        'backend_category_dialog' => 'category_dialog',
        'backend_order' => 'backend_order_info_section',
        'category_save' => 'category_save',
        //'frontend_nav' => 'frontend_nav',
    ),
);
//EOF
