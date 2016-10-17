<?php

class shopQuickorderPluginFrontendActions extends waJsonActions
{
    public function quickorderAction()
    {
        $productId = waRequest::get('productId');
        $response = array(
            'status' => 'fail',
        );
        if ($productId)
        {


            $productModel = new shopProductModel();
            $product = $productModel->getById($productId);

            $name = waRequest::post('name');
            $phone = waRequest::post('phone');

            $errors = array();
            if (!strlen($name))
            {
                $errors['name'] = 1;
            }

            if (!strlen($phone))
            {
                $errors['phone'] = 1;
            }
            $plugin = wa()->getPlugin('quickorder');
            $view = wa()->getView();

            if (!sizeof($errors))
            {
                $title = false;

                if (!$title) {
                    $title = $this->getConfig()->getGeneralSettings('name');
                }
                if (!$title) {
                    $app = wa()->getAppInfo();
                    $title = $app['name'];
                }

                $price = $product['price'];

                $orderId = '#fast_'.uniqid();
                $result = "_gaq.push(['_addTrans', '" . $orderId . "', '" . htmlspecialchars($title) . "', '" . $price . "', '',  '',  '',  '', '']);\n";
                $result .= "_gaq.push(['_addItem', '" . $orderId . "', '" . $product['sku_id'] . "', '" . htmlspecialchars($product['name']) . "', '', '" . $product['price'] . "',  '1']);\n";
                $result .= "_gaq.push(['_set', 'currencyCode', '" . $product['currency'] . "']);\n";
                $result .= "_gaq.push(['_trackTrans']);\n";

                $response['conversion_js'] = $result;

                $html = $view->fetch($plugin->getTemplatePath('QuickOrderOk.html'));
                $response['confirm_window_html'] = $html;
                $response['status'] = 'ok';

                if ($notifyEmail = $plugin->getSettings('notify_email'))
                {

                    $emailsToNotify = explode(',', $notifyEmail);
                    $mainEmail = array_shift($emailsToNotify);

                    $view = wa()->getView();
                    $view->assign('user_name', $name);
                    $view->assign('user_phone', $phone);
                    $view->assign('product', $product);
                    $body = $view->fetch($plugin->getTemplatePath('QuickOrderAdminEmail.html'));
                    $mail_message = new waMailMessage('Уведомление о быстром заказе '.$orderId, $body);

                    $mail_message->setFrom('noreply@vip-bag.ru', 'Робот магазина '.$title);
                    $mail_message->setTo($mainEmail);
                    foreach ($emailsToNotify as $email)
                    {
                        $mail_message->addBcc($email);
                    }
                    $mail_message->send();
                }
            }
            else
            {
                $response['errors'] = $errors;
                $response['status'] = 'fail';
            }
        }
        $this->response = $response;
    }
}

