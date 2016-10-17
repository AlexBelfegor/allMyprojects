<?php

class shopCallbackPlugin extends shopPlugin
{
    private $templatepaths = array();

    public function backend_order_info_section()
    {
		$return = array();

        $order_id = waRequest::get('id', null, waRequest::TYPE_INT);

        $order = array();
        $shipping_address = array();

        // Existing order?
        if ($order_id)
        {
            $order = $this->getOrder($order_id);

            /*$currency = $order['currency'];
            if ($order['contact_id']) {
                $has_contacts_rights = shopHelper::getContactRights($order['contact_id']);

                $shipping_address = shopHelper::getOrderAddress($order['params'], 'shipping');
            } else {
                $has_contacts_rights = shopHelper::getContactRights();
            }*/

            //wa_contact_data
	        $user_model = new shopCallbackPluginModel();
	        $user_data = $user_model->GetUserData($order['contact_id']);

	        $data = "";
	        $data = "<pre class='block double-padded s-order-comment' style='font-size:12px;'><div><h3>Данные пользователя:</h3>";

	        if(isset($order['contact']))
	        {
            	$data .= "<div>Имя: ".$order['contact']['name']."</div>";
	        	$data .= "<div>E-mail: ".$order['contact']['email']."</div>";
	        	$data .= "<div>Телефон: ".$order['contact']['phone']."</div>";
	        }

	        if(isset($user_data))
	        {
            	$data .= "<br /><h3>Дополнительные данные о пользователе:</h3>";

            	foreach($user_data as $val)
            	{
            		$data .= "<div>".$val['field'].": ".$val['value']."</div>";
            	}
	        }

            $data .= "</div></pre>";

		    $return['info_section'] = $data;
        }

		return $return;
    }

    private function getOrder($order_id)
    {
        $order_model = new shopOrderModel();
        $order = $order_model->getOrder($order_id, true, true);

        if (!$order) {
            throw new waException("Unknow order", 404);
        }
        $order['shipping_id'] = ifset($order['params']['shipping_id'], '').'.'.ifset($order['params']['shipping_rate_id'], '');

        $sku_ids = array();
        foreach ($order['items'] as $item) {
            foreach ($item['skus'] as $sku) {
                if (empty($sku['fake'])) {
                    $sku_ids[] = $sku['id'];
                }
            }
        }

        //$sku_stocks = $this->getSkuStocks(array_unique($sku_ids));
        $sku_ids = array_unique($sku_ids);
        if (!$sku_ids) {
            return array();
        }
        $product_stocks_model = new shopProductStocksModel();
        $sku_stocks = $product_stocks_model->getBySkuId($sku_ids);

        $subtotal = 0;
        $product_ids = array();

        foreach ($order['items'] as $i) {
            $product_ids[] = $i['id'];
            $subtotal += $i['item']['price'] * $i['item']['quantity'];
        }
        $order['subtotal'] = $subtotal;
        $product_ids = array_unique($product_ids);
        $feature_model = new shopFeatureModel();
        $f = $feature_model->getByCode('weight');
        if (!$f) {
            $values = array();
        } else {
            $values_model = $feature_model->getValuesModel($f['type']);
            $values = $values_model->getProductValues($product_ids, $f['id']);
        }

        foreach ($order['items'] as &$item) {
            if (isset($values['skus'][$item['item']['sku_id']])) {
                $w = $values['skus'][$item['item']['sku_id']];
            } else {
                $w = isset($values[$item['id']]) ? $values[$item['id']] : 0;
            }
            //$this->workupItems($item, $sku_stocks);
            $item['quantity'] = $item['item']['quantity'];
            $item['weight'] = $w;
        }
        unset($item);

        return $order;
    }

    /*category*/
    public function category_dialog()
    {
		$field = "";

		$category_id = waRequest::get('category_id');

		if($category_id)
		{
			$model = new shopCallbackCategoryPluginModel();

	    	$data = $model->GetCategoryLink($category_id);

	    	$field = '
				<div class="field-group">
				    <div class="field link_category">
				        <div class="name">
				            <label for="link_category">
				                Перелинковка категорий
				            </label>
				        </div>
				        <div class="value">
				            <textarea style="height: 100px;width:100%;" name="link_category">'.(($data['link_category'])?$data['link_category']:"").'</textarea>
				        </div>
				    </div>
				</div>
	    	';
        }

	    return $field;
    }

    public function category_save($params)
    {
	    $category_id = $params["id"];

	    if($category_id)
	    {
			$link_category = waRequest::post("link_category");

			$model = new shopCallbackCategoryPluginModel();

            $category_link = $model->GetCategoryLink($category_id);

            if($category_link)
            {
            	$model->UpdateCategoryLink($category_id,$link_category);
            }
            else
            {
            	$model->AddCategoryLink($category_id,$link_category);
            }
        }
    }

