<?php
/**
 * @author Плагины Вебасист <info@wa-apps.ru>
 * @link http://wa-apps.ru/
 */
class shopTitlemaskPlugin extends shopPlugin
{
    /**
     * Маска для Title страниц товаров
     * @param shopProduct $product
     * @return empty string
     */
    public function titleProduct($product)
    {
        if (waRequest::param('action') != 'product') {
            return;
        }

        $view = wa()->getView();

        $breadcrumbs = $view->getVars('breadcrumbs');
        $categories = array();
        if ($breadcrumbs) {
            foreach($breadcrumbs as $b) {
                $categories[] = $b['name'];
            }
        }
        $category = $categories ? end($categories) : '';
        $root_category = $categories ? $categories[0] : '';
        $price = shop_currency($product['price']);

        if (count($categories) > 1) {
            $parent_category = $categories[count($categories) - 2];
        } else {
            $parent_category = '';
        }

        if (isset($product['skus'])) {
            $sku = $product['skus'][$product['sku_id']]['sku'];
            if (!$sku && (count($product['skus']) > 1)) {
                foreach ($product['skus'] as  $s) {
                    if ($s['sku']) {
                        $sku = $s['sku'];
                        break;
                    }
                }
            }
        } else {
            $sku = '';
        }

        $replaces = array(
            '%product%' => $product['name'],
            '%product|lower%' => mb_strtolower($product['name']),
            '%summary%' => $product['summary'],
            '%price%' => $price,
            '%category%' => $category,
            '%category|lower%' => mb_strtolower($category),
            '%root_category%' => $root_category,
            '%category_full%' => implode(' / ', $categories),
            '%parent_category%' => $parent_category,
            '%sku%' => $sku
        );

        if ($this->getSettings('category_mask') && $product['category_id']) {
            $category_model = new shopCategoryModel();
            $category = $category_model->getById($product['category_id']);
        }

        foreach (array('title', 'description', 'keywords') as $key) {
            if (empty($product['meta_'.$key])) {
                $mask = $this->getSettings('product'.($key == 'title' ? '' : '_'.$key));
                if ($this->getSettings('category_mask') && $product['category_id']) {
                    if ($category[$key.'mask']) {
                        $mask = $category[$key.'mask'];
                    }
                }
                if (!isset($replaces['%subcategories%']) && strpos($mask, '%subcategories%') !== false) {
                    $category_model = new shopCategoryModel();
                    $subcategories = array();
                    foreach ($category_model->getSubcategories($product['category_id']) as $sc) {
                        $subcategories[] = $sc['name'];
                    }
                    $replaces['%subcategories%'] = implode(', ', $subcategories);
                }

                if (strpos($mask, '%feature.') !== false) {
                    if (preg_match_all('/%feature\.([^%]+)%/uis', $mask, $match)) {
                        foreach ($match[1] as $k) {
                            if (isset($product->features[$k])) {
                                $v = $product->features[$k];
                                if (is_array($v)) {
                                    $v = implode(', ', $v);
                                } else {
                                    $v = (string)$v;
                                }
                                $v = trim(strip_tags($v));
                            } else {
                                $v = '';
                            }
                            $mask = str_replace('%feature.'.$k.'%', $v, $mask);
                        }
                    }
                }
                $this->set($key, $mask, $replaces);
            }
        }
        return false;
    }

    /**
     * Маска для Title страниц каталога
     * @param array $category
     * @return empty string
     */
    public function titleCategory($category)
    {
        $breadcrumbs = wa()->getView()->getVars('breadcrumbs');
        $categories = array();
        if ($breadcrumbs) {
            foreach($breadcrumbs as $b) {
                $categories[] = $b['name'];
            }
        }
        $parent_category = $categories ? end($categories) : '';
        $root_category = $categories ? $categories[0] : '';
        $categories[] = $category['name'];

        $subcategories = array();
        foreach ($category['subcategories'] as $sc) {
            $subcategories[] = $sc['name'];
        }

        $replaces = array(
            '%category%' => $category['name'],
            '%category|lower%' => mb_strtolower($category['name']),
            '%root_category%' => $root_category,
            '%category_full%' => implode(' / ', $categories),
            '%count%' => $category['count'],
            '%subcategories%' => implode(', ', $subcategories)
        );

        if ($parent_category) {
            $replaces['%parent_category%'] = $parent_category;
        }

        foreach (array('title', 'description', 'keywords') as $key) {
            $mask = false;
            if (empty($category['meta_'.$key])) {
                $mask = $this->getSettings('category' . ($key == 'title' ? '' : '_' . $key));
            } elseif ($this->getSettings('category_vars')) {
                $mask = $category['meta_'.$key];
            }
            if (!empty($mask)) {
                if (!$parent_category && (strpos($mask, '%parent_category%') !== false)) {
                    $mask = preg_replace("/[^\s]?%parent_category%[^\s%]*\s?/ui", "", $mask);
                }
                $mask = $this->pageMask($mask);
                $this->set($key, $mask, $replaces);
            }
        }
        return false;
    }

    /**
     * Маска для Title главной страницы
     * @return boolean|string
     */
    public function titleIndex()
    {
        $title = waRequest::param('title');
        if (empty($title)) {
            $this->set('title', $this->getSettings('index'));
        }
        $description = waRequest::param('meta_description');
        if (empty($description)) {
            $this->set('description', $this->getSettings('index_description'));
        }
        $keywords = waRequest::param('meta_keywords');
        if (empty($keywords)) {
            $this->set('keywords', $this->getSettings('index_keywords'));
        }
        return false;
    }


