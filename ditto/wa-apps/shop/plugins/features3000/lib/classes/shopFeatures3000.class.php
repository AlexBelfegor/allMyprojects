<?php

class shopFeatures3000
{
    public $productsIds = array();
    public $featuresIds = array();
    public $tree = array();
    public $filteredProductIds = array();
    public $filters = array();
    public static $model = false;
    public function __construct($collectionData, $filters, $collectionType)
    {
        $this->collectionType = $collectionType;
        $this->collectionData = $collectionData;

        //$this->filters = $filters;
        $this->minPrice = false;
        $this->maxPrice = false;
        $this->minPriceCurrent = false;
        $this->maxPriceCurrent = false;
        $this->setWaFilters($filters);

        if ($collectionData and $this->filters)
        {
            $this->prepareTree();
        }
    }
    public function setWaFilters($filters)
    {
        if(is_array($filters))
        foreach ($filters as $filterId => $filterData) {
            if ($filterId == 'price')
            {
                continue;
            }
            elseif ($filterData['type'] == 'varchar' || $filterData['type'] == 'boolean' || $filterData['type'] == 'color')
            {
                continue;
            }
            else
            {
                unset($filters[$filterId]);
            }
        }
        $this->filters = $filters;
    }
    public function prepareTree()
    {
        try
        {
            if ($this->collectionType == 'category')
            {
                $collection = new shopProductsCollection('category/'.$this->collectionData);
                $collection->setOptions(array('filters' => false));
                $sql = $collection->getSQL();
            }
            elseif ($this->collectionType == 'custom_sql')
            {
                $sql = $this->collectionData;
            }

            //var_dump();
            $sql = 'SELECT p.id '.$sql;
            $model = new shopProductModel();
            $productsIds = array_keys($model->query($sql)->fetchAll('id'));

            $sql = "
                SELECT
                  spf.*,
                  sf.code as feature_code,
                  sf.type as ftype,
                  p.min_price as price_min,
                  p.max_price as price_max
                FROM
                  `shop_product_features` spf
                LEFT JOIN
                   `shop_feature` sf
                   ON
                   sf.id = spf.feature_id
                LEFT JOIN
                    `shop_product` p
                    ON
                    spf.product_id = p.id
                WHERE
                  `product_id` IN (".join(', ', $productsIds).")
            ";

            if (!$this->hasPriceFilterOnly())
            {
                $sql.= "AND
                  `feature_id` IN (".join(', ', array_keys($this->filters)).")";
            }

            /*bw sort*/
            $sort = waRequest::post('sort');
            $order = waRequest::post('order');

            if($sort && $order)
            {
            	$sql .= "order by p.".$sort." ".$order."";
            }

            $result = $this->query($sql);

            if ($result->count() > 0)
            {
                $this->tree = array();
                foreach ($result->fetchAll() as $productFeature)
                {
                    if (!is_array($this->tree[$productFeature['feature_id']]))
                    {
                        $this->tree[$productFeature['feature_id']] = array(
                            'values' => array(),
                            'code' => $productFeature['feature_code'],
                            'type' => $productFeature['ftype']
                        );
                    }
                    if (!is_array($this->tree[$productFeature['feature_id']]['values'][$productFeature['feature_value_id']]))
                    {
                        $this->tree[$productFeature['feature_id']]['values'][$productFeature['feature_value_id']] = array();
                    }
                    $this->tree[$productFeature['feature_id']]['values'][$productFeature['feature_value_id']][$productFeature['product_id']] = array('price_min' => $productFeature['price_min'], 'price_max' => $productFeature['price_max']);
                    if ($this->minPrice == false or $productFeature['price_min'] < $this->minPrice)
                    {
                        $this->minPrice = $productFeature['price_min'];
                    }
                    if ($productFeature['price_max'] > $this->maxPrice)
                    {
                        $this->maxPrice = $productFeature['price_max'];
                    }
                }
                $this->minPriceCurrent = $this->minPrice;
                $this->maxPriceCurrent = $this->maxPrice;
            }
        } catch (waDbException $e)
        {
            //var_dump($e->getMessage());
        }

    }
    public static function getModel()
    {
        if (!self::$model)
        {
            self::$model = new waModel();
        }
        return self::$model;
    }
    public function query($sql, $params = array())
    {
        $model = self::getModel();
        return $model->query($sql, $params);
    }
    public function filterByPrice($priceMin = 0, $priceMax = false)
    {
        if (array_key_exists('price', $this->filters))
        {
            $this->minPriceCurrent = 0;
            $this->maxPriceCurrent = 0;
            foreach ($this->tree as $filterId => $filterValue)
            {
                foreach ($filterValue['values'] as $filterValueId => $products)
                {
                    foreach ($products as $productId => $productData)
                    {
                        if (!( ($priceMin > 0 ? $productData['price_max'] >= (int) $priceMin : true) and ($priceMax ? $productData['price_max'] <= (int) $priceMax : true)))
                        {
                            unset($this->tree[$filterId]['values'][$filterValueId][$productId]);
                        }
                        else
                        {
                            if ($this->minPriceCurrent == false or $productData['price_min'] < $this->minPriceCurrent)
                            {
                                $this->minPriceCurrent = $productData['price_min'];
                            }
                            if ($productData['price_max'] > $this->maxPriceCurrent)
                            {
                                $this->maxPriceCurrent = $productData['price_max'];
                            }
                        }
                    }
                }
            }
        }
    }
    public function getFiltratedFilters($filterInput)
    {
        $filters3000 = $this->getFilters();
        if(is_array($this->filters))
        foreach ($this->filters as $id => $filterData) {
            $code = $filterData['code'];
            $getData = $filterInput;
            if (array_key_exists($code, $getData))
            {
                $filterQueries = $getData[$code];

                if (is_array($filterQueries))
                {
                    foreach ($filterQueries as $value) {
                        if (strlen($value) > 0)
                        {
                            $filters3000->q($code, $value, true);
                        }
                    }
                }
                else
                {
                    if (strlen($filterQueries) > 0)
                    {
                        $filters3000->q($code, $filterQueries, true);
                    }
                }
            }
        }

        return $filters3000;
    }
    public function calcMaxMinPriceByIds($ids)
    {
        $this->minPriceCurrent = false;
        $this->maxPriceCurrent = false;
        foreach ($this->tree as $filterId => $filterValue)
            {
                foreach ($filterValue['values'] as $filterValueId => $products)
                {
                    foreach ($products as $productId => $productData)
                    {
                        if (false === array_search($productId, $ids))
                        {
                            //unset($this->tree[$filterId]['values'][$filterValueId][$productId]);
                        }
                        else
                        {
                            if ($this->minPriceCurrent == false or $productData['price_min'] < $this->minPriceCurrent)
                            {
                                $this->minPriceCurrent = $productData['price_min'];
                            }
                            if ($productData['price_max'] > $this->maxPriceCurrent)
                            {
                                $this->maxPriceCurrent = $productData['price_max'];
                            }
                        }
                    }
                }
            }

    }
    public function getFilters()
    {
        $filters = array();
        if(is_array($this->filters))
        foreach ($this->filters as $filterId => $filterData) {
            if (is_array($filterData)) {
                $values = array();

                if(is_array($filterData['values']))
                foreach ($filterData['values'] as $valueId => $value) {


                    $fv = new SlabFilterValue($valueId, $this->tree[$filterId]['values'][$valueId] ? array_keys($this->tree[$filterId]['values'][$valueId]) : array());
                    $fv->data['type'] = $filterData['type'];
                    if ($filterData['type'] == 'color')
                    {
                        $fv->data['color'] = $value->rgb;
                        $fv->data['color_value'] = $value;
                    }

                    $fv->data['name'] = (string)$value;
                    $values[] = $fv;
                }
                $f = new SlabFilter($this->tree[$filterId]['code'], $values, $filterData['multiple'] == 1);
                $f->type = $this->tree[$filterId]['type'];
                $f->data = $filterData;
                $filters[] = $f;
            }
        }
        $filtersCollection = new SlabFiltersCollection($filters);
        return $filtersCollection;
    }
    public function hasPriceFilter()
    {
        return @array_key_exists('price', $this->filters);
    }
    public function hasPriceFilterOnly()
    {
        return (array_key_exists('price', $this->filters) && (sizeof($this->filters) == 1));
    }
    public static function putInStorage($key, $value)
    {
        wa()->getStorage()->write($key, $value);
    }
    public static function getFromStorage($key)
    {
        return wa()->getStorage()->read($key);
    }
    public static function injectJs($elementSelector = false, $collectionData, $filters = array(), $collectionType = 'category')
    {

        $url = wa()->getRouteUrl('shop/frontend/filter');

        $requestData = array('elementSelector' => $elementSelector);
        $requestData['collectionType'] = $collectionType;

        /*$requestData['sort'] = waRequest::get("sort");
        $requestData['order'] = waRequest::get("order");*/

        if ($collectionType == 'category')
        {
            $categoryId = $collectionData;
            $requestData['categoryId'] = $categoryId;
            $storageKey = 'filters_category_'.$categoryId.'_'.md5($elementSelector);
        }
        elseif ($collectionType == 'custom_sql')
        {
            $storageKey = 'filters_category_custom_'.md5($elementSelector);
        }

        self::putInStorage($storageKey, serialize(array($filters, $collectionData)));

        $postData = json_encode(waRequest::get());

        $rqUrl = $url.'?'.http_build_query($requestData);

        /*bw*/
        $rqUrl = str_replace("&amp;","&",$rqUrl);

        $filtersJs = <<<EOJS
<script type="text/javascript">
         $(document).ready(function() {
            $('$elementSelector').load('$rqUrl', $postData, function(resp, status, xhr) {
                $(document).trigger(jQuery.Event('f3000_loaded'));
            });
         });
</script>
EOJS;
        return $filtersJs;

    }

}