<?php

$model = new waModel();

try {
    $model->query('SELECT url FROM shop_seofilter_feature_values WHERE 0');
} catch (waDbException $e) {
    $model->exec('ALTER TABLE shop_seofilter_feature_values ADD COLUMN url VARCHAR(255) NOT NULL AFTER value');
    shopSeofilterFeaturesModel::setAllUrls();
}
