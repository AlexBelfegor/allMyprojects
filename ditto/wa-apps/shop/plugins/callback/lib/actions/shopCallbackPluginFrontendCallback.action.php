<?php

class shopCallbackPluginFrontendCallbackAction extends waViewAction
{
    public function execute()
    {
        $alias = waRequest::param('alias');

        switch($alias)
        {
        	case "product_know_size":{
        		$return_data = $this->product_know_size();
        	}break;
        	case "product_select_size":{
        		$return_data = $this->product_select_size();
        	}break;
        	case "product_preview":{
        		$return_data = $this->product_preview();
        	}break;
        	case "contact":{
        		$return_data = $this->contact();
        	}break;
        	case "phone":{
        		$return_data = $this->phone();
        	}break;
        	case "delete_item":{
        		$return_data = $this->delete_item();
        	}break;
        	case "search_data":{
        		$return_data = $this->search_data();
        	}break;
        	case "shipping":{
        		$return_data = $this->get_shipping();
        	}break;
        }

		if($return_data)
		{
			$this->json($return_data);
		}
    }

    //reserve not delete
    protected function product_select_size()
    {
        //template
    	$view = wa()->getView();
        $plugin = wa()->getPlugin('callback');

		$data = "";
		$this->view->assign('data', $data);
    	$content = $view->fetch($plugin->getTemplatePopupPath('product_select_size.html'));

        //show
        $this->view->assign('content', $content);
        $this->setTemplate($plugin->getTemplatePath());
    }

    protected function product_know_size()
    {
        //template
    	$view = wa()->getView();
        $plugin = wa()->getPlugin('callback');

		$data = "";
		$this->view->assign('data', $data);
    	$content = $view->fetch($plugin->getTemplatePopupPath('product_know_size.html'));

        //show
        $this->view->assign('content', $content);
        $this->setTemplate($plugin->getTemplatePath());
    }

    protected function product_preview()
    {
    	$product_id = trim(waRequest::get('product_id'));

        if($product_id)
        {
        	//product
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($product_id);
	        $product = new shopProduct($product);

	        if($product)
	        {
	        	//features
		        if ($product->sku_type == shopProductModel::SKU_TYPE_SELECTABLE) {
		            $features_selectable = $product->features_selectable;
		            $this->view->assign('features_selectable', $features_selectable);

		            $product_features_model = new shopProductFeaturesModel();
		            $sku_features = $product_features_model->getSkuFeatures($product->id);

		            $sku_selectable = array();
		            foreach ($sku_features as $sku_id => $sf) {
		                if (!isset($product->skus[$sku_id])) {
		                    continue;
		                }
		                $sku_f = "";
		                foreach ($features_selectable as $f_id => $f) {
		                    if (isset($sf[$f_id])) {
		                        $sku_f .= $f_id.":".$sf[$f_id].";";
		                    }
		                }
		                $sku = $product->skus[$sku_id];
		                $sku_selectable[$sku_f] = array(
		                    'id'        => $sku_id,
		                    'price'     => (float)shop_currency($sku['price'], $product['currency'], null, false),
		                    'available' => $product->status && $sku['available'] &&
		                        ($this->getConfig()->getGeneralSettings('ignore_stock_count') || $sku['count'] === null || $sku['count'] > 0),
		                    'image_id'  => (int)$sku['image_id']
		                );
		                if ($sku['compare_price']) {
		                    $sku_selectable[$sku_f]['compare_price'] = (float)shop_currency($sku['compare_price'], $product['currency'], null, false);
		                }
		            }
		            $product['sku_features'] = ifset($sku_features[$product->sku_id], array());
		            $this->view->assign('sku_features_selectable', $sku_selectable);
		        }

	        	/*if ($product->sku_type == shopProductModel::SKU_TYPE_SELECTABLE)
		        {
		            $product_features_selectable_model = new shopProductFeaturesSelectableModel();
		            $features_selectable = $product_features_selectable_model->getData($product);
	            }*/

	            //features all
	            $feature_codes = array_keys($product->features);
	            $feature_model = new shopFeatureModel();
	            $features = $feature_model->getByCode($feature_codes);

	            //furl
	            $category_model = new shopCategoryModel();
	            $category = $category_model->getById($product['category_id']);
                $product['category_url'] = waRequest::param('url_type') == 1 ? $category['url'] : $category['full_url'];
	            $product['frontend_url'] = wa()->getRouteUrl('/frontend/product', array('product_url' => $product['url'], 'category_url' => $product['category_url']));

		        //template
		    	$view = wa()->getView();
		        $plugin = wa()->getPlugin('callback');

	            $this->view->assign('features', $features);
		        $this->view->assign('features_selectable', $features_selectable);
		    	$this->view->assign('product', $product);
		    	$content = $view->fetch($plugin->getTemplatePopupPath('product_preview.html'));

		        //show
		        $this->view->assign('content', $content);
		        $this->setTemplate($plugin->getTemplatePath());
        	}
        }
    }

