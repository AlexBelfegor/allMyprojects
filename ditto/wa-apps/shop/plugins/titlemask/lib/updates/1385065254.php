<?php

$replaces = array(
    '%stor_' => '%store_',
    '%article%' => '%sku%',
    '%goods%' => '%product%',
    '%group%' => '%category%'
);

// rename settings
$app_settings_model = new waAppSettingsModel();
$key = array('shop', 'titlemask');
$app_settings_model->set($key, 'product', $app_settings_model->get($key, 'goods'));
$app_settings_model->set($key, 'category', $app_settings_model->get($key, 'group'));
$app_settings_model->del($key, 'goods');
$app_settings_model->del($key, 'group');

$app_settings_model->set($key, 'product_description', '');
$app_settings_model->set($key, 'category_description', '');
$app_settings_model->set($key, 'index_description', '');
$app_settings_model->set($key, 'page_description', '');
$app_settings_model->set($key, 'tag_description', '');

// rename vars in setting values
$settings = $app_settings_model->get($key);
foreach ($settings as $k => $v) {
    $v = str_replace(array_keys($replaces), array_values($replaces), $v);
    $app_settings_model->set($key, $k, $v);
}