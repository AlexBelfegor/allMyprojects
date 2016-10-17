<?php

class shopSeofilterProcessor
{
    public function run($feature, $value)
    {
        $masks = $this->getMasks();
        $template = $this->getTemplate();
        $settings = array();

        foreach ($masks as $mask)
        {
            $settings = self::applyMask($mask, $settings);
        }

        $settings = self::applyTemplate($template, $settings);

        $view = wa()->getView();
        $category = $view->getVars('category');

        $this->applyChanges($settings, $category, $feature, $value);

        $view->assign('category', $category);
    }

    protected function applyChanges($settings, &$category, $feature, $value_id)
    {
        $storefront = shopSeofilterSettingsModel::getCurrentStorefront();
        $m_seo_feature = new shopSeofilterFeaturesModel();
        $appSettingsModel = new waAppSettingsModel();

        $seo_feature_info = $m_seo_feature->getSeoFeature('front', $storefront, $feature['id'], $value_id, $category['id']);

        $counter = 0;

        if (!empty($seo_feature_info['meta_title'])) {
            wa()->getResponse()->setTitle($seo_feature_info['meta_title']);
            $category['meta_title'] = $seo_feature_info['meta_title'];
        } else if (!empty($settings['meta_title'])) {
            wa()->getResponse()->setTitle($settings['meta_title']);
            $category['meta_title'] = $settings['meta_title'];
        } else {
            $counter++;
        }

        if (!empty($seo_feature_info['meta_description'])) {
            wa()->getResponse()->setMeta('description', $seo_feature_info['meta_description']);
        } else if (!empty($settings['meta_description'])) {
            wa()->getResponse()->setMeta('description', $settings['meta_description']);
        } else {
            $counter++;
        }

        if (!empty($seo_feature_info['meta_keywords'])) {
            wa()->getResponse()->setMeta('keywords', $seo_feature_info['meta_keywords']);
        } else if (!empty($settings['meta_keywords'])) {
            wa()->getResponse()->setMeta('keywords', $settings['meta_keywords']);
        } else {
            $counter++;
        }

        if (!empty($seo_feature_info['h1'])) {
            $category['name'] = $seo_feature_info['h1'];
        } else if (!empty($settings['h1'])) {
            $category['name'] = $settings['h1'];
        }

        if (!empty($seo_feature_info['seo_desc'])) {
            $category['description'] = $seo_feature_info['seo_desc'];
        } else if (!empty($settings['description'])) {
            $category['description'] = $settings['description'];
        }

        if (!$counter || !empty($seo_feature_info['meta_title']) || !empty($settings['meta_title'])) {
            $plugin_sitemap = $appSettingsModel->get(array('shop', 'seofilter'), 'sitemap');

            if ($plugin_sitemap) {
                $chpu =  wa()->getView()->getVars('chpu');
                if (isset($chpu)) {
                    wa()->getView()->assign('canonical', null);
                }
            } else {
                wa()->getView()->assign('canonical', null);
            }

            if ( empty($seo_feature_info['seo_desc']) && empty($settings['description']) ) {
                $category['description'] = '';
            }
        }
    }

    protected function getMasks()
    {
        $general_settings = shopSeofilterSettingsModel::getGeneralSeofilterSettings();
        $storefront_settings = shopSeofilterSettingsModel::getStorefrontSeofilterSettings();

        return array(
            $general_settings,
            $storefront_settings
        );
    }

    protected function getTemplate()
    {
        return new shopSeofilterTemplate(array('base'));
    }

    private function applyMask($primary_data, $secondary_data)
    {
        $result = $secondary_data;

        foreach ($primary_data as $key => $_primary_data)
        {
            if (!empty($_primary_data))
            {
                $result[$key] = $_primary_data;
            }
        }

        return $result;
    }

    private function applyTemplate(shopSeofilterTemplate $template, $data)
    {
        $result = array();

        foreach ($data as $k => $_data)
        {
            $result[$k] = $template->fetch($_data);
        }

        return $result;
    }
}
