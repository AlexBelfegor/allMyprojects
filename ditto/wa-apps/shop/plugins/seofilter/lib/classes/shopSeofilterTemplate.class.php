<?php

class shopSeofilterTemplate
{
    private $types;

    public function __construct($types)
    {
        $this->types = $types;
    }

    public function fetch($template)
    {
        $result = $template;

        foreach ($this->types as $type)
        {
            switch ($type)
            {
                case 'base':
                    $view = wa()->getView();
                    $category = $view->getVars('category');

                    $result = self::replaceVarBase($result, $category);
                    break;
            }
        }

        return $result;
    }

    private static function replaceVarBase($template, $category)
    {
        $storefront = shopSeofilterSettingsModel::getCurrentStorefront();
        $settings = shopSeofilterSettingsModel::getSeofilterSettings($storefront);

        $config = wa()->getConfig();
        $config_store_name = $config->getGeneralSettings('name');
        $config_store_phone = $config->getGeneralSettings('phone');
        $config_storefront_name = !empty($settings['storefront_name']) ? $settings['storefront_name'] : $config_store_name;

        $m_category = new shopCategoryModel();

        if ($category['parent_id'] != '0')
        {
            $parent_category = $m_category->getById($category['parent_id']);
        }
        $category_name_tmp = $m_category->select('name')->where('id = '.(int)$category['id'])->fetchAll();
        $category_name = $category_name_tmp[0]['name'];
        $parent_category_name = isset($parent_category) ? $parent_category['name'] : '';
        if (is_callable(array('shopSeoSettings', 'getByCategoryID'))) {
            $seo_cat = new shopSeoSettings();
            $category_seo_name_tmp = $seo_cat->getByCategoryID($category['id'], 'general');
        }
        if (isset($category_seo_name_tmp) && !empty($category_seo_name_tmp['category_name'])) {
            $category_seo_name = $category_seo_name_tmp['category_name'];
        } else {
            $category_seo_name = $category_name;
        }

        $m_feature = new shopFeatureModel();
        $m_seo_feature = new shopSeofilterFeaturesModel();
        $data = waRequest::get();
        $features_array = array();

        foreach ($data as $key => $value) {
            if ($key != "_" && $key != "sort" && $key != "order" && $key != "page" && count($value) == 1) {
                if (is_array($value) && !isset($value['unit'])) {
                    $features_array[$key] = $value;
                }
            }
        }

        foreach ($features_array as $code => $value) {
            $feature = $m_feature->getByCode($code);
            $feature_name = $feature['name'];
            $value_id = $value[0];
            $feature_values = $m_feature->getFeatureValues($feature);

            if ($feature['type'] == 'color') {
                $value_name = $feature_values[$value_id]['value'];
            } else {
                $value_name = $feature_values[$value_id];
            }

            $seo_feature = $m_seo_feature->getSeoFeature('front', $storefront, $feature['id'], $value_id, $category['id']);
        }

        if (isset($seo_feature) && $seo_feature['seo_name'] != '') {
            $seo_name = $seo_feature['seo_name'];
        } else {
            $seo_name = $value_name;
        }

        $view = wa()->getView();
        $products_count = $view->getVars('products_count');

        $replace_rules = array(
            '{store_name}' => $config_store_name,
            '{store_phone}' => $config_store_phone,
            '{storefront_name}' => $config_storefront_name,
            '{category_name}' => $category_name,
            '{category_seo_name}' => $category_seo_name,
            '{parent_category_name}' => $parent_category_name,
            '{seo_name}' => $seo_name,
            '{feature_name}' => $feature_name,
            '{value_name}' => $value_name,
            '{products_count}' => $products_count,
        );

        return self::replaceVar($replace_rules, $template);
    }

    private static function replaceVar($replace_rules, $template)
    {
        preg_match_all("/\{(\S+)\}/", $template, $matches);

        foreach ($matches[0] as $match) {
            if (strpos($match, 'lower') !== false) {
                $rule = str_replace('|lower', '', $match);
                $replace_rules[$rule] = mb_strtolower($replace_rules[$rule]);
            }
        }

        $template = str_replace('|lower', '', $template);

        $replace_search = array_keys($replace_rules);
        $replace_value = array_values($replace_rules);

        return str_replace($replace_search, $replace_value, $template);
    }
}