    protected function search_data()
    {
        $limit = waRequest::get('limit');
        $string = trim(waRequest::get('string'));

        $return_data['data'] = Array();
		$return_data['empty'] = "1";

        if($string && strlen($string) >= 1)
        {
        	$model = new shopCallbackPluginModel();
        	$category_model = new shopCategoryModel();

        	if($limit > 10) $limit = 10;

        	$data = $model->GetSearchWord($string,$limit);

			if($data)$return_data['empty'] = "0";

			foreach($data as $val)
			{
	            if($val['image_id'])
	            {
	            	$val['img'] = shopImage::getUrl(array('id' => $val['image_id'],'product_id' => $val['id'],'ext' => $val['ext']),'50x50');
                }
                else
                {
                	$val['img'] = "/wa-apps/shop/themes/ditto/img/dummy200.png";
                }

	            $category = $category_model->getById($val['category_id']);
	            $val['category_url'] = waRequest::param('url_type') == 1 ? $category['url'] : $category['full_url'];
				$val['full_url'] = wa()->getRouteUrl('/frontend/product', array('product_url' => $val['url'], 'category_url' => $val['category_url']));
                $val['price'] = shop_currency($val['price']).(($val['compare_price'] > $val['price'])?" <span class='katalog_oldprice'>".shop_currency($val['compare_price'])."</span>":"");

				$return_data['data'][] = $val;
			}
        }

		return $return_data;
    }

    protected function delete_item()
    {
        $id = waRequest::post('id');
        $cart = new shopCart();

        if ($id)
        {
            $cart->deleteItem($id);
        }

        $return_data['data']['total'] = shop_currency($cart->total());
        $return_data['data']['count'] = $cart->count();
        $return_data['data']['discount'] = shop_currency($cart->discount());

    	return $return_data;
    }

    protected function phone()
    {
        $return_data['status'] = "fail";

        if (waRequest::getMethod() == 'post')
        {
            $post = waRequest::post();

            //csrf check
            //if (trim(waRequest::cookie('_csrf', 2)) == trim(waRequest::post('_csrf', 1)))
            {
		    	if(isset($post['phone']))
		    	{
			        $phone = waRequest::post('phone');
			        $email_validator = new waEmailValidator();
			        $subject = trim(waRequest::post('subject', 'Пользователь с номером телефона '.$phone.' просит перезвонить ему.'));

			        if (!$phone)
			        {
			            $return_data['error']['phone'] = "Поле Телефон обязательно для заполнения";
			        }
			        elseif(strlen($phone) != 18)
                    {
			            $return_data['error']['phone'] = "Поле Телефон должно быть 11 цифр";
                    }

			        if (!$return_data['error'])
			        {
			            $app_settings_model = new waAppSettingsModel();
			            $email = $app_settings_model->get('webasyst', 'email');

				        if (!isset($to)) {
				            $to = waMail::getDefaultFrom();
				        }

		            	$m = new waMailMessage($subject, nl2br($email));
		            	$m->setTo($to);
		            	$m->setFrom(array($email => $email));

		            	if (!$m->send())
		            	{
		            	    //$return_data['status'] = "success";
		            	    $return_data['error']['all'] = _ws('An error occurred while attempting to send your request. Please try again in a minute.');
		            	}
		            	else
		            	{
		            	    $return_data['status'] = "success";
		            	}
			        }
		        }
            }
        }

		return $return_data;
    }

    protected function contact()
    {
        $return_data['status'] = "fail";

        if (waRequest::getMethod() == 'post')
        {
            $post = waRequest::post();

            //csrf check
            //if (trim(waRequest::cookie('_csrf', 2)) == trim(waRequest::post('_csrf', 1)))
            {
		    	if(isset($post['email']))
		    	{
			        $email = waRequest::post('email');
			        $id_category = intval(waRequest::post('category'));
			        $email_validator = new waEmailValidator();
			        $subject = trim(waRequest::post('subject', 'Пользователь '.$email.' подписался на рассылку.'));

			        if (!$email) {
			            $return_data['error']['email'] = "Обязательно для заполнения";
			        } elseif (!$email_validator->isValid($email)) {
			            $return_data['error']['email'] = implode(', ', $email_validator->getErrors());
			        }

			        if (!$return_data['error'])
			        {
			    		$model = new shopCallbackPluginModel();

				        $data = $model->GetUser($email);

				        if(!$data)
				        {
				        	$model->AddNewUser($email,$id_category);

				            $app_settings_model = new waAppSettingsModel();
				            $email = $app_settings_model->get('webasyst', 'email');

					        if (!isset($to)) {
					            $to = waMail::getDefaultFrom();
					        }

			            	$m = new waMailMessage($subject, nl2br($email));
			            	$m->setTo($to);
			            	$m->setFrom(array($email => $email));

			            	if (!$m->send())
			            	{
			            	    //$return_data['status'] = "success";
			            	    $return_data['error']['all'] = _ws('An error occurred while attempting to send your request. Please try again in a minute.');
			            	}
			            	else
			            	{
			            	    $return_data['status'] = "success";
			            	}
						}
						else
						{
		                	$return_data['error']['userexist'] = "Ваш e-mail уже есть в базе подписчиков.";
						}
			        }
		        }
            }
        }

		return $return_data;
    }

    //get shipping rate
    protected function get_shipping()
    {
        $shipping_step = new shopCheckoutShipping();
        $shipping = $shipping_step->getRate();

        //$return_data['shipping']['rate'] = shop_currency($shipping['rate']);
        $return_data['shipping']['rate'] = $shipping['rate']?$shipping['rate']:0;
        $return_data['shipping']['data'] = $shipping;

    	return $return_data;
    }

    //get json
    protected function json($return_data)
    {
		$return_data = json_encode($return_data);

		echo $return_data;
    }
}
