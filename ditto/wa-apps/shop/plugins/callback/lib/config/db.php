<?php
return array(
    'shop_callback' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'category_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'link_category' => array('text', 'null' => 0),
        'status' => array('tinyint', 4, 'null' => 0, 'default' => '0'),
        'datetime' => array('datetime', 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('id'),
        ),
        ':options' => array('engine' => 'MyISAM')
    ),
    'shop_callback_category_link' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'category_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'link_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'link' => array('varchar', 500),
        'name' => array('varchar', 300),
        'status' => array('tinyint', 4, 'null' => 0, 'default' => '0'),
        'datetime' => array('datetime', 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('id'),
        ),
        ':options' => array('engine' => 'MyISAM')
    ),
);
