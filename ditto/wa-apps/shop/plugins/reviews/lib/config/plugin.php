<?php

return array(
    'name' => 'Отзывы в товаре',
    'version' => '1.0',
    'description' => 'Отзывы в товаре',
    'vendor' => 'blazewebart.com',
    'img' => 'img/reviews.png',
    'frontend'    => true,
    'handlers' => array(
        'frontend_product' => 'reviews_show',
    ),
);
//EOF
