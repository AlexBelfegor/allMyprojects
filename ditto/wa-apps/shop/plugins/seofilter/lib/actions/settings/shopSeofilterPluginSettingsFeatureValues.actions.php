<?php

class shopSeofilterPluginSettingsFeatureValuesActions extends waJsonActions {

    public function getSeoFeatureAction()
    {
        $storefront = waRequest::get('storefront');
        $feature_id = waRequest::get('feature_id');
        $category_id = waRequest::get('category_id');
        $value_id = waRequest::get('value_id');

        if (isset($value_id)) {
            if ($storefront == 'general') {
                $category_model = new shopCategoryModel();
                $values_tmp = $category_model->getFullTree();

                $i = 0;
                foreach ($values_tmp as $value) {
                    $values[$i] = $value;
                    $i++;
                }
            } else {
                $values = shopSeofilterSettingsModel::getCategoryByRoute($storefront);
            }

            shopSeofilterPluginSettingsAction::setMarker($values);

            $all_feature_ids = waRequest::get('all_feature_ids');
            $filled = shopSeofilterFeaturesModel::isSeoFilled($storefront, $all_feature_ids, $feature_id, $value_id);
        } else {
            $feature_model = new shopFeatureModel();
            $feature = $feature_model->getById($feature_id);
            $values_tmp = $feature_model->getFeatureValues($feature);
            $values = array();

            foreach ($values_tmp as $key => $value) {
                if (is_object($value)) {
                    $values[$key] = $value['value'];
                } else {
                    $values[$key] = $value;
                }
            }

            ksort($values);
            $value_id = key($values);

            $filled = shopSeofilterFeaturesModel::isSeoFilled($storefront, null, $feature_id, $value_id);
        }

        $seo_data = shopSeofilterFeaturesModel::getSeoFeature('back', $storefront, $feature_id, $value_id, $category_id);

        $this->response = array(
            'values' => $values,
            'filled_features' => $filled['features'],
            'filled_values' => $filled['values'],
            'filled_categories' => $filled['categories'],
            'seo' => $seo_data
        );
    }

    public function getSeoValueAction()
    {
        $storefront = waRequest::get('storefront');
        $feature_id = waRequest::get('feature_id');
        $category_id = waRequest::get('category_id');
        $value_id = waRequest::get('value_id');
        $all_feature_ids = waRequest::get('all_feature_ids');

        $filled = shopSeofilterFeaturesModel::isSeoFilled($storefront, $all_feature_ids, $feature_id, $value_id);

        $seo_data = shopSeofilterFeaturesModel::getSeoFeature('back', $storefront, $feature_id, $value_id, $category_id);

        $this->response = array(
            'filled_features' => $filled['features'],
            'filled_values' => $filled['values'],
            'filled_categories' => $filled['categories'],
            'seo' => $seo_data
        );
    }
}
