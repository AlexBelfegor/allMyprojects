<?php

$model = new waModel();

try {
    $model->query("SELECT yes FROM shop_product_reviews WHERE 0");
} catch (waException $e) {
	$model->exec("ALTER TABLE shop_product_reviews ADD COLUMN yes INT NOT NULL DEFAULT '0'");
}

try {
    $model->query("SELECT no FROM shop_product_reviews WHERE 0");
} catch (waException $e) {
    $model->exec("ALTER TABLE shop_product_reviews ADD COLUMN no INT NOT NULL DEFAULT '0'");
}