    protected function set($type, $mask, $replaces = array())
    {
        if (!empty($mask)) {
            /**
             * @var shopConfig $config
             */
            $config = wa()->getConfig();
            $replaces['%store_name%'] = $config->getGeneralSettings('name');
            $replaces['%store_phone%'] = $config->getGeneralSettings('phone');

            $value = str_replace(array_keys($replaces), array_values($replaces), $mask);
            $value = $this->paramsMask($value);
            $value = str_replace('  ', ' ', $value);
            if ($type == 'title') {
                wa()->getResponse()->setTitle($value);
            } else {
                wa()->getResponse()->setMeta($type, $value);
            }
        }
    }

    /**
     * Маска для Title страниц меток
     * @return boolean|string
     */
    public function titleTag()
    {
        $replaces = array(
            '%tag%' => waRequest::param('tag'),
            '%tag|lower%' => mb_strtolower(waRequest::param('tag'))
        );

        foreach (array('title', 'description', 'keywords') as $key) {
            $mask = $this->getSettings('tag' . ($key == 'title' ? '' : '_' . $key));
            if ($mask) {
                $mask = $this->pageMask($mask);
                $this->set($key, $mask, $replaces);
            }
        }
    }

    /**
     * Маска для Title статических страниц
     * @return boolean|string
     */
    public function titlePage()
    {
        $view = wa()->getView();
        $page = $view->getVars('page');
        $replaces = array(
            '%page%' => $page['name'],
            '%page|lower%' => mb_strtolower($page['name']),
        );

        if ($page['name'] == $page['title'] || !$page['title']) {
            $this->set('title', $this->getSettings('page'), $replaces);
        }
        if (empty($page['description'])) {
            $this->set('description', $this->getSettings('page_description'), $replaces);
        }
        if (empty($page['keywords'])) {
            $this->set('keywords', $this->getSettings('page_keywords'), $replaces);
        }
        return false;
    }

    public function titleBrand()
    {
        $view = wa()->getView();
        $brand_name = urldecode(waRequest::param('brand'));
        $brand = $view->getVars('brand');

        if ($brand) {
            $brand_name = $brand['name'];
        }

        $replaces = array(
            '%brand%' => $brand_name,
            '%brand|lower%' => mb_strtolower($brand_name),
        );

        if ($view->getVars('category')) {
            $category = $view->getVars('category');
            $replaces['%category%'] = $category['name'];
            $replaces['%category%|lower'] = mb_strtolower($category['name']);
        } else {
            $replaces['%category%'] = '';
            $replaces['%category%|lower'] = '';
        }

        foreach (array('title', 'description', 'keywords') as $key) {
            if (!$brand || empty($brand[($key == 'title' ? '' : 'meta_') . $key ])) {
                $mask = $this->getSettings('brand' . ($key == 'title' ? '' : '_' . $key));
                if ($mask) {
                    $mask = $this->pageMask($mask);
                    $this->set($key, $mask, $replaces);
                }
            }
        }
        return false;
    }


    public function frontendHead()
    {
        if ((waRequest::param('module') == 'frontend') && is_null(waRequest::param('action'))) {
            return $this->titleIndex();
        }

        if (waRequest::param('action') == 'tag') {
            return $this->titleTag();
        }

        if (waRequest::param('action') == 'page') {
            return $this->titlePage();
        }

        if (waRequest::param('brand')) {
            return $this->titleBrand();
        }
    }

    protected function paramsMask($mask)
    {
        if (strpos($mask, '%params.') !== false) {
            if (preg_match_all('/%params\.([^%]+)%/uis', $mask, $match)) {
                foreach ($match[1] as $k) {
                    $mask = str_replace('%params.'.$k.'%', waRequest::param($k, ''), $mask);
                }
            }
        }
        return $mask;
    }

    protected function pageMask($mask)
    {
        if (strpos($mask, '%page_num%') !== false) {
            $page = waRequest::get('page', 1, 'int');
            if (preg_match('/\[[^\]]*?%page_num%[^\]]*?\]/uis', $mask)) {
                $mask = preg_replace('/\[([^\]]*?)%page_num%([^\]]*?)\]/uis', $page > 1 ? '$1%page_num%$2' : '', $mask);
            }
            $mask = str_replace('%page_num%', $page > 0 ? $page : '', $mask);
        }
        return $mask;
    }

    public function saveSettings($settings = array())
    {
        if (!empty($settings['category_mask'])) {
            // check fields titlemask and descriptionmask in table shop_category
            $model = new waModel();
            try {
                $model->query("SELECT titlemask FROM shop_category WHERE 0");
            } catch (waDbException $e) {
                $model->query("ALTER TABLE shop_category ADD titlemask VARCHAR(255) NULL DEFAULT NULL");
            }
            try {
                $model->query("SELECT descriptionmask FROM shop_category WHERE 0");
            } catch (waDbException $e) {
                $model->query("ALTER TABLE shop_category ADD descriptionmask TEXT NULL");
            }
            try {
                $model->query("SELECT keywordsmask FROM shop_category WHERE 0");
            } catch (waDbException $e) {
                $model->query("ALTER TABLE shop_category ADD keywordsmask TEXT NULL");
            }
            try {
                $cache = new waSystemCache('db/shop_category');
                $cache->delete();
            } catch (waException $e) {
            }
        }
        return parent::saveSettings($settings);
    }

    public function categoryDialog($category)
    {
        if ($this->getSettings('category_mask')) {
            $view = wa()->getView();
            $view->assign('category', $category);
            return $view->fetch($this->path.'/templates/Dialog.html');
        }
    }


    public function categorySave($category)
    {
        if ($this->getSettings('category_mask')) {
            $category_model = new shopCategoryModel();
            $category_model->updateById($category['id'], array(
                'titlemask' => waRequest::post('titlemask'),
                'descriptionmask' => waRequest::post('descriptionmask'),
                'keywordsmask' => waRequest::post('keywordsmask'),
            ));
        }
    }
}