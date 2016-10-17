<?php


class shopFeatures3000PluginFrontendFilterAction extends waViewAction
{

    public function execute()
    {
        $hack = new shopFeatureValuesBooleanModel();
        $plugin = wa()->getPlugin('features3000');

        //xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        $collectionType = waRequest::get('collectionType');
        $elementSelector = waRequest::get('elementSelector');

        if ($collectionType == 'category')
        {
            $categoryId = waRequest::get('categoryId');
            $storageKey = 'filters_category_'.$categoryId.'_'.md5($elementSelector);
        }
        elseif ($collectionType == 'custom_sql')
        {
            $storageKey = 'filters_category_custom_'.md5($elementSelector);
        }
        list($filters, $collectionData) = unserialize(shopFeatures3000::getFromStorage($storageKey));

    	$features3000 = new shopFeatures3000($collectionData, $filters, $collectionType);

        $rqData = waRequest::post();
        $conditions = array();
        $config = wa()->getConfig();
        if ($features3000->hasPriceFilter())
        {
            $filters3000 = $features3000->getFiltratedFilters($rqData);

            $currentProducts = $filters3000->getProducts();
            if (!$features3000->hasPriceFilterOnly())
            {
                $features3000->calcMaxMinPriceByIds($currentProducts);
            }


        	$priceMin = waRequest::post('price_min', 0);
        	$priceMax = waRequest::post('price_max', false);


        	if ($priceMin or $priceMax)
        	{

                $conditions[] = array('type' => 'price', 'filter' => false, 'min' => $priceMin, 'max' => $priceMax);

                $features3000->filterByPrice($priceMin, $priceMax);
                if ($plugin->getSettings('enabled_price_hack') == true)
                {
                    $priceMin = floor(shop_currency($priceMin, false, false, false));
                    $priceMax = ceil(shop_currency($priceMax, false, false, false));
                }
                $this->view->assign('priceMinFilter', $priceMin);
                $this->view->assign('priceMaxFilter', $priceMax);
        	}


            if ($plugin->getSettings('enabled_price_hack') == true)
            {
                $currencyKey = $config->getCurrency(false);
                $currencyCurrent = $config->getCurrencies(array($currencyKey));
                $currencyRate = $currencyCurrent[$currencyKey]['rate'];
                $this->view->assign('filters3000_currency_rate', number_format($currencyRate, 5));
            }
            else
            {
                $this->view->assign('filters3000_currency_rate', 1);
            }
        }

        $filters3000 = $features3000->getFiltratedFilters($rqData);
        foreach ($filters3000->filters as $filterId => $filter)
        {
            if ($filter->hasSelected())
            {
                foreach ($filter->selectedList as $valueId) {
                    $val = $filter->getValue($valueId);
                    if ($val)
                    {
                        $conditions[] = array('type' => 'filter_value', 'filter' => $filter->data['name'], 'value' => $val->data['name'], 'filterId' => $filter->id, 'valueId' =>  $valueId);
                    }
                }
            }
        }

        $ur = http_build_query(waRequest::get());
        /*bw*/
        $ur = str_replace("&amp;","&",$ur);

        $url = wa()->getRouteUrl('shop/frontend/filter').'?'.$ur;


        $this->view->assign('filters3000_url', $url);
        $this->view->assign('filters3000', $filters3000);
        $this->view->assign('features3000', $features3000);
        $this->view->assign('f3000_conditions',  $conditions);
        $this->view->assign('f3000_elements_selector', $elementSelector);

        /**$xhprof_data = xhprof_disable();
        $xhprof_runs = new XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_testing");
        // Формируем ссылку на данные профайлинга и записываем ее в консоль
        echo  "http://tools.lan/xh/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";**/

        if ($plugin->getSettings('enabled_theme_template') == true)
        {
            $this->setThemeTemplate('filter3000.html');
        }
    }

}