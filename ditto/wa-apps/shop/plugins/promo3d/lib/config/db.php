<?php
return array(
    'shop_product_promo' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'product_id' => array('int', 11, 'null' => 0),
        'status' => array('tinyint', 4, 'null' => 0),
        'datetime' => array('datetime', 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('id', 'product_id'),
        ),
    ),
);
