<?php

$model = new waAppSettingsModel();
$key = array('shop', 'manager');

$auto_assign = $model->get($key, 'auto_assign', 1);

if ($auto_assign) {
    $model = new waModel();
    $model->exec("UPDATE shop_order JOIN (
              SELECT o.id order_id, l.id, l.contact_id
              FROM shop_order o
              JOIN shop_order_log l ON o.id = l.order_id
              WHERE l.contact_id != o.contact_id
              GROUP BY o.id
              HAVING l.id = MIN( l.id )
              ) AS t ON shop_order.id = t.order_id
              SET shop_order.manager_id = t.contact_id
              WHERE shop_order.manager_id = 0");
}
