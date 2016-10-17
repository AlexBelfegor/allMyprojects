<?php

class shopSeofilterFeaturesModel extends waModel
{
    public static function getAllowSettings() { return self::$allow_settings; }

    protected static $allow_settings = array(
        'h1',
        'seo_name',
        'seo_desc',
        'meta_title',
        'meta_description',
        'meta_keywords'
    );
    protected $table = 'shop_seofilter_feature_values';

    public static function setFeatures($settings)
    {
        $m_settings = new self();

        $allow_settings = self::getAllowSettings();

        foreach ($allow_settings as $allow_setting) {
            $url = '';

            if ($allow_setting == 'seo_name') {
                $category_id = 0;

                if ( isset($settings[$allow_setting]) ) {
                    if (!$settings['url']) {
                        $criteria = array(
                            'storefront' => $settings['storefront'],
                            'feature_id' => $settings['feature_id'],
                            'value_id' => $settings['value_id'],
                            'category_id' => $category_id,
                            'name' => 'seo_name'
                        );
                        $current_url = $m_settings->getByField($criteria, true);

                        if ($current_url[0]['url']) {
                            $url = $current_url[0]['url'];
                        } else {
                            $url = $m_settings->transliterate($settings[$allow_setting], false);
                        }
                    } else if ($settings[$allow_setting] != '') {
                        $url = $settings['url'];
                    }
                }
            } else {
                $category_id = $settings['category_id'];
            }

            if ( isset($settings[$allow_setting]) ) {
                $priority = $m_settings->calcPriority($settings['storefront'], $category_id);
                $seo_value = $settings[$allow_setting];
            } else {
                $priority = 0;
                $seo_value = '';
            }

            $m_settings->replace(
                array(
                    'storefront' => $settings['storefront'],
                    'feature_id' => $settings['feature_id'],
                    'value_id' => $settings['value_id'],
                    'category_id' => $category_id,
                    'name' => $allow_setting,
                    'value' => $seo_value,
                    'url' => $url,
                    'priority' => $priority
                )
            );
        }

    }

    public static function getSeoFeature($side, $storefront, $feature_id, $value_id, $category_id = 0)
    {
        $m_settings = new self();

        $allow_settings = self::getAllowSettings();

        if ($side == 'front' && $storefront != 'general') {
            $storefront = array($storefront, 'general');
        }
        if ($side == 'front' && $category_id != 0) {
            $category_id = array($category_id, 0);
        }

        $criteria = array(
            'storefront' => $storefront,
            'feature_id' => $feature_id,
            'value_id' => $value_id,
            'category_id' => $category_id
        );

        $sql = "SELECT * FROM ".$m_settings->table;
        $where = $m_settings->getWhereByField($criteria);
        if ($where != '') {
            $sql .= " WHERE ".$where;
        }
        $sql .= " ORDER BY priority ASC";
        $results_array = $m_settings->query($sql)->fetchAll();

        foreach ($allow_settings as $setting) {
            $result[$setting] = '';
        }
        $result['url'] = '';

        if ($side == 'back' && $category_id != 0) {
            $criteria['category_id'] = 0;
            $criteria['name'] = 'seo_name';
            $seo_name = $m_settings->getByField($criteria, true);

            if (!empty($seo_name)) {
                $result['seo_name'] = $seo_name[0]['value'];
            }
        }

        foreach ($results_array as $a) {
            if ($result[$a['name']] != '') {
                continue;
            } else if ($result[$a['name']] == '') {
                if ($a['value']) {
                    if ($a['name'] == 'seo_name' && $a['category_id'] != 0) {
                        continue;
                    } else if ($a['name'] == 'seo_name' && $a['url'] != '') {
                        $result['url'] = $a['url'];
                    }
                    $result[$a['name']] = $a['value'];
                    continue;
                }
            }
        }

        return $result;
    }

