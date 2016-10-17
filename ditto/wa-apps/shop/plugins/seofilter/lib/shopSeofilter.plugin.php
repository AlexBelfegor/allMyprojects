<?php

class shopSeofilterPlugin extends shopPlugin
{
    public function frontendCategory()
    {
        // $this->addJs('js/seofilter.js');

        $plugin = wa()->getPlugin('seofilter');
        $appSettingsModel = new waAppSettingsModel();
        $seo_feature_model = new shopSeofilterFeaturesModel();
        $plugin_settings = $appSettingsModel->get(array('shop', 'seofilter'));

        if ($plugin_settings['enable']) {
            $data = waRequest::get();
            $route = waRequest::param();
            $view = wa()->getView();
            $features_array = array();

            foreach ($data as $key => $value) {
                if ($key != "_" && $key != "sort" && $key != "order" && $key != "page" && count($value) == 1) {
                    if ( (is_array($value) && !isset($value['unit'])) || is_string($value) ) {
                        $features_array[$key] = $value;
                    }

                    if (count($features_array) > 1) {
                        $features_array = array();
                        break;
                    }
                } else if (count($value) > 1) {
                    $features_array = array();
                    break;
                }
            }

            $category = $view->getVars('category');
            $category_url = wa()->getRouteUrl('shop/frontend/category', array('category_url' => isset($route['url_type']) && ($route['url_type'] == 1) ? $category['url'] : $category['full_url']), true);

            if ($features_array) {
                $m_feature = new shopFeatureModel();

                foreach ($features_array as $code => $value) {
                    $feature = $m_feature->getByCode($code);

                    if ($feature['selectable']) {
                        $chpu = $view->getVars('chpu');
                        if (!isset($chpu) && !waRequest::isXMLHttpRequest() && !isset($data['page']) && !isset($data['sort']) && !isset($data['order']) && !isset($data['showall'])) {
                            $url = $seo_feature_model->getOneUrl($feature['id'], $value[0]);
                            if ($url) {
                                $url = $category_url."_".$url;
                                wa()->getResponse()->redirect($url, 301);
                            }
                        }

                        $processor = new shopSeofilterProcessor();
                        $processor->run($feature, $value[0]);
                        break;
                    }
                }
            }

            $filters_url = $seo_feature_model->getSeoUrl($category);
            $filters_url_json = json_encode($filters_url);

            $seoFilterUrls = "<script>var filterValuesNames = '".$filters_url_json."', categoryUrl = '".$category_url."';</script>";

            if ($plugin_settings['js']) {
                $script = "<script type='text/javascript'>".$plugin_settings['js']."</script>";
            } else {
                $script = "<script type='text/javascript' src='".$plugin->getPluginStaticUrl()."js/seofilter.js'></script>";
            }

            return $seoFilterUrls.$script;
        }
    }

    public static function getVariablesTemplatePath()
    {
        $plugin = wa()->getPlugin('seofilter');

        return $plugin->path.'/templates/Variables.html';
    }

    public function sitemap($route)
    {
        $urls = array();

        $m_category = new shopCategoryModel();
        $m_feature = new shopFeatureModel();
        $m_seo_feature = new shopSeofilterFeaturesModel();
        $appSettingsModel = new waAppSettingsModel();
        $plugin_sitemap = $appSettingsModel->get(array('shop', 'seofilter'), 'sitemap');

        $route_2 = shopSeofilterSettingsModel::getCurrentStorefront();
        $sql = "SELECT c.*
                FROM shop_category c
                LEFT JOIN shop_category_routes cr ON c.id = cr.category_id
                WHERE c.status = 1 AND (cr.route IS NULL OR cr.route = '".$route_2."')
                ORDER BY c.left_key";
        $all_categories = $m_category->query($sql)->fetchAll('id');

        foreach ($all_categories as $category) {
            if ($category['filter']) {
                $category_url = wa()->getRouteUrl('shop/frontend/category', array('category_url' => isset($route['url_type']) && ($route['url_type'] == 1) ? $category['url'] : $category['full_url']), true);

                $filters_url = $m_seo_feature->getSeoUrl($category);

                if ($plugin_sitemap) {

                    foreach ($filters_url as $feature) {
                        foreach ($feature as $url) {
                            $urls[] = array(
                                'loc' => $category_url.'_'.$url.'/',
                                'priority' => 0.5
                            );
                        }
                    }

                } else {

                    $collection = new shopProductsCollection('category/' . $category['id']);
                    $category_value_ids = $collection->getFeatureValueIds();

                    $feature_ids = explode(',', $category['filter']);
                    $features = $m_feature->getById(array_filter($feature_ids, 'is_numeric'));

                    foreach ($features as $feature) {
                        if ($feature['selectable']) {
                            $values = $m_feature->getFeatureValues($feature);

                            foreach ($values as $v_id => $v) {
                                if (in_array($v_id, $category_value_ids[$feature['id']])) {
                                    if (array_key_exists($v_id, $filters_url[$feature['code']])) {
                                        $url = $category_url.'_'.$filters_url[$feature['code']][$v_id].'/';
                                    } else {
                                        $url = $category_url.'?'.$feature['code'].'[]='.$v_id;
                                    }

                                    $urls[] = array(
                                        'loc' => $url,
                                        'priority' => 0.5
                                    );
                                }
                            }
                        }
                    }

                }
            }
        }

        if ($urls) {
            return $urls;
        }
    }
}
