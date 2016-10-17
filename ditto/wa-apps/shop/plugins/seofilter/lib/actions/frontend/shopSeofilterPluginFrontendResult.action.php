<?php

class shopSeofilterPluginFrontendResultAction extends shopFrontendCategoryAction
{
    public function execute()
    {
        if (!waRequest::get()) {
            $seo_feature_url = waRequest::param('feature');
            $seo_feature_model = new shopSeofilterFeaturesModel();
            $seo_feature = $seo_feature_model->getByField('url', $seo_feature_url);
            
            if (!$seo_feature) {
                throw new waException('Category not found', 404);
            }

            $f_model = new shopFeatureModel();
            $f_ = $f_model->getById($seo_feature['feature_id']);

            $_GET[$f_['code']] = array($seo_feature['value_id']);
            $this->view->assign('chpu', true);
        }

        $category = $this->getCategory();
        $this->addCanonical();

        // breadcrumbs
        $root_category_id = $category['id'];
        if ($category['parent_id']) {
            $breadcrumbs = array();
            $path = array_reverse($this->getModel()->getPath($category['id']));
            $root_category = reset($path);
            $root_category_id = $root_category['id'];
            foreach ($path as $row) {
                $breadcrumbs[] = array(
                    'url' => wa()->getRouteUrl('/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $row['url'] : $row['full_url'])),
                    'name' => $row['name']
                );
            }
            if ($breadcrumbs) {
                $this->view->assign('breadcrumbs', $breadcrumbs);
            }
        }
        $this->view->assign('root_category_id', $root_category_id);
        // sort
        if ($category['type'] == shopCategoryModel::TYPE_DYNAMIC && !$category['sort_products']) {
            $category['sort_products'] = 'create_datetime DESC';
        }
        if ($category['sort_products'] && !waRequest::get('sort')) {
            $sort = explode(' ', $category['sort_products']);
            $this->view->assign('active_sort', $sort[0] == 'count' ? 'stock' : $sort[0]);
        } elseif (!$category['sort_products'] && !waRequest::get('sort')) {
            $this->view->assign('active_sort', '');
        }
        $this->view->assign('category', $category);

        // products
        $collection = new shopProductsCollection('category/' . $category['id']);

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
                                $tmp_v = $this->getFeatureValue($v);
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
            $this->view->assign('filters', $filters);

            $this->setCollection($collection);

            // fix prices
            $products = $this->view->getVars('products');
            $product_ids = array();
            foreach ($products as $p_id => $p) {
                if ($p['sku_count'] > 1) {
                    $product_ids[] = $p_id;
                }
            }
            if ($product_ids) {
                $min_price = $max_price = null;
                $tmp = array();
                foreach ($filters as $fid => $f) {
                    if ($fid == 'price') {
                        $min_price = waRequest::get('price_min');
                        if (!empty($min_price)) {
                            $min_price = (double)$min_price;
                        } else {
                            $min_price = null;
                        }
                        $max_price = waRequest::get('price_max');
                        if (!empty($max_price)) {
                            $max_price = (double)$max_price;
                        } else {
                            $max_price = null;
                        }
                    } else {
                        $fvalues = waRequest::get($f['code']);
                        if ($fvalues && !isset($fvalues['min']) && !isset($fvalues['max'])) {
                            $tmp[$fid] = $fvalues;
                        }
                    }
                }
                $rows = array();
                if ($tmp) {
                    $pf_model = new shopProductFeaturesModel();
                    $rows = $pf_model->getSkusByFeatures($product_ids, $tmp);
                } elseif ($min_price || $max_price) {
                    $ps_model = new shopProductSkusModel();
                    $rows = $ps_model->getByField('product_id', $product_ids, true);
                }

                $product_skus = array();
                shopRounding::roundSkus($rows, $products);
                foreach ($rows as $row) {
                    $product_skus[$row['product_id']][] = $row;
                }

                $default_currency = $this->getConfig()->getCurrency(true);
                if ($product_skus) {
                    foreach ($product_skus as $product_id => $skus) {
                        $currency = $products[$product_id]['currency'];

                        usort($skus, array($this, 'sortSkus'));
                        $k = 0;
                        if ($min_price || $max_price) {
                            foreach ($skus as $i => $sku) {
                                if ($min_price) {
                                    $tmp_price = shop_currency($min_price, true, $currency, false);
                                    if ($sku['price'] < $tmp_price) {
                                        continue;
                                    }
                                }
                                if ($max_price) {
                                    $tmp_price = shop_currency($max_price, true, $currency, false);
                                    if ($sku['price'] > $tmp_price) {
                                        continue;
                                    }
                                }
                                $k = $i;
                                break;
                            }
                        }
                        $sku = $skus[$k];
                        if ($products[$product_id]['sku_id'] != $sku['id']) {
                            $products[$product_id]['sku_id'] = $sku['id'];
                            $products[$product_id]['frontend_url'] .= '?sku='.$sku['id'];
                            $products[$product_id]['price'] = shop_currency($sku['price'], $currency, $default_currency, false);
                            $products[$product_id]['frontend_price'] = $sku['price'];
                            $products[$product_id]['unconverted_price'] = shop_currency($sku['unconverted_price'], $currency, $default_currency, false);
                            $products[$product_id]['compare_price'] = shop_currency($sku['compare_price'], $currency, $default_currency, false);
                            $products[$product_id]['frontend_compare_price'] = $sku['compare_price'];
                            $products[$product_id]['unconverted_compare_price'] = shop_currency($sku['unconverted_compare_price'], $currency, $default_currency, false);
                        }
                    }
                    $this->view->assign('products', $products);
                }
            }
        } else {
            $this->setCollection($collection);
        }

        // set meta
        wa()->getResponse()->setTitle($category['meta_title']);
        wa()->getResponse()->setMeta('keywords', $category['meta_keywords']);
        wa()->getResponse()->setMeta('description', $category['meta_description']);

        /**
         * @event frontend_category
         * @return array[string]string $return[%plugin_id%] html output for category
         */
        $this->view->assign('frontend_category', wa()->event('frontend_category', $category));

        // default title and meta
        if (!wa()->getResponse()->getTitle()) {
            wa()->getResponse()->setTitle(shopCategoryModel::getDefaultMetaTitle($category));
        }
        if (!wa()->getResponse()->getMeta('keywords')) {
            wa()->getResponse()->setMeta('keywords', shopCategoryModel::getDefaultMetaKeywords($category));
        }

        $this->setThemeTemplate($this->getTheme()->path.'/category.html');
    }
}
