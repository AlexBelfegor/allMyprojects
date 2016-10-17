<?php
/**
 * @see https://api.privatbank.ua/article/4/
 *
 * @property-read string $merchant
 * @property-read string $pass
 */
class platonPayment extends waPayment implements waIPayment
{
    private $order_id;
    private $form_url = "https://secure.platononline.com/pcc.php?a=auth";
    private $pass_last = "EUyR1qRxpd9RQ7JX0EE5eGxHG6ruHUXq";

    public function payment($payment_form_data, $order_data, $auto_submit = false)
    {
        $view = wa()->getView();

        $order = waOrder::factory($order_data);

        $success = false;

        if(isset($order['id']))
        {
        	$transaction_model = new waTransactionModel();
            $transaction = $transaction_model->getPrimary($order['id']);

            if($transaction)
            {
		        $success = true;

				$i = 0;
				foreach($transaction as $val)
				{
					if($i > 0) break;
					$this->UpdateOrder($val["order_id"], $val["view_data"]);
					$i++;
                }

		        return $view->fetch($this->path.'/templates/success.html');
            }
            else
			{
				$transaction_cancel = $transaction_model->getCancelable($order['id']);

	            if($transaction_cancel)
	            {
			        $success = true;

					$i = 0;
					foreach($transaction_cancel as $val)
					{
						if($i > 0) break;
						$this->UpdateOrder($val["order_id"], $val["view_data"]);
						$i++;
	                }

			        return $view->fetch($this->path.'/templates/cancel.html');
	            }
            }
        }

        if(!$success)
        {
        	$view->assign('data', $payment_form_data);
	        $view->assign('order', $order_data);
	        $view->assign('settings', $this->getSettings());

	        $form = array();
	        $form['key'] = $this->merchant;
	        $form['url'] = $this->getRelayUrl().'?transaction_result=result';
	        $form['data'] = base64_encode(serialize(array('amount' => substr($order["total"], 0, -2),'name' => $order["description"],'currency' => $order["currency"])));
	        $form['sign'] = $this->verifySign($form);
	        $form['order'] = $order["id"];
	        $form['ext1'] = $this->app_id;
	        $form['ext2'] = $this->merchant_id;
	        $form['ext3'] = $order["id"];
	        $form['ext4'] = $order["contact_id"];
	        $form['error_url '] = $this->getAdapter()->getBackUrl(waAppPayment::URL_FAIL);

	        $view->assign('form', $form);
	        $view->assign('form_url', $this->form_url);

	        //$auto_submit = false;
	        $view->assign('auto_submit', $auto_submit);
	        return $view->fetch($this->path.'/templates/payment.html');
		}
    }

    public function UpdateOrder($id = 0, $comment = "")
    {
    	if($id && $comment)
    	{
    		$model_new = new waModel();
    		$model_new->exec("UPDATE shop_order SET comment = '".strip_tags($comment)."' WHERE id = ".$id."");
    	}
    }

    protected function callbackInit($request)
    {
        if(isset($_GET["order"]))
        {
            header("Location: /checkout/success/");
	        exit;
        }

        if (!empty($request['id']) && !empty($request['ext1']) && !empty($request['ext2']) && !empty($request['ext3']) && !empty($request['ext4']))
        {
            $this->app_id = $request['ext1'];
            $this->merchant_id = $request['ext2'];
            $this->order_id = $request['ext3'];
	       	//self::log($this->id, array('request_data' => var_export($request,1),'request' => var_export($_REQUEST,1),'post' => var_export($_POST,1),'get' => var_export($_GET,1)));
        }
        else
        {
            self::log($this->id, array('error' => 'empty required field(s)'));
            throw new waPaymentException('Empty required field(s)');
        }

        if(!$request)	$request = $_POST;

        $this->callbackHandler($request);
        //return parent::callbackInit($request);
    }

