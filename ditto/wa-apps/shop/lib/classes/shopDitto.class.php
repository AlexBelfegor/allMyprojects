<?php

class shopDitto
{
    //last viewd products
    public static function MostPopularViewProducts($limit = 10)
    {
    	$result = Array();
    	$products = shopDitto::GetViewProduct();

        if($products)
        {
        	$collection = new shopProductsCollection();
        	$where = "p.id in (".$products.")";
        	$collection->addWhere($where);
			$result = $collection->getProducts('*', $limit);
        }

    	return $result;
    }

    //similar products
    public static function SimilarProductsFromCurrentCategory($product, $limit = 10)
    {
    	$result = Array();

        if($product)
       	{
	       	$product_model = new shopProductModel();
	       	$product = $product_model->getById($product['id']);
	        $product = new shopProduct($product);

	       	$price = $product['price'];
	       	$percent = ($price * 0.2);
	       	$where = "
	       		p.category_id = ".$product->category_id." and
	     		p.price >= ".round($price - $percent)." and p.price <= ".round($price + $percent)."
	     	";
            $orderBy = "id desc";

       		$collection = new shopProductsCollection();
            $collection->addWhere($where);
			$collection->orderBy($orderBy);
			$result = $collection->getProducts('*', $limit);
        }

    	return $result;
    }

	public static function AddViewProduct($productID)
	{
       	$product_viewd = "";
        $pos = false;

       	if(isset($_COOKIE['product_viewd']))
       	{
       		$product_viewd = $_COOKIE['product_viewd'];

       		$pos = strpos($product_viewd, ''.$productID.'');
       	}

       	if($product_viewd)	$product_viewd .= ",".$productID;
        else	$product_viewd = $productID;

       	if ($pos === false)	@setcookie("product_viewd", $product_viewd, time()+(60*60*24*360), "/");
	}

	public static function GetViewProduct()
	{
       	$data = "";

       	if(isset($_COOKIE['product_viewd']))
       	{
       		$data = $_COOKIE['product_viewd'];
       	}

       	return $data;
	}



    //get brand
    public static function getFeaturesProd($id = 0, $get_array = false)
    {
        $features = Array();

        $brand = "";

        if($id)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

	        if($get_array == false)
	        {
	        	if (isset($product->features['brand']))
	        	{
	        	    $brand = $product->features['brand'];
            	}
            }
            else
            {
            	if (count($product->features))
            	{
            		$features = $product->features;
            	}
            }
        }

        //echo $memory	= round(memory_get_usage() / 1024 / 1024, 2).'MB';;

