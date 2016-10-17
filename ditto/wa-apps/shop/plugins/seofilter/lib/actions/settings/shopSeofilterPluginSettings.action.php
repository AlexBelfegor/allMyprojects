<?php

class shopSeofilterPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        waLocale::loadByDomain(array('shop', 'seofilter'));
        waSystem::pushActivePlugin('seofilter', 'shop');

        $appSettingsModel = new waAppSettingsModel();
        $plugin_settings = $appSettingsModel->get(array('shop', 'seofilter'));

        $plugin = wa('shop')->getPlugin('seofilter');

        $is_submit = waRequest::post('is_submit', '');

        if (waRequest::isXMLHttpRequest() and $is_submit)
        {
            $common_settings = waRequest::post('seo_settings', array());
            $features_settings = waRequest::post('seo-features', array());
            $features_settings['storefront'] = waRequest::post('current_storefront');

            $appSettingsModel->set(array('shop', 'seofilter'), "enable", $common_settings['enable']);
            $appSettingsModel->set(array('shop', 'seofilter'), "sitemap", $common_settings['sitemap']);
            $appSettingsModel->set(array('shop', 'seofilter'), "js", $common_settings['js']);

            shopSeofilterSettingsModel::setSettings($common_settings);
            shopSeofilterFeaturesModel::setFeatures($features_settings);

            $this->view->assign('success', true);
        }

        $seo_settings = shopSeofilterSettingsModel::getSeofilterSettings();
        $storefronts = shopSeofilterSettingsModel::getStorefronts();

        $seo_settings['enable'] = $plugin_settings['enable'];
        $seo_settings['sitemap'] = $plugin_settings['sitemap'];
        $seo_settings['js'] = $plugin_settings['js'];

        if (empty($seo_settings['js'])) {
            $path = wa()->getAppPath('plugins/seofilter/js/', 'shop');
            $seo_settings['js'] = file_get_contents($path.'seofilter.js');
        }

        $feature_model = new shopFeatureModel();
        $features = $feature_model->getFeatures('selectable', 1);

        if ($features) {
            $current_feature = current($features);

            $values_tmp = $feature_model->getFeatureValues($current_feature);
            $values = array();

            foreach ($values_tmp as $key => $value) {
                if (is_object($value)) {
                    $values[$key] = $value['value'];
                } else {
                    $values[$key] = $value;
                }
            }
            ksort($values);

            $current_value = key($values);

            $filled = shopSeofilterFeaturesModel::isSeoFilled('general', array_keys($features), $current_feature['id'], $current_value);

            $seo_features = shopSeofilterFeaturesModel::getSeoFeature('back', 'general', $current_feature['id'], $current_value);

            $this->view->assign(array(
                'features'=> $features,
                'values'=> $values,
                'filled_features' => $filled['features'],
                'filled_values' => $filled['values'],
                'filled_categories' => $filled['categories']
            ));
        }

        $category_model = new shopCategoryModel();
        $all_categories = $category_model->getFullTree();

        $this->setMarker($all_categories);

        $this->view->assign(array(
            'seofilter_js' => $plugin->getPluginStaticUrl().'js/seofilter.Settings.js',
            'seofilter_css' => $plugin->getPluginStaticUrl().'css/seofilter.Settings.css',
            'categories' => $all_categories,
            'seofilter_features' => isset($seo_features) ? $seo_features : '',
            'variables_template_path' => shopSeofilterPlugin::getVariablesTemplatePath(),
            'storefronts' => $storefronts,
            'seofilter_settings' => $seo_settings,
            'plugin_version' => $plugin->getVersion()
        ));

        waSystem::popActivePlugin();
    }

    public static function setMarker(&$categories)
    {
        foreach ($categories as &$category) {
            $category['marker'] = '';

            if ($category['depth'] > 0) {
                for ($i = 0; $i < $category['depth']; $i++) {
                    $category['marker'] .= '-';
                }
            }
        }
    }
}
