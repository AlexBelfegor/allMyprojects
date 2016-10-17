<?php
return array(
    'shop_customernotes_notes' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'contact_id' => array('int', 11, 'null' => 0),
        'order_id' => array('int', 11, 'null' => 1),
        'create_contact_id' => array('int', 11, 'null' => 0),
        'create_datetime' => array('datetime'),
        'update_datetime' => array('datetime'),
        'note' => array('text', 'null' => 1),
        'pro_note_id' => array('int', 11),
        'rate' => array('tinyint', 1, 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => 'id',
        ),
    ),
);