        return $get_array?$features:$brand;
    }

    //get info table
    public static function GetLastShopTableInfo()
    {
        $data = Array();

       	$ditto = new shopDittoModel();

       	$data = $ditto->GetLastShopTableInfo();

        return ((isset($data['Auto_increment']) && $data['Auto_increment'])?$data['Auto_increment']:0);
    }

    //category url
    public static function GetCategoryCart($id = 0)
    {
        $category = "";

        if ($id)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

            $cat = Array();
            $category_model = new shopCategoryModel();
            $cat = $category_model->getById($product->category_id);

            if(count($cat))
            {
            	$category = $cat['url'];
            }
        }

        return $category;
	}

    //get feature color
    public static function getFeatureColorProduct($id = 0)
    {
        $color = "";

        if($id)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

	        if (count($product->features['color']))
	        {
	            $color = shopColorValue::convert("hex", $product->features['color']['code']);
            }
        }

        return $color;
    }

    //get feature video
    public static function getFeatureVideoProduct($id = 0)
    {
        $video = "";

        if($id)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

	        if (isset($product->features['youtube']) && @$product->features['youtube'] != "")
	        {
	            $video = $product->features['youtube'];
            }
        }

        return $video;
    }

    //get features
    public static function getFeaturesProductAvailable($id = 0)
    {
        $features_selectable = Array();

        if($id)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

	        if ($product->sku_type == shopProductModel::SKU_TYPE_SELECTABLE)
	        {
	            $features_selectable = $product->features_selectable;

                $product_features_model = new shopProductFeaturesModel();
	            $sku_features = $product_features_model->getSkuFeatures($product->id);

	            $sku_selectable = array();
	            $sku_selectable_ids = array();

	            foreach ($sku_features as $sku_id => $sf)
	            {
	                if (!isset($product->skus[$sku_id]))
	                {
	                    continue;
	                }

	                $sku_f = "";
	                $sku_f_id = "";
	                $sku_f_id_s = "";

                    foreach ($features_selectable as $f_id => $f)
	                {
	                    if (isset($sf[$f_id]))
	                    {
	                        $sku_f .= $f_id.":".$sf[$f_id].";";
	                        $sku_f_id .= $f_id;
	                        $sku_f_id_s .= $sf[$f_id];
	                    }
	                }

	                $sku = $product->skus[$sku_id];

	                if($product->status && $sku['available'] && ($sku['count'] === null || $sku['count'] > 0))
	                {
		                $sku_selectable[$sku_f] = array(
		                    'id'        => $sku_id,
		                    'price'     => (float)shop_currency($sku['price'], $product['currency'], null, false),
		                    'available' => $product->status && $sku['available'] && ($sku['count'] === null || $sku['count'] > 0),
		                    'image_id'  => (int)$sku['image_id']
		                );

		                $sku_selectable_ids[$sku_f_id_s] = $features_selectable[$sku_f_id]['values'][$sku_f_id_s];
	                }
	            }

	            $features_selectable['features_short'] = $sku_selectable_ids;
	            $features_selectable['features_full'] = $sku_selectable;
	        }

        }

        return $features_selectable;
    }

    //get features
    public static function getFeaturesProduct($id = 0)
    {
        $features_selectable = Array();

        if($id)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

	        if ($product->sku_type == shopProductModel::SKU_TYPE_SELECTABLE)
	        {
	            $product_features_selectable_model = new shopProductFeaturesSelectableModel();
	            $features_selectable = $product_features_selectable_model->getData($product);
            }
        }

        return $features_selectable;
    }

    //get image
    public static function productImgHtmlPath($product, $size)
    {
        $src .= ''.shopImage::getUrl(array('product_id' => $product['id'], 'id' => $product['image_id'], 'ext' => $product['ext']), $size).'';
        return $src;
    }

    //get images for product in category
    public static function getImages($product_id, $sizes = array(), $absolute = false)
    {
        if ($product_id) {
            $images_model = new shopProductImagesModel();
            if (empty($sizes)) {
                $sizes = 'crop';
            }
            $images = $images_model->getImages($product_id, $sizes, 'id', $absolute);
            return $images;
        } else {
            return array();
        }
    }

    //get products categoryes novinki, rasprodazha
    public static function products($category = Array())
    {
        $data = false;

        if(count($category))
        {
        	$orderBy = "id desc";

            switch($category['url'])
	        {
	        	case "nmuzhskaya":{$_GET['sex'][0] = "76";$where = "p.badge = 'new'";}break;
	        	case "nzhenskaya":{$_GET['sex'][0] = "77";$where = "p.badge = 'new'";}break;
	        	case "novinki":{$where = "p.badge = 'new'";}break;
	        	case "skidki":{$where = "p.badge = 'lowprice' and p.category_id = ".$category['parent_id']."";}break;
	        	case "rmuzhskaya":{$_GET['sex'][0] = "76";$where = "p.compare_price_selectable > 0";}break;
	        	case "rzhenskaya":{$_GET['sex'][0] = "77";$where = "p.compare_price_selectable > 0";}break;
	        	case "rasprodazha":{$where = "p.compare_price_selectable > 0";}break;
	        	case "poslednyaya-para":
	        	{
		            $sort = waRequest::get('sort');
		            if($sort)$orderBy = "price ".$sort;
		            // and p.compare_price_selectable > 0
	        		$where = "((select count(*) as cnt from shop_product_skus as s where s.product_id = p.id and s.count > 0 and s.name != 'One size') = 1)";
	        	}break;
	        }

	        $collection = new shopProductsCollection();
	        $collection->addWhere($where);
	        $collection->orderBy($orderBy);
            $collection->filters(waRequest::get());
	        $limit = wa()->getConfig()->getOption('products_per_page');

	        $page = waRequest::get('page', 1, 'int');
	        if ($page < 1) {
	            $page = 1;
	        }
	        $offset = ($page - 1) * $limit;

	        $products = $collection->getProducts('*', $offset, $limit);

	        $count = $collection->count();

	        $pages_count = ceil((float)$count / $limit);

	        // filters
	        if ($category['filter']) {
	            $filter_ids = explode(',', $category['filter']);
	            $feature_model = new shopFeatureModel();
	            $features = $feature_model->getById(array_filter($filter_ids, 'is_numeric'));
	            if ($features) {
	                $features = $feature_model->getValues($features);
	            }
	            $category_value_ids = $collection->getFeatureValueIds();

	            $filters = array();
	            foreach ($filter_ids as $fid) {
	                if ($fid == 'price') {
	                    $range = $collection->getPriceRange();
	                    if ($range['min'] != $range['max']) {
	                        $filters['price'] = array(
	                            'min' => shop_currency($range['min'], null, null, false),
	                            'max' => shop_currency($range['max'], null, null, false),
	                        );
	                    }
	                } elseif (isset($features[$fid]) && isset($category_value_ids[$fid])) {
	                    $filters[$fid] = $features[$fid];
	                    $min = $max = $unit = null;
	                    foreach ($filters[$fid]['values'] as $v_id => $v) {
	                        if (!in_array($v_id, $category_value_ids[$fid])) {
	                            unset($filters[$fid]['values'][$v_id]);
	                        } else {
	                            if ($v instanceof shopRangeValue) {
	                                $begin = $this->getFeatureValue($v->begin);
	                                if ($min === null || $begin < $min) {
	                                    $min = $begin;
	                                }
	                                $end = $this->getFeatureValue($v->end);
	                                if ($max === null || $end > $max) {
	                                    $max = $end;
	                                    if ($v->end instanceof shopDimensionValue) {
	                                        $unit = $v->end->unit;
	                                    }
	                                }
	                            } else {
	                                $tmp_v = shopDitto::getFeatureValue($v);
	                                if ($min === null || $tmp_v < $min) {
	                                    $min = $tmp_v;
	                                }
	                                if ($max === null || $tmp_v > $max) {
	                                    $max = $tmp_v;
	                                    if ($v instanceof shopDimensionValue) {
	                                        $unit = $v->unit;
	                                    }
	                                }
	                            }
	                        }
	                    }
	                    if (!$filters[$fid]['selectable'] && ($filters[$fid]['type'] == 'double' ||
	                        substr($filters[$fid]['type'], 0, 6) == 'range.' ||
	                        substr($filters[$fid]['type'], 0, 10) == 'dimension.')) {
	                        if ($min == $max) {
	                            unset($filters[$fid]);
	                        } else {
	                            $type = preg_replace('/^[^\.]*\./', '', $filters[$fid]['type']);
	                            if ($type != 'double') {
	                                $filters[$fid]['base_unit'] = shopDimension::getBaseUnit($type);
	                                $filters[$fid]['unit'] = shopDimension::getUnit($type, $unit);
	                                if ($filters[$fid]['base_unit']['value'] != $filters[$fid]['unit']['value']) {
	                                    $dimension = shopDimension::getInstance();
	                                    $min = $dimension->convert($min, $type, $filters[$fid]['unit']['value']);
	                                    $max = $dimension->convert($max, $type, $filters[$fid]['unit']['value']);
	                                }
	                            }
	                            $filters[$fid]['min'] = $min;
	                            $filters[$fid]['max'] = $max;
	                        }
	                    }
	                }
	            }

	            $data['filters'] = $filters;
	        }

	        $data['pages_count'] = $pages_count;
	        $data['products'] = $products;
            $data['collection_sql'] = $collection->getSQL();
		}

		return $data;
    }

    /**
     * @param shopDimensionValue|double $v
     * @return double
     */
    protected function getFeatureValue($v)
    {
        if ($v instanceof shopDimensionValue) {
            return $v->value_base_unit;
        }
        if (is_object($v)) {
            return $v->value;
        }
        return $v;
    }

    //last articles
    public static function GetLastArtcles($type_url = "",$limit = 3)
    {
		$data = Array();

		$ditto_model = new shopDittoModel();

		$data = $ditto_model->GetLastArtcles($type_url,$limit);

       	return $data;
    }

    //get categoryes for menu
    public static function categories($id = 0, $depth = 0, $short = false)
    {
        $limit = 11;
        $category_model = new shopDittoCategoryModel();
        $where = "status = 1";
        $cats = $category_model->getTree($id, $depth, $where);

        $data = Array();

        foreach ($cats as $i => &$c)
        {
            $category_features = Array();
            $category_features[$c['id']] = $c['id'];

            $c['alias'] = str_replace("-","_",$c['url']);
            $c['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $c['url'] : $c['full_url']));

            if(!$short)
            {
            	$where = Array("parent_id > 0 and status = 1");
	            $c['subcategories'] = $category_model->getTree($c['id'], null, $where);

	            foreach ($c['subcategories'] as &$item)
	            {
		            $category_features[$item['id']] = $item['id'];
	                $item['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $item['url'] : $item['full_url']));

			        // params
			        $category_params_model = new shopCategoryParamsModel();
			        $item['params'] = $category_params_model->get($item['id']);
	            }
	            unset($item);

	            $c['brand'] = $category_model->getAliasFeature("brand", $category_features,$limit);
	            $c['sizon'] = $category_model->getAliasFeature("sizon", $category_features,$limit);
	            $c['stil'] = $category_model->getAliasFeature("stil", $category_features,$limit);

	        	// params
	        	//$category_params_model = new shopCategoryParamsModel();
	        	//$c['params'] = $category_params_model->get($c['id']);
            }

        	$data[$i] = $c;
        }

        unset($c);

        return $data;
    }

    //category breadcrumbs
    public static function breadcrumbscategory($category = array())
    {
        if ($category['id'])
        {
            $category_model = new shopCategoryModel();
            $breadcrumbs = array();
            $path = $category_model->getPath($category['id']);
            sort($path);
            foreach ($path as $row)
            {
                $breadcrumbs[] = array(
                    'url' => wa()->getRouteUrl('/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $row['url'] : $row['full_url'])),
                    'name' => $row['name']
                );
            }

           	if ($breadcrumbs)
           	{
                return $breadcrumbs;
            }
        }

        return false;
    }

    //products breadcrumbs
    public static function breadcrumbs($product = array())
    {
        if ($product['category_id'])
        {
            $category_model = new shopCategoryModel();
            $category = $category_model->getById($product['category_id']);
            $product['category_url'] = waRequest::param('url_type') == 1 ? $category['url'] : $category['full_url'];
            $breadcrumbs = array();
            $path = $category_model->getPath($category['id']);
            sort($path);

            foreach ($path as $row)
            {
                $breadcrumbs[] = array(
                    'url' => wa()->getRouteUrl('/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $row['url'] : $row['full_url'])),
                    'name' => $row['name']
                );
            }

            $breadcrumbs[] = array(
                'url' => wa()->getRouteUrl('/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $category['url'] : $category['full_url'])),
                'name' => $category['name']
            );

            if ($product_link)
            {
                $breadcrumbs[] = array(
                    'url' => wa()->getRouteUrl('/frontend/product', array('product_url' => $product['url'], 'category_url' => $product['category_url'])),
                    'name' => $product['name']
                );
            }

           	if ($breadcrumbs)
           	{
                return $breadcrumbs;
            }
        }

        return false;
    }

    //cart popup on main
    public static function getSessionData($key = 1, $default = null)
    {
        $cart = new shopCart();

        $code = $cart->getCode();
        $cart_model = new shopCartItemsModel();
        $items = $cart_model->where('code= ?', $code)->order('parent_id')->fetchAll('id');

        $product_ids = $sku_ids = $service_ids = $type_ids = array();
        foreach ($items as $item) {
            $product_ids[] = $item['product_id'];
            $sku_ids[] = $item['sku_id'];
        }

        $product_ids = array_unique($product_ids);
        $sku_ids = array_unique($sku_ids);

        $product_model = new shopProductModel();
        $products = $product_model->getByField('id', $product_ids, 'id');

        $sku_model = new shopProductSkusModel();
        $skus = $sku_model->getByField('id', $sku_ids, 'id');
        $category_model = new shopCategoryModel();

        foreach ($items as &$item) {
            if ($item['type'] == 'product') {
	            $item['product'] = $products[$item['product_id']];
                $item['category'] = $category_model->getById($item['product']['category_id']);
                $sku = $skus[$item['sku_id']];
                $item['sku_name'] = $sku['name'];
                $item['price'] = $sku['price'];
                $item['currency'] = $item['product']['currency'];
                $type_ids[] = $item['product']['type_id'];
            }
        }
        unset($item);

        //discount
        /*$items2 = $cart->items(false);
        $subtotal = $cart->total(false);
        $order = array('total' => $subtotal, 'items' => $items2);
        $order['discount'] = shopDiscounts::calculate($order);*/

        return	$items;
    }

    //add coupon to session
    public static function addcouponetosession($code = "")
    {
    	if($code)
    	{
	        //discount
	        $cart = new shopCart();
	        $items = $cart->items(false);
	        $subtotal = $cart->total(false);
	        $order = array('total' => $subtotal, 'items' => $items);
	        $discount = shopDiscounts::calculate($order);

        	if($discount)	$_SESSION['coupon'] = $code;
    	}
    }

    //get coupon from session
    public static function getcouponetosession()
    {
    	$code = "";

    	if(isset($_SESSION['coupon'])) $code = $_SESSION['coupon'];

    	return $code;
    }

    public static function sidebarcategories($category = array())
    {
        $category_model = new shopDittoCategoryModel();
        $where = "status = 1 and parent_id = 0";

        $id = 0;
        $cats = $category_model->getTree($id, $depth, $where);

        $data = Array();

        foreach ($cats as $i => &$c)
        {
            if($c['id'] == $category['id'])
            {
            	$c['show'] = true;
            	$c['active'] = true;
            }

            $c['alias'] = str_replace("-","_",$c['url']);
            $c['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $c['url'] : $c['full_url']));

            $where = Array("parent_id > 0 and status = 1");
            $c['subcategories'] = $category_model->getTree($c['id'], null, $where);

            foreach ($c['subcategories'] as &$item)
            {
	            if($item['id'] == $category['id'])
	            {
	            	$c['active'] = true;
	            	$c['show'] = true;
	            	$item['show'] = true;
	            }

                $item['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $item['url'] : $item['full_url']));

		        // params
		        $category_params_model = new shopCategoryParamsModel();
		        $item['params'] = $category_params_model->get($item['id']);
            }

            unset($item);

            $data[$i] = $c;
        }

        unset($c);

        return $data;
    }

    public static function GetUserMail($id = 0)
    {
	    if($id)
	    {
	    	$user = new waContact($id);

	    	return $user->get('email', 'default');
	  	}
    }

    /**
     * Выводим текст о доставке
     */
    public static function SeoShieldDeliveryBlock()
    {
        if (function_exists("seo_shield_init_generate_content"))
        {
            $ss_content = seo_shield_init_generate_content(
                array(
                    'type' => 'delivery_block',
                    'markers' => array()
                    )
                );
            echo '<p>';
            $ss_content->start();
            echo '</p>';
        }
    }

    /**
     * Выводим текст о категории с продуктами
     */
    public static function SeoShieldCategoryBlock()
    {
        // вытягиваем uri
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '?') !== false)
            $uri = substr($uri, 0, strpos($uri, '?'));
        $uri = trim($uri, '/');

        // находим id и имя текущей категории
        $model = new shopCategoryModel();
        $categoryData = array_pop(
            $model->query("SELECT `id`, `name` FROM `shop_category` WHERE `full_url` = '$uri'")
                ->fetchAll()
        );

        // запомним имя категории
        $categoryName = $categoryData['name'];

        // если это категория 1 или 2, то нужно найти товары из их подкатегорий
        $additionalWhere = '';
        if ($categoryData['id'] == 1 || $categoryData['id'] == 2)
        {
            $categoryData = $model->query("SELECT `id` FROM `shop_category` WHERE `parent_id` = '" . $categoryData['id'] . "' AND `id` < '100' AND `id_1c` IS NOT NULL AND `id` != '42'")->fetchAll();

            $categoryData = $categoryData[array_rand($categoryData, 1)];
        }
        // если это новинки
        else if ($categoryData['id'] == 38 || $categoryData['id'] == 39)
        {
            // для мужчин
            if ($categoryData['id'] == 38)
                $parent_id = 1;
            // для женщин
            else if ($categoryData['id'] == 39)
                $parent_id = 2;

            $categoryData = $model->query("SELECT `id` FROM `shop_category` WHERE `parent_id` = '" . $parent_id . "' AND `id` < '100' AND `id_1c` IS NOT NULL")->fetchAll();

            $additionalWhere .= '(';
            foreach ($categoryData as $oneCategory)
            {
                $additionalWhere .= "'" . $oneCategory['id'] . "', ";
            }
            $additionalWhere = rtrim($additionalWhere, ', ');
            $additionalWhere .= ") and p.badge = 'lowprice'";
        }
        // если это скидки
        else if ($categoryData['id'] == 40 || $categoryData['id'] == 41 || $categoryData['id'] == 231
            || $categoryData['id'] == 32 || $categoryData['id'] == 33)
        {
            // для мужчин
            if ($categoryData['id'] == 40)
                $parent_id = "`parent_id` = '1'";
            // для женщин
            else if ($categoryData['id'] == 41)
                $parent_id = "`parent_id` = '2'";
            // последняя пара или общая категория
            else if ($categoryData['id'] == 231 || $categoryData['id'] == 32 || $categoryData['id'] == 33)
                $parent_id = "`parent_id` IN (1, 2)";

            $categoryData = $model->query("SELECT `id` FROM `shop_category` WHERE " . $parent_id . " AND `id` < '100' AND `id_1c` IS NOT NULL")->fetchAll();

            $additionalWhere .= '(';
            foreach ($categoryData as $oneCategory)
            {
                $additionalWhere .= "'" . $oneCategory['id'] . "', ";
            }
            $additionalWhere = rtrim($additionalWhere, ', ');
            $additionalWhere .= ") and p.count = '1'";
        }
        // для средств нужно подменить id
        else if ($categoryData['id'] == 44 || $categoryData['id'] == 47)
            $categoryData['id'] = 45;
        // для сертификатов тоже
        else if ($categoryData['id'] == 43)
            $categoryData['id'] = 42;

        // выбираем карточки товара
        $collection = new shopProductsCollection();

        if (strlen($additionalWhere) == 0)
            $collection->addWhere('p.category_id = ' . $categoryData['id']);
        else
            $collection->addWhere('p.category_id in ' . $additionalWhere);

        $products = $collection->getProducts('*');
        sort($products);

        // если карточек меньше 4, заполним пустыми полями
        if (!isset($products) || count($products) < 4)
        {
            for ($i = 0; $i < 4; $i++)
            {
                if (!isset($products[$i]))
                    $products[$i]['name'] = '';
            }
        }

        // перемешаем товары
        shuffle($products);

        // выводим текст, если есть товары
        if (function_exists("seo_shield_init_generate_content"))
        {
            $ss_content = seo_shield_init_generate_content(
                array(
                    'type' => 'category_block',
                    'markers' => array(
                            'cat_name' => $categoryName,
                            'product_1' => $products[0]['name'],
                            'product_2' => $products[1]['name'],
                            'product_3' => $products[2]['name'],
                            'product_4' => $products[3]['name'],
                        )
                    )
                );
            echo '<p>';
            $ss_content->start();
            echo '</p>';
        }
    }

    /**
     * Выводим текст на карточке товара
     */
    public static function SeoShieldProductBlock()
    {
        if (function_exists("seo_shield_init_generate_content"))
        {
            $ss_content = seo_shield_init_generate_content(
                array(
                    'type' => 'product_block',
                    'markers' => array()
                    )
                );
            echo '<p>';
            $ss_content->start();
            echo '</p>';
        }
    }
}