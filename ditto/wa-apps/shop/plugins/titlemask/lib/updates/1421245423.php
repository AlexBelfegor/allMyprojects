<?php

$model = new waModel();
$app_settings_model = new waAppSettingsModel();
$plugin_key = array('shop', 'titlemask');

// remove setting product_low
if ($app_settings_model->get($plugin_key, 'product_low')) {
    foreach (array('title', 'description', 'keywords') as $k) {
        $k = 'product'.($k == 'title' ? '' : '_'.$k);
        $mask = $app_settings_model->get($plugin_key, $k);
        if ($mask) {
            $mask = str_replace('%product%', '%product|lower%', $mask);
            $app_settings_model->set($plugin_key, $k, $mask);
        }
    }
}
$app_settings_model->del($plugin_key, 'product_low');

// remove setting category_low
if ($app_settings_model->get($plugin_key, 'category_low')) {
    foreach (array('title', 'description', 'keywords') as $k) {
        $k = 'category'.($k == 'title' ? '' : '_'.$k);
        $mask = $app_settings_model->get($plugin_key, $k);
        if ($mask) {
            $mask = str_replace('%category%', '%category|lower%', $mask);
            $app_settings_model->set($plugin_key, $k, $mask);
        }
    }
}
$app_settings_model->del($plugin_key, 'category_low');