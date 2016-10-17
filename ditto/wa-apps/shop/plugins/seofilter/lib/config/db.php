<?php

return array(
    'shop_seofilter_settings' => array(
        'storefront' => array('varchar', 255, 'null' => 0),
        'name' => array('varchar', 32, 'null' => 0),
        'value' => array('text', 'null' => 1),
        ':keys' => array(
            'PRIMARY' => array('storefront', 'name'),
        )
    ),
    'shop_seofilter_feature_values' => array(
        'storefront' => array('varchar', 255, 'null' => 0),
        'feature_id' => array('int', 11, null => 0),
        'value_id' => array('int', 11, null => 0),
        'category_id' => array('int', 11, null => 0),
        'name' => array('varchar', 32, 'null' => 0),
        'value' => array('text', 'null' => 1),
        'url' => array('varchar', 255, 'null' => 0),
        'priority' => array('int', 11, null => 0),
        ':keys' => array(
            'PRIMARY' => array('storefront', 'feature_id', 'value_id', 'category_id', 'name'),
        )
    )
);