<?php
return array(
    'shop_bwredirect' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'url_from' => array('varchar', 1000),
        'url_to' => array('varchar', 1000),
        'redirect' => array('varchar', 10),
        'status' => array('tinyint', 4, 'null' => 0, 'default' => '0'),
        'datetime' => array('datetime', 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('id'),
        ),
        ':options' => array('engine' => 'MyISAM')
    ),
);
