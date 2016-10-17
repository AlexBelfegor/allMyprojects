<?php

$model = new waModel();

try {
    $model->query("SELECT titlemask FROM shop_category WHERE 0");
    $model->exec("ALTER TABLE shop_category DROP titlemask");
} catch (waDbException $e) {
}

try {
    $model->query("SELECT descriptionmask FROM shop_category WHERE 0");
    $model->exec("ALTER TABLE shop_category DROP descriptionmask");
} catch (waDbException $e) {
}

try {
    $model->query("SELECT keywordsmask FROM shop_category WHERE 0");
    $model->exec("ALTER TABLE shop_category DROP keywordsmask");
} catch (waDbException $e) {
}