    public static function isSeoFilled($storefront, $features, $current_feature, $current_value)
    {
        $m_settings = new self();
        $filled = array('features' => array(), 'values' => array(), 'categories' => array());

        if ($features) {
            $feature_id = $features;
        } else {
            $feature_id = $current_feature;
        }

        $criteria = array(
            'storefront' => $storefront,
            'feature_id' => $feature_id
        );

        $all = $m_settings->getByField($criteria, true);

        foreach ($all as $a) {
            if ($a['value'] && $a['category_id'] != 0) {
                if (!in_array($a['feature_id'], $filled['features'])) {
                    $filled['features'][] = $a['feature_id'];
                }

                if ($a['feature_id'] == $current_feature) {
                    if (!in_array($a['value_id'], $filled['values'])) {
                        $filled['values'][] = $a['value_id'];
                    }

                    if ($a['value_id'] == $current_value && !in_array($a['category_id'], $filled['categories'])) {
                        $filled['categories'][] = $a['category_id'];
                    }
                }
            }
        }

        return $filled;
    }

    public function getSeoUrl($category) {
        $filter_ids = explode(',', $category['filter']);
        $storefront = shopSeofilterSettingsModel::getCurrentStorefront();

        $criteria = array(
            'storefront' => array('general', $storefront),
            'feature_id' => $filter_ids,
            'name' => 'seo_name'
        );

        $sql = "SELECT * FROM ".$this->table;
        $where = $this->getWhereByField($criteria);
        if ($where != '') {
            $sql .= " WHERE ".$where;
        }
        $sql .= " ORDER BY priority ASC";
        $results_array = $this->query($sql)->fetchAll();

        if ($results_array) {
            foreach ($results_array as $r) {
                $feature_ids[] = $r['feature_id'];
            }

            $feature_model = new shopFeatureModel();
            $all_features = $feature_model->getById($feature_ids);

            foreach ($results_array as $a) {
                $code = $all_features[ $a['feature_id'] ]['code'];

                if (isset( $result[ $code ][ $a['value_id'] ] ) && $result[ $code ][ $a['value_id'] ] != '') {
                    continue;
                } else {
                    if ($a['url']) {
                        $result[ $code ][ $a['value_id'] ] = $a['url'];
                    }
                }
            }
        }

        return $result;
    }

    public function getOneUrl($feature_id, $value_id) {
        $m_settings = new self();
        $storefront = shopSeofilterSettingsModel::getCurrentStorefront();
        $url = '';

        $criteria = array(
            'storefront' => array('general', $storefront),
            'feature_id' => $feature_id,
            'value_id' => $value_id,
            'category_id' => 0,
            'name' => 'seo_name'
        );

        $seo_feature = $m_settings->getByField($criteria, true);

        if ($seo_feature) {
            foreach ($seo_feature as $s) {
                if ($s['url'] != '') {
                    $url = $s['url'];
                    break;
                }
            }
        }

        return $url;
    }

    public static function setAllUrls()
    {
        $m_settings = new self();
        $appSettingsModel = new waAppSettingsModel();

        $seo_features = $m_settings->getByField(array('name' => 'seo_name', 'category_id' => 0), true);

        foreach ($seo_features as $f) {
            if ($f['value']) {
                $field = array(
                    'storefront' => $f['storefront'],
                    'feature_id' => $f['feature_id'],
                    'value_id' => $f['value_id'],
                    'category_id' => $f['category_id'],
                    'name' => 'seo_name'
                );

                $url = $m_settings->transliterate($f['value'], false);
                $m_settings->updateByField($field, array('url' => $url));
            }
        }

        $appSettingsModel->set(array('shop', 'seofilter'), 'url_updated', 1);
    }

    protected function calcPriority($storefront, $category_id) {
        if ($storefront != 'general' && $category_id != 0) {
            return 1;
        } else if ($storefront != 'general' && $category_id == 0) {
            return 2;
        } else if ($storefront == 'general' && $category_id != 0) {
            return 3;
        } else if ($storefront == 'general' && $category_id == 0) {
            return 4;
        }
        return 5;
    }

    public function transliterate($str, $strict = true)
    {
        $str = preg_replace('/\s+/', '-', $str);
        if ($str) {
            foreach (waLocale::getAll() as $lang) {
                $str = waLocale::transliterate($str, $lang);
            }
        }
        $str = preg_replace('/[^a-zA-Z0-9_-]+/', '', $str);
        if ($strict && !strlen($str)) {
            $str = date('Ymd');
        }
        return strtolower($str);
    }

}
