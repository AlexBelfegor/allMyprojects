<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopQuickorderPluginFrontendSendQuickorderController extends waJsonController
{

    public function execute()
    {
        // Действующие поля
        $active_fields = shopQuickorderPlugin::getFields();
        // Переданные поля
        $fields = waRequest::post('fields', array());
        $isCartSubmit = waRequest::post('isCartSubmit', '0', waRequest::TYPE_INT);
        $user_comment = waRequest::post('quickorder_user_comment', '', waRequest::TYPE_STRING_TRIM);
        // Если пользователь не авторизован, то создаем нового
        // иначе присваиваем заказ к текущему пользователю
        if (wa()->getUser()->isAuth()) {
            $contact = wa()->getUser();
        } else {
            $contact = new waContact();
        }

        $address_field = array();
        foreach ($fields as $field_id => $field) {
            // Если передано поле адреса
            if (strpos($field_id, "::")) {
                $parts = explode("::", $field_id);
                $field_type = $parts[0];
                if (strpos($field_type, '.') !== false) {
                    $parts2 = explode('.', $field_type, 2);
                    $field_type = $parts2[0];
                    $ext = "." . $parts2[1];
                } else {
                    $ext = null;
                }
                $field_id = $parts[1];
            } else {
                $field_type = "contact";
            }
            // Проверяем, действительно ли поле является действующим
            // если передано незнакомое поле - удаляем
            if ($this->isActiveField($field_id, $field_type, $active_fields)) {
                // Валидация поля email
                if ($field_id == 'email') {
                    $validator = new waEmailValidator();
                    if (is_array($field)) {
                        foreach ($field as $f) {
                            if (!$validator->isValid($f['value'])) {
                                $this->errors = _wp('Incorrect email');
                                return;
                            }
                        }
                    } else {
                        if (!$validator->isValid($field)) {
                            $this->errors = _wp('Incorrect email');
                            return;
                        }
                    }
                }
                // Если передан адрес, то формируем массив к сохранению
                // иначе обновляем поля контакта
                if ($field_type == 'address') {
                    $address_field[$field_type . $ext][$field_id] = $field;
                } else {
                    $contact->set($field_id, $field);
                }
            } else {
                unset($fields[$field_id]);
            }
        }
        // Сохраняем поля адреса
        if ($address_field) {
            foreach ($address_field as $addr_id => $addr_v) {
                $contact->set($addr_id, $addr_v);
            }
        }
        // Если пользователь авторизован - обновляем его данные
        if (wa()->getUser()->isAuth()) {
            $contact->save();
        }
        // Если поля прошли проверку
        if ($fields) {
            // Получаем настройки плагина
            $settings = shopQuickorderPlugin::getQuickorderSettings();
            // Формируем данные для создания нового заказа
            $order = array();
            $order['contact'] = $contact;

            $routing_url = wa()->getRouting()->getRootUrl();
            $order['params']['storefront'] = wa()->getConfig()->getDomain() . ($routing_url ? '/' . $routing_url : '');
            $order['params']['ip'] = waRequest::getIp();
            $order['params']['user_agent'] = waRequest::getUserAgent();

            $order['comment'] = $settings['comment'] . " " . $user_comment;
            // Поля адреса доставки
            if (isset($address_field['address.shipping'])) {
                foreach ($address_field['address.shipping'] as $k => $v) {
                    $order['params']['shipping_address.'.$k] = $v;
                }
            } elseif (isset($address_field['address'])) {
                foreach ($address_field['address'] as $k => $v) {
                    $order['params']['shipping_address.'.$k] = $v;
                }
            }

            $order['shipping'] = 0;
            // Формируем список товаров, включая переданные услуги
            if ($isCartSubmit) {
                $cart = new shopCart();
                $items['items'] = $cart->items(false);
                if (!$items['items']) {
                    $items['status'] = 'error';
                    $items['message'] = _wp('Your shopping cart is empty');
                } else {
                    // remove id from item
                    foreach ($items['items'] as &$item) {
                        unset($item['id']);
                        unset($item['parent_id']);
                    }
                    unset($item);
                    $cart_model = new shopCartItemsModel();
                    $code = $cart->getCode();
                    $not_available_items = $cart_model->getNotAvailableProducts($code, !wa()->getSetting('ignore_stock_count'));
                    if ($not_available_items) {
                        $items['status'] = 'error';
                        $items['message'] = "";
                        foreach ($not_available_items as $row) {
                            if ($row['available']) {
                                //$items['message'] .= "Выберите размер!<br><br>";
                                $items['message'] .= sprintf(_wp('For product %s only %d left in stock. Sorry. '), $items['items'][$row['id']]['product']['name'] . ($items['items'][$row['id']]['sku_name'] ? ' (' . $items['items'][$row['id']]['sku_name'] . ')' : ''), $row['count']) . "<br><br>";
                            } else {
                                $items['message'] .= sprintf(_wp('Oops! %s is not available for purchase at the moment. Please remove this product from your shopping cart to proceed. '), $items['items'][$row['id']]['product']['name'] . ($items['items'][$row['id']]['sku_name'] ? ' (' . $items['items'][$row['id']]['sku_name'] . ')' : '')) . "<br><br>";
                            }
                        }
                    } else {
                        $items['total'] = $cart->total(false);
                        $items['status'] = 'ok';
                    }
                }
            } else {
                $items = $this->getItems();
            }
            if ($items['status'] == 'error') {
                $this->errors = $items['message'];
            } else {
                $order['total'] = $items['total'];
                $order['items'] = $items['items'];
                $order['discount'] = shopDiscounts::apply($order);
                // Создаем новый заказ
                $workflow = new shopWorkflow();
                if ($order_id = $workflow->getActionById('create')->run($order)) {
                    $response_items = array();
                    $currency = wa('shop')->getConfig()->getCurrency(false);
                    foreach ($items['items'] as $k => $it) {
                        $response_items[] = array(
                            "id" => $k,
                            "name" => htmlspecialchars($it['name']),
                            "price" => ($it['currency'] !== $currency ? shopQuickorderPluginHelper::convertCurrency($it['price']) : $it['price']),
                            "currency" => $currency,
                            "quantity" => $it['quantity'],
                            "type" => $it['type'],
                        );
                    }
                    $this->response['params'] = array(
                        "order_id" => $order_id,
                        "total" => $isCartSubmit ? $order['total'] : shopQuickorderPluginHelper::convertCurrency($order['total']),
                        "currency" => $currency,
                        "items" => $response_items
                    );
                    $this->response['redirect'] = false;
                    if ($isCartSubmit) {
                        $cart->clear();
                        wa()->getStorage()->remove('shop/checkout');
                        wa()->getStorage()->set('shop/order_id', $order_id);
                        $this->response['redirect'] = wa()->getRouteUrl('/frontend/checkout', array('step' => 'success'));
                    } else {
                        if (strpos($settings['order_text'], '$order_id') !== false) {
                            $settings['order_text'] = str_replace('$order_id', shopHelper::encodeOrderId($order_id), $settings['order_text']);
                        }
                        $this->response['text'] = $settings['order_text'];
                    }
                } else {
                    $this->errors = _wp('Cannot create order');
                }
            }
        }
    }

    /**
     * Check, if field exists
     * @param string $field - value of field
     * @param string $field_type - type of the field: address or contact
     * @param array $active_fields - fields, which were added by admin
     * @return boolean
     */
    private function isActiveField($field, $field_type = "contact", $active_fields)
    {
        $active = false;
        foreach ($active_fields as $f) {
            if ($f['field_value'] == $field && $f['field_type'] == $field_type) {
                $active = true;
                break;
            }
        }
        return $active;
    }

    /**
     * Get items, services and total price
     * @return array
     */
    private function getItems()
    {
        $cart_model = new shopCartItemsModel();
        $code = waRequest::cookie('shop_cart');
        if (!$code) {
            $code = md5(uniqid(time(), true));
            wa()->getResponse()->setCookie('shop_cart', $code, time() + 30 * 86400, null, '', false, true);
        }
        $product = $sku = null;

        $data = waRequest::post();

        $sku_model = new shopProductSkusModel();
        $product_model = new shopProductModel();
        // Получаем id товара
        // Если в форме шаблона не было полей product_id или sku_id, то
        // берем данные, переданные через форму быстрого заказа
        $quickorder_product = waRequest::post('product');
        $product_id = isset($data['product_id']) ? $data['product_id'] : (isset($quickorder_product['product_id']) ? $quickorder_product['product_id'] : "");
        $sku_id_post = isset($data['sku_id']) ? $data['sku_id'] : "";

        if ($product_id) {
            $product = $product_model->getById($product_id);
            if ($sku_id_post) {
                $sku = $sku_model->getById($sku_id_post);
            } else {
                if (isset($data['features'])) {
                    $product_features_model = new shopProductFeaturesModel();
                    $sku_id = $product_features_model->getSkuByFeatures($product['id'], $data['features']);
                    if ($sku_id) {
                        $sku = $sku_model->getById($sku_id);
                    } else {
                        $sku = null;
                    }
                } else {
                    $sku = $sku_model->getById($product['sku_id']);
                    if (!$sku['available']) {
                        $sku = $sku_model->getByField(array('product_id' => $product['id'], 'available' => 1));
                    }
                }
            }
        }
        // Количество товаров
        $quantity = ifset($data['quantity'], 1);
        // Если переданный товар существует
        if ($product && $sku) {
            // Проверяем наличие товара на складе
            if (!wa()->getSetting('ignore_stock_count')) {
                $c = $cart_model->countSku($code, $sku['id']);
                if ($sku['count'] !== null && $c + $quantity > $sku['count']) {
                    $quantity = $sku['count'] - $c;
                    if (!$quantity) {
                        return(array("status" => "error", "message" => "Выберите размер!"));
                        //return(array("status" => "error", "message" => sprintf(_wp('Only %d left in stock. Sorry.'), $sku['count'])));
                    } else {
                        return(array("status" => "error", "message" => "Выберите размер!"));
                        //return(array("status" => "error", "message" => sprintf(_wp('Only %d left in stock. Sorry.'), $sku['count'])));
                    }
                }
            }
            // Создаем массив товаров
            $count = 0;
            $data = array(
                'code' => $code,
                'contact_id' => $this->getUser()->getId(),
                'product_id' => $product['id'],
                'sku_id' => $sku['id'],
                'create_datetime' => date('Y-m-d H:i:s'),
                'quantity' => $quantity
            );
            $items[$count] = $data;
            $items[$count]['type'] = 'product';
            $items[$count]['product'] = $product;
            $items[$count]['sku_code'] = $sku['sku'];
            $items[$count]['purchase_price'] = $sku['purchase_price'];
            $items[$count]['sku_name'] = $sku['name'];
            $items[$count]['currency'] = $items[$count]['product']['currency'];
            $items[$count]['price'] = $sku['price'];
            $items[$count]['name'] = $items[$count]['product']['name'];
            if ($items[$count]['sku_name']) {
                $items[$count]['name'] .= ' (' . $items[$count]['sku_name'] . ')';
            }
            // Общая стоимость заказа
            $total = $quantity * $items[$count]['price'];
            // Переданные сервисы к товару
            $services = waRequest::post('services', array());
            if ($services) {
                $variants = waRequest::post('service_variant');
                $temp = array();
                $service_ids = array();
                foreach ($services as $service_id) {
                    if (isset($variants[$service_id])) {
                        $temp[$service_id] = $variants[$service_id];
                    } else {
                        $service_ids[] = $service_id;
                    }
                }
                if ($service_ids) {
                    $service_model = new shopServiceModel();
                    $temp_services = $service_model->getById($service_ids);
                    foreach ($temp_services as $row) {
                        $temp[$row['id']] = $row['variant_id'];
                    }
                }
                $services = $temp;

                $service_model = new shopServiceModel();
                $servicesData = $service_model->getByField('id', array_keys($services), 'id');
                $service_variants_model = new shopServiceVariantsModel();
                $variants = $service_variants_model->getByField('id', array_values($services), 'id');

                foreach ($services as $service_id => $variant_id) {
                    $count++;
                    $data_service = array(
                        'service_id' => $service_id,
                        'service_variant_id' => $variant_id,
                        'type' => 'service'
                    );
                    $items[$count] = $data + $data_service;
                    $items[$count]['name'] = $items[$count]['service_name'] = $servicesData[$items[$count]['service_id']]['name'];
                    $items[$count]['currency'] = $servicesData[$items[$count]['service_id']]['currency'];
                    $items[$count]['service'] = $servicesData[$items[$count]['service_id']];
                    $items[$count]['variant_name'] = $variants[$items[$count]['service_variant_id']]['name'];
                    if ($items[$count]['variant_name']) {
                        $items[$count]['name'] .= ' (' . $items[$count]['variant_name'] . ')';
                    }

                    $product_services_model = new shopProductServicesModel();
                    $rows = $product_services_model->getByProducts($product['id']);
                    $product_services = $sku_services = array();
                    foreach ($rows as $row) {
                        if ($row['sku_id'] && $row['sku_id'] !== $product['sku_id']) {
                            continue;
                        }
                        if (!$row['sku_id']) {
                            $product_services[$row['product_id']][$row['service_variant_id']] = $row;
                        }
                        if ($row['sku_id']) {
                            $sku_services[$row['sku_id']][$row['service_variant_id']] = $row;
                        }
                    }

                    $items[$count]['price'] = $variants[$items[$count]['service_variant_id']]['price'];
                    if (isset($product_services[$items[$count]['product_id']][$items[$count]['service_variant_id']])) {
                        if ($product_services[$items[$count]['product_id']][$items[$count]['service_variant_id']]['price'] !== null) {
                            $items[$count]['price'] = $product_services[$items[$count]['product_id']][$items[$count]['service_variant_id']]['price'];
                        }
                    }
                    if (isset($sku_services[$items[$count]['sku_id']][$items[$count]['service_variant_id']])) {
                        if ($sku_services[$items[$count]['sku_id']][$items[$count]['service_variant_id']]['price'] !== null) {
                            $items[$count]['price'] = $sku_services[$items[$count]['sku_id']][$items[$count]['service_variant_id']]['price'];
                        }
                    }
                    $total += $items[$count]['price'];
                }
            }
            return(array("status" => "ok", "total" => $total, "items" => $items));
        } else {
            return(array("status" => "error", "message" => _wp('Product not found')));
        }
    }

}