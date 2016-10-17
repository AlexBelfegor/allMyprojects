<?php

return array(
    'name' => 'Промо 3Д',
    'version' => '1.0',
    'description' => 'Промо 3Д',
    'vendor' => 'blazewebart.com',
    'img' => 'img/promo.png',
    'frontend'    => true,
    'handlers' => array(
        'frontend_product' => 'promo_show',
        'backend_product' => 'toolbar_section',
    ),

);
//EOF