    protected function callbackHandler($request)
    {
        //self::log($this->id, array('request_data_2' => var_export($request,1),'request_2' => var_export($_REQUEST,1),'post_2' => var_export($_POST,1),'get_2' => var_export($_GET,1),'sign_2' => $this->verifySignCallback($request)));

        if (!$request['order'] || !$this->app_id || !$this->merchant_id)
        {
            throw new waPaymentException('invalid order number', 404);
        }

        if ($request['sign'] !== $this->verifySignCallback($request))
        {
            throw new waPaymentException('invalid signature', 404);
        }

        $transaction_data = $this->formalizeData($request);

		switch ($request['status'])
		{
			case 'SALE':
			{
				$text = "Был успешно проведен платеж по системе Platon номер заказа ".$request["order"].". Номер транзацкии ".$request["id"].".";
			}break;
			case 'REFUND':
			{
				$text = "Произошла отмена платежа по системе Platon номер заказа ".$request["order"].". Номер транзацкии ".$request["id"].".";
			}break;
			case 'CHARGEBACK':
			{
				$text = "Произошел chargeback платежа по системе Platon номер заказа ".$request["order"].". Номер транзацкии ".$request["id"].".";
			}break;
			default:
			{
				$text = "Произошел сбой платежа по системе Platon номер заказа ".$request["order"].". Номер транзацкии ".$request["id"].".";
			}break;
		}

		$transaction_data['view_data'] = $this->sendmail($text, $request);
        //self::log($this->id, array('transaction_data' => var_export($transaction_data,1),'request' => var_export($request,1)));

		if (!empty($transaction_data['state']))
        {
            $method = null;

            switch ($transaction_data['state'])
            {
                case 'SALE':
                {
                	$method = self::CALLBACK_PAYMENT;
	                $transaction_data['state'] = self::STATE_CAPTURED;
                }break;
                default:
                {
                    $method = self::CALLBACK_DECLINE;
                    $transaction_data['state'] = self::STATE_AUTH;
                    $transaction_data['type'] = self::OPERATION_CANCEL;
                }break;
            }

	        //self::log($this->id, array('transaction_data' => var_export($transaction_data,1),'request' => var_export($request,1),'method' => var_export($method,1)));

            $transaction_data = $this->saveTransaction($transaction_data, $request);

            if ($method)
            {
                $this->merchant_id = $transaction_data["ext2"];
                $result = $this->execAppCallback($method, $transaction_data);

                if (!empty($result['error']))
                {
	            	self::log($this->id, array('result' => var_export($result,1)));
	            }
            }
        }

        exit("OK");
    }

    protected function sendmail($text, $request)
    {
		$email_validator = new waEmailValidator();
		$subject = trim($text);

		$app_settings_model = new waAppSettingsModel();
		$email = $app_settings_model->get('webasyst', 'email');

		if (!isset($to))
		{
			$to = waMail::getDefaultFrom();
		}

		$body = "";
        $status = "";

		switch ($request['status'])
		{
			case 'SALE':
			{
				$status = "Успешно.";
			}break;
			case 'REFUND':
			{
				$status = "Возврат.";
			}break;
			case 'CHARGEBACK':
			{
				$status = "Требование возврата через банк.";
			}break;
			default:
			{
				$status = "Ошибка оплаты!";
			}
		}

        //self::log($this->id, array('request_new' => var_export($request,1)));

		$body .= "<div>Оформлен заказ номер ".$request["order"]."</div>";
		$body .= "<div>Статус заказа ".$status."</div>";
		$body .= "<div>Сумма ".$request["amount"]."</div>";
		$body .= "<div>Оплата с карты ".$request["card"]."</div>";
		$body .= "<div>Дата ".$request["date"]."</div>";
		$body .= "<div>Ip ".$request["ip"]."</div>";

		$m = new waMailMessage($subject, $body);

		$m->setTo($to);
		$m->setFrom(array($email => $email));

		if(!$m->send())
		{
	        self::log($this->id, array('error_mail' => var_export($m,1)));
		}

		return $body;
    }

    protected function formalizeData($request)
    {
        // формируем полный список полей, относящихся к транзакциям, которые обрабатываются платежной системой platon
        $fields = array(
            'id',
            'order',
            'status',
            'card',
            'description',
            'amount',
            'currency',
            'name',
            'date',
            'ip',
            'sign',
            'ext1',
            'ext2',
            'ext3',
            'ext4',
            'transaction_result',
        );
        foreach ($fields as $f) {
            if (!isset($request[$f])) {
                $request[$f] = null;
            }
        }

        // выполняем базовую обработку данных
        $transaction_data = parent::formalizeData($request);

        // номер заказа
        $transaction_data['order_id'] = $request['order'];

        // сумма заказа
        $transaction_data['customer_id'] = $request['ext4'];

        // сумма заказа
        $transaction_data['amount'] = $request['amount'];

        // сумма заказа
        $transaction_data['currency_id'] = $request['currency'];

        // сумма заказа
        $transaction_data['state'] = $request['status'];

        $transaction_data['type'] = self::OPERATION_AUTH_CAPTURE;

        //$transaction_data['view_data'] = "Номер транзакции в системе platon - ".$request['id']." .";

        return $transaction_data;
    }

    private function verifySign($request)
    {
		$sign = md5(strtoupper(
			strrev($_SERVER['REMOTE_ADDR']). /* not mandatory */
			strrev($request['key']).
			strrev($request['data']).
			strrev($request['url']).
			strrev($this->pass)
		));

        return $sign;
    }

    private function verifySignCallback($request)
    {
		$gen = strtoupper(
				strrev($request['email']) .
				$this->pass_last .
				$request['order'] .
				strrev(substr($request['card'], 0, 6) . substr($request['card'], -4))
		);

		//self::log($this->id, array('gen' => strrev($request['email'])."-".$this->pass_last."-".$request['order']."-".strrev(substr($request['card'], 0, 6) . substr($request['card'], -4))));

		$sign =  md5($gen);

        return $sign;
    }

    public function allowedCurrency()
    {
        return array('UAH');
    }

}
