<?php

$model = new waModel();

try {
    $model->exec("ALTER TABLE shop_product_reviews DROP `yes`, DROP `no`");

} catch (waException $e) {
}