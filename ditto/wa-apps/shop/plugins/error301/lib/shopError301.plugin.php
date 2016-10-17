<?php 
class shopError301Plugin extends shopPlugin
{
	static public function index()
	{
		$model = new shopError301Model();
		$model->index();
		
		$plugin_id = array('shop', 'error301');
		$application_settings_model = new waAppSettingsModel();
        $application_settings_model->set($plugin_id, 'index', time());
	}
	
	public function categorySave($category)
	{
		if(isset($category['parent_id']))
		{
			$model = new shopError301Model();
			$data = array(
				"id" => $category['id'],
				"type" => "c",
				"parent" => $category['parent_id'],
				"url" => $category['url'],
			);
			$model->insert($data, 1);
		}
		else //родитель передается только при создании категории
		{
			$model = new shopError301Model();
			$model->exec("INSERT IGNORE INTO `".$model->table."` SELECT `id`, 'c' as `type`, `url`, `parent_id` as `parent` FROM `shop_category` WHERE `id` = ".(int)$category['id'].";");
		}
	}
	
	public function categoryDelete($item)
	{
		$model = new shopError301Model();
		$model->deleteHistoryByID(array($item['id']), "c");
	}
	
	public function pageSave()
	{
		$get = waRequest::get();
		
		if(isset($get['action']) AND isset($get['module']) AND $get['action'] == 'pageSave' AND $get['module'] == 'product')
		{
			$model = new shopError301Model();
			if(isset($get['id']) AND $get['id'] > 0)
			{
				$post = waRequest::post();
				$data = array(
					"id" => $get['id'],
					"type" => "x",
					"url" => $post['info']['url'],
					"parent" => $get['product_id']
				);
				$model->insert($data, 1);
			}
			else
			{
				$model->exec("INSERT IGNORE INTO `".$model->table."` SELECT `id`, 'x' as `type`, `url`, `product_id` as `parent` FROM `shop_product_pages`;");
			}
		}
	}
	
	public function productSave($params)
	{
		$model = new shopError301Model();
		$data = array(
			"id" => $params['data']['id'],
			"type" => "p",
			"url" => $params['data']['url'],
			"parent" => $params['data']['category_id'],
		);
		$model->insert($data, 1);
	}
	
	public function productDelete($ids)
	{
		$model = new shopError301Model();
		$model->deleteHistoryByID($ids['ids'], "p");
		$model->deleteHistoryByID($ids['ids'], "x");
	}
	
	public function frontendError($params)
	{
		$model_settings = new waAppSettingsModel();
        $status = $model_settings->get($key = array('shop', 'error301'));
		
		if(!isset($status['index']) OR (time() - $status['index'] > 86400)) //некоторые изменения невозможно отследить, поэтому не чаще 1 раза в сутки индексируем
		{
			shopError301Plugin::index();
		}
		
		if ($params->getCode() == 404 AND isset($status['status']) AND $status['status']==1)
		{
            $redirect = $this->getRedirect();		
			
			if($redirect)
				wa()->getResponse()->redirect($redirect, 301);
        }
	}
	
	public function getRedirect()
	{
		
		$routing = wa()->getRouting();
		$curRouting = $routing->dispatch();
		$url = $routing->getCurrentUrl();
		$search = array(
			'reviews' => false, //принадлежность адреса к отзыву
			'category' => false, //принадлежность адреса к категории
			'product' => false, //принадлежность адреса к товару
			'productpage' => false, //принадлежность адреса подстранице товара
			'url' => '', //url искомого объекта
			'parents' => array() //список родителей искомого объекта
		);
		
		/*
		2.Естественный 
		Страницы товаров: /category-name/subcategory-name/product-name/
		Страница отзывов на товар: /category-name/subcategory-name/product-name/reviews/
		Подстраницы товаров: /category-name/subcategory-name/product-name/page-name/
		Страницы категорий: /category-name/subcategory-name/
		
		0.Смешанный 
		Страницы товаров: /product-name/
		Страница отзывов на товар: /product-name/reviews/
		Подстраницы товаров: /product-name/page-name/
		Страницы категорий: /category/category-name/subcategory-name/subcategory-name/...
		
		1.Плоский (WebAsyst Shop-Script) 
		Страницы товаров: /product/product-name/
		Страница отзывов на товар: /product/product-name/reviews/
		Подстраницы товаров: /product/product-name/page-name/
		Страницы категорий: /category/category-name/
		*/
		
		$expUrl = explode("/", $url);
		unset($expUrl[count($expUrl)-1]); //последний блок адреса тоже не нужен
		$expUrl = array_values($expUrl);
		
		if(isset($expUrl[count($expUrl) - 1]) && $expUrl[count($expUrl) - 1] == 'reviews') // если в конце адреса /reviews/, то это адрес отзыва продукта
		{
			unset($expUrl[count($expUrl) - 1]);
			$search['reviews'] = true;
			$search['product'] = true;
		}	
		
		if(isset($expUrl[0]) && $expUrl[0] == 'product') //товар, плоская адресация
		{
			unset($expUrl[0]);
			$expUrl = array_values($expUrl);
			$search['product'] = true;
			
			if(count($expUrl) == 2) //подстраница товара
			{
				$search['productpage'] = true;
				$search['parents'] = array($expUrl[0]); //url товара заносим в родителя
				$search['url'] = $expUrl[1];
			}
			elseif(count($expUrl) > 0)
			{
				$search['url'] = $expUrl[count($expUrl)-1]; //теоретически только один блок остался, но на случай совсем кривого адреса берем последний
			}			
			unset($expUrl);
		}	
		
		if(isset($expUrl[0]) AND $expUrl[0]=="category") //Категория при смешанной или плоской адресации
		{
			unset($expUrl[0]);
			$expUrl = array_values($expUrl);
			$search['category'] = true;
			$search['url'] = $expUrl[count($expUrl)-1];
			unset($expUrl[count($expUrl)-1]);
			
			if(count($expUrl) != 0)
			{
				$search['parents'] = $expUrl;
			}
			unset($expUrl);
		}
		
		if(isset($expUrl[0]))
		{
			$search['url'] = $expUrl[count($expUrl)-1];
			unset($expUrl[count($expUrl)-1]);
			$search['parents'] = $expUrl;
			unset($expUrl);
		}
		$model = new shopError301Model();
		
		/*
		$item = array(
			'type' => 'x', //тип элемента (x - подстраница товара, p - продукт, с - категория)
			'parent' => '', //родительская часть адреса
			'url0' => '', //адрес для смешанной адресации
			'url1' => '', //адрес для плоской адресации
			'url0' => '', //адрес для естественной адресации
			'rating' => '', //релевантность найденного элемента
		);*/

		$item = $model->getItem($search);
		
		if($item)
		{
			$base = wa()->getRouteUrl('shop/frontend', array(), true);
			$newUrl = $item['url'.$curRouting['url_type']];
			
			if($search['reviews'])
				return $base.$newUrl.'reviews/';
			
			return $base.$newUrl;
		}
		else
			return false;
	}
}