    //show linked categoryes
    public function GetLinkCategory($category_id)
    {
	    $html = "";

	    if($category_id)
	    {
			$model = new shopCallbackCategoryPluginModel();

            $data = $model->GetCategoryLink($category_id);

        	if($data)
        	{
            	$html = html_entity_decode($data["link_category"]);
        	}
        }

        return $html;
    }

    //show anachors block in category
    //public function frontend_nav($params)
    public function GetLinkCategorySidebar($category_id)
    {
	    $data = Array();

        $url = wa()->getConfig()->getHostUrl();

        $category_id = intval($category_id);

	    if($category_id)
	    {
			$model = new shopCallbackCategoryPluginModel();
			$links_data = $model->GetCategoryLinksSidebar($category_id);

			if($links_data)
			{
            	$data = $links_data;
			}
			else
			{
				$csv_data = self::_get_csv_data();

            	$data_rand = array_rand ( $csv_data, 5 );

	            foreach($data_rand as $i => $id)
	            {
                    $data[$i]['link_id'] = $id;
	                $data[$i]['link'] = $csv_data[$id]['link'];
	                $data[$i]['name'] = $csv_data[$id]['name'];
	            	$model->AddCategoryLinkSidebar($category_id,$id,$csv_data[$id]['link'],$csv_data[$id]['name']);
	          	}
	        }

        	if(count($data))
        	{
            	$html .= "<style>.link a{color: #9596A1;font-size: 14px;line-height: 16px;}.link a:hover{text-decoration:none;}</style><br /><div><p class='filtrname'>Популярные категории:</p>";

            	foreach($data as $i => $val)
            	{
            		$html .= "<div class='link'><a href='".$val['link']."#".$val['link_id']."-".$i++."'>".$val['name']."</a></div>";
            	}

            	$html .= "</div>";
        	}
        }

        return $html;
    }

    //fet csv data
    private function _get_csv_data()
    {
		$data_csv = Array();

        //path to file
        $file_name = wa()->getConfig()->getRootPath()."/promo/block_links/anchors.csv";

		if (($handle = fopen($file_name, "r")) !== FALSE)
		{
			$i = 0;

			while(($row = fgetcsv($handle, 1000, ";")) !== FALSE)
			{
				if($i)
				{
					if(count($row) != 2)
					{
						continue;
					}

	                $csv = Array();
	                $csv['link'] = $row[0];
	                $csv['name'] = iconv("windows-1251","utf-8",$row[1]);

					$data_csv[]	= $csv;
				}

				$i++;
			}

			fclose ($handle);
		}

		return $data_csv;
    }

    //link preve next in head
    public function SetPaginationRelInHead($total)
    {
	    $page = waRequest::get('page', 1);

	    if ($page < 1)
	    {
	    	$page = 1;
        }

	    $html = "";

	    if ($total < 2)
	    {
	    	return '';
        }

	    $url = wa()->getConfig()->getRequestUrl(false, true);

	    $get_params = waRequest::get();

	    if (isset($get_params['page']))
	    {
	        unset($get_params['page']);
	    }

	    $url_params = http_build_query($get_params);

	    if ($page > 1)
	    {
	        if($page > $total)
	        {
	        	$page = $total;
	        }

	        $page_url = $url.($url && $page == 2 ? ($url_params ? '?'.$url_params : '') : '?page='.($page - 1).($url_params ? '&'.$url_params : ''));
	        $html .= '<link rel="prev" href="'.$page_url.'">';
	    }

	    if ($page < $total) {
	        $page_url = $url.'?page='.($page + 1).($url_params ? '&'.$url_params : '');
	        $html .= '<link rel="next" href="'.$page_url.'">';
	    }

        return $html;
    }

    /*private functions*/
    private function getTemplatePaths()
    {
        if (!$this->templatepaths) {
            $this->templatepaths = array(
                'original' => $this->path . '/templates/PopupTemplate.html'
            );
        }
        return $this->templatepaths;
    }

    public function getTemplatePath()
    {
        foreach ($this->getTemplatePaths() as $filepath) {
            if (file_exists($filepath)) {
                return $filepath;
            }
        }
        return '';
    }

    public function getTemplatePopupPath($path)
    {
        $filepath = $this->path.'/templates/actions/popup/'.$path;

        if (file_exists($filepath))
        {
        	return $filepath;
        }

        return ;
    }

	public function getCronJob($name = null)
	{
        $config_path = $this->path.'/lib/config/cron.php';

        if (file_exists($config_path))
        {
            $data = include($config_path);
        }

	    return $name?(isset($data[$name])?$data[$name]:null):$data;
	}

}

