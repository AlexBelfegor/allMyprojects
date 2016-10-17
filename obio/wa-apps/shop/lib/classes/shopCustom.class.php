<?php

class shopCustom
{
    //sidebar categories list
    public static function sidebarcategories($category = array())
    {
        $category_model = new shopCustomCategoryModel();
        $where = "status = 1 and parent_id = 0";

        $id = 0;
        $cats = $category_model->getTree($id, $depth, $where);

        $data = Array();

        foreach ($cats as $i => &$c)
        {
            if($c['id'] == $category['id'])
            {
            	$c['show'] = true;
            }

            $c['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $c['url'] : $c['full_url']));

            $where = Array("parent_id > 0 and status = 1 and depth = 1");
            $c['subcategories'] = $category_model->getTree($c['id'], null, $where);

            foreach ($c['subcategories'] as &$item)
            {
	            if($item['id'] == $category['id'])
	            {
	            	$c['show'] = true;
	            	$item['show'] = true;
	            }

                $item['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $item['url'] : $item['full_url']));

            	$where = Array("parent_id > 0 and status = 1 and depth = 2");
	            $item['subcategories'] = $category_model->getTree($item['id'], null, $where);

	            foreach ($item['subcategories'] as &$subitem)
	            {
		            if($subitem['id'] == $category['id'])
		            {
		            	$c['show'] = true;
		            	$c['subcategories'][$subitem['parent_id']]['show'] = true;
		            	$subitem['show'] = true;
		            }

	                $subitem['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $subitem['url'] : $subitem['full_url']));
	            }

	            unset($subitem);
            }

            unset($item);

            $data[$i] = $c;
        }

        unset($c);

        return $data;
    }

    //get user mail
    public static function GetUserMail($id = 0)
    {
	    if($id)
	    {
	    	$user = new waContact($id);

	    	return $user->get('email', 'default');
	  	}
    }

    //get user mail
    public static function GetUserName($id = 0)
    {
	    if($id)
	    {
	    	$user = new waContact($id);

	    	return $user->get('name', 'default');
	  	}
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

    //last articles
    public static function GetLastArtcles($type_url = "",$limit = 3,$best = false)
    {
		$data = Array();

		$model = new shopCustomModel();

		$data = $model->GetLastArtcles($type_url,$limit,$best);

       	return $data;
    }

    //get categoryes for menu
    public static function categories($id = 0, $depth = 0, $short = false)
    {
        $limit = 11;
        $category_model = new shopCustomCategoryModel();
        $where = "status = 1";
        $cats = $category_model->getTree($id, $depth, $where);

        $data = Array();

        foreach ($cats as $i => &$c)
        {
	        // params
	        $category_params_model = new shopCategoryParamsModel();
	        $c['params'] = $category_params_model->get($c['id']);

            $c['alias'] = str_replace("-","_",$c['url']);
            $c['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $c['url'] : $c['full_url']));

            if(!$short)
            {
            	$where = Array("parent_id > 0 and status = 1 and depth = 1");
	            $c['subcategories'] = $category_model->getTree($c['id'], null, $where);

	            foreach ($c['subcategories'] as &$item)
	            {
	                $item['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $item['url'] : $item['full_url']));

			        // params
			        //$category_params_model = new shopCategoryParamsModel();
			        //$item['params'] = $category_params_model->get($item['id']);

	            	$where = Array("parent_id > 0 and status = 1 and depth = 2");
		            $item['subcategories'] = $category_model->getTree($item['id'], null, $where);

		            foreach ($item['subcategories'] as &$subitem)
		            {
		                $subitem['url'] = wa()->getRouteUrl('shop/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $subitem['url'] : $subitem['full_url']));
		            }

		            unset($subitem);
	            }

	            unset($item);
            }

        	$data[$i] = $c;
        }

        unset($c);

        /*print_r($data);exit;*/

        return $data;
    }

    //last viewed products
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

    //last viewed products
	public static function GetViewProduct()
	{
       	$data = "";

       	if(isset($_COOKIE['product_viewd']))
       	{
       		$data = $_COOKIE['product_viewd'];
       	}

       	return $data;
	}

    //last viewed products
    public static function MostPopularViewProducts($limit = 10)
    {
    	$result = Array();
    	$products = shopCustom::GetViewProduct();

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
	       	$price = $product['price'];
	       	$percent = ($price * 0.2);
	       	$where = "
	       		p.category_id = ".$product['category_id']." and
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

        return	$items;
    }

    //get image
    public static function productImgHtmlPath($product, $size)
    {
        $src .= ''.shopImage::getUrl(array('product_id' => $product['id'], 'id' => $product['image_id'], 'ext' => $product['ext']), $size).'';
        return $src;
    }

    //badge html
    public static function badgeHtml($data)
    {
    	if(count($data))
    	{         	if($data['id'] && !$data['type'])
         	{
            	$data['type'] = shopCustom::getFeaturesProd($data['id'],'osobennost_tovara');
         	}

         	if($data['compare_price'] > 0)
         	{         		$discount = 100-round(($data['price']/$data['compare_price'])*100);
         		$code = '<div class="badge low-price"><span>-'.$discount.'%</span></div>';         	}
         	else
         	{                switch($data['type'])
                {                	case "New":
                	{                		$code = '<div class="badge new"><span>'.$data['type'].'</span></div>';                	}break;
                	case "Хит":
                	{                		$code = '<div class="badge bestseller"><span>'.$data['type'].'</span></div>';
                	}break;                }
         	}    	}

    	return $code;
    }

    //get badge
    public static function getFeaturesProd($id = 0, $type_feature = '', $get_array = false)
    {
        $all_features = Array();
        $one_feature = "";

        if($id && $type_feature)
        {
        	$product_model = new shopProductModel();
        	$product = $product_model->getById($id);
	        $product = new shopProduct($product);

	        if($get_array == false)
	        {
	        	if (isset($product->features[$type_feature]))
	        	{
	        	    $one_feature = $product->features[$type_feature];
            	}
            }
            else
            {
            	if (count($product->features))
            	{
            		$all_features = $product->features;
            	}
            }
        }

        return $get_array?$all_features:$one_feature;
    }













    /*- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /*new*/
    /*- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/

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
	        	{		            $sort = waRequest::get('sort');
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

    //Get Shipping Checkout
    public static function GetShippingCheckout()
    {        $shipping_step = new shopCheckoutShipping();
        $shipping = $shipping_step->getRate();

        return $shipping;
    }

    //add coupon to session
    public static function addcouponetosession($code = "")
    {
    	if($code)
    	{	        //discount
	        $cart = new shopCart();
	        $items = $cart->items(false);
	        $subtotal = $cart->total(false);
	        $order = array('total' => $subtotal, 'items' => $items);
	        $discount = shopDiscounts::calculate($order);

        	if($discount)	$_SESSION['coupon'] = $code;    	}
    }

    //get coupon from session
    public static function getcouponetosession()
    {
    	$code = "";

    	if(isset($_SESSION['coupon'])) $code = $_SESSION['coupon'];

    	return $code;
    }

}