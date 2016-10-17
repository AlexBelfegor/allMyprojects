<?php

$model = new waModel();
$app_settings_model = new waAppSettingsModel();
if ($app_settings_model->get(array('shop', 'titlemask'), 'category_mask')) {
    try {
        $model->query("SELECT keywordsmask FROM shop_category WHERE 0");
    } catch (waDbException $e) {
        $model->query("ALTER TABLE shop_category ADD keywordsmask TEXT NULL");
    }
    try {
        $cache = new waSystemCache('db/shop_category');
        $cache->delete();
    } catch (waException $e) {
    }
}
