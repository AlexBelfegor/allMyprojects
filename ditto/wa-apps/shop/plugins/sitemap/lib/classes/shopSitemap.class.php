<?php

class shopSitemap extends waSitemapConfig
{
    protected $app_id;
    protected $limit = 10000;
    protected $domain = "";


    public function execute($n = 1)
    {
        $routes = $this->getRoutes();
        $this->app_id = wa()->getApp();

        $category_model = new shopCategoryModel();
        $product_model = new shopProductModel();
        $page_model = new shopPageModel();

        $count = 0;

        foreach ($routes as $route) {
            $this->routing->setRoute($route);
            $domain = $this->routing->getDomain(null, true);
            $this->domain = $domain;
            $route_url = $domain.'/'.$this->routing->getRoute('url');

            if ($n == 1) {
                // categories
                $sql = "SELECT c.id,c.parent_id,c.left_key,c.url,c.full_url,c.create_datetime,c.edit_datetime
                        FROM shop_category c
                        LEFT JOIN shop_category_routes cr ON c.id = cr.category_id
                        WHERE c.status = 1 AND (cr.route IS NULL OR cr.route = '".$category_model->escape($route_url)."')
                        ORDER BY c.left_key";
                $categories = $category_model->query($sql)->fetchAll('id');
                $category_url = $this->routing->getUrl($this->app_id.'/frontend/category', array('category_url' => '%CATEGORY_URL%'), true);
                foreach ($categories as $c_id => $c) {
                    if ($c['parent_id'] && !isset($categories[$c['parent_id']])) {
                        unset($categories[$c_id]);
                        continue;
                    }
                    if (isset($route['url_type']) && $route['url_type'] == 1) {
                        $url = $c['url'];
                    } else {
                        $url = $c['full_url'];
                    }
                    $this->addUrl(str_replace('%CATEGORY_URL%', $url, $category_url),
                        $c['edit_datetime'] ? $c['edit_datetime'] : $c['create_datetime'], self::CHANGE_WEEKLY, 0.6);
                }

                $main_url = $this->getUrl('');
                // pages
                $sql = "SELECT full_url, url, create_datetime, update_datetime FROM ".$page_model->getTableName().'
                        WHERE status = 1 AND domain = s:domain AND route = s:route';
                $pages = $page_model->query($sql, array('domain' => $domain, 'route' => $route['url']))->fetchAll();
                foreach ($pages as $p) {
                    $this->addUrl($main_url.$p['full_url'], $p['update_datetime'] ? $p['update_datetime'] : $p['create_datetime'], self::CHANGE_MONTHLY, 0.6);
                }

                /**
                 * @event sitemap
                 * @param array $route
                 * @return array $urls
                 */
                $plugin_urls = wa()->event(array($this->app_id, 'sitemap'), $route);
                if ($plugin_urls) {
                    foreach ($plugin_urls as $urls) {
                        foreach ($urls as $url) {
                            $this->addUrl($url['loc'], ifset($url['lastmod'], time()), ifset($url['changefreq']), ifset($url['priority']));
                        }
                    }
                }

                // main page
                $this->addUrl($main_url, time(), self::CHANGE_DAILY, 1);
            }

            // products
            $c = $this->countProductsByRoute($route);

            if ($count + $c <= ($n - 1) * $this->limit) {
                $count += $c;
                continue;
            } else {
                if ($count >= ($n - 1) * $this->limit) {
                    $offset = 0;
                } else {
                    $offset = ($n - 1) * $this->limit - $count;
                }
                $count += $offset;
                $limit = min($this->limit, $n * $this->limit - $count);
            }

            $sql = "SELECT p.id, p.image_id, p.ext, p.name, p.url, p.create_datetime, p.edit_datetime";
            if (isset($route['url_type']) && $route['url_type'] == 2) {
                $sql .= ', c.full_url category_url';
            }
            $sql .= " FROM ".$product_model->getTableName().' p';
            if (isset($route['url_type']) && $route['url_type'] == 2) {
                $sql .= " LEFT JOIN ".$category_model->getTableName()." c ON p.category_id = c.id";
            }
            $sql .= ' WHERE p.status = 1';
            if (!empty($route['type_id'])) {
                $sql .= ' AND p.type_id IN (i:type_id)';
            }
            $sql .= ' LIMIT '.$offset.','.$limit;
            $products = $product_model->query($sql, $route);

            $count += $products->count();

            $product_url = $this->routing->getUrl($this->app_id.'/frontend/product', array(
                'product_url' => '%PRODUCT_URL%',
                'category_url' => '%CATEGORY_URL%'
            ), true);

            foreach ($products as $p) {
                if (!empty($p['category_url'])) {
                    $url = str_replace(array('%PRODUCT_URL%', '%CATEGORY_URL%'), array($p['url'], $p['category_url']), $product_url);
                } else {
                    $url = str_replace(array('%PRODUCT_URL%', '/%CATEGORY_URL%'), array($p['url'], ''), $product_url);
                }

                $img = $this->productImgHtmlPath($p,'1400');
                $title = $p['name'];

                $this->addUrl($url, $p['edit_datetime'] ? $p['edit_datetime'] : $p['create_datetime'], self::CHANGE_MONTHLY, 0.8, $img, $title);
            }

            if ($count >= $n * $this->limit) {
                break;
            }
        }
    }

    protected function countProductsByRoute($route)
    {
        $model = new waModel();
        $sql = "SELECT COUNT(*) FROM shop_product WHERE status = 1";
        if (!empty($route['type_id'])) {
            $sql .= ' AND type_id IN (i:type_id)';
        }
        return $model->query($sql, $route)->fetchField();
    }

    public function count()
    {
        $routes = $this->getRoutes('shop');
        $c = 0;
        foreach ($routes as $r) {
            $c += $this->countProductsByRoute($r);
        }
        return ceil($c / $this->limit);
    }

    private function getUrl($path, $params = array())
    {
        return $this->routing->getUrl($this->app_id.'/frontend'.($path ? '/'.$path : ''), $params, true);
    }

    public function addUrl($loc, $lastmod, $changefreq = null, $priority = null, $img = null, $title = null)
    {
        if (!is_numeric($lastmod)) {
            $lastmod = strtotime($lastmod);
        }

        $xml  = "<url>\n";
        $xml .= "\t<loc>".htmlspecialchars($loc, ENT_NOQUOTES)."</loc>\n";

        if ($img) {
			$xml .= "\t<image:image>\n";
            $xml .= "\t<image:loc>".$img."</image:loc>\n";
            $xml .= "\t<image:title>".$title."</image:title>\n";
            $xml .= "\t</image:image>\n";
        }

        $xml .= "\t<lastmod>".date('c', $lastmod)."</lastmod>\n";
        if ($changefreq) {
            $xml .= "\t<changefreq>".$changefreq."</changefreq>\n";
        }
        if ($priority) {
            $xml .= "\t<priority>".str_replace(',', '.', min(1.0, max(0.0, $priority)))."</priority>\n";
        }
        $xml .= "</url>\n";
        echo $xml;
    }

    //get image
    public function productImgHtmlPath($product, $size)
    {
        $root = waConfig::get('wa_path_root');
        $image_real = $root.shopImage::getUrl(array('product_id' => $product['id'], 'id' => $product['image_id'], 'ext' => $product['ext']), $size);
        $image_http = "http://".$this->domain.shopImage::getUrl(array('product_id' => $product['id'], 'id' => $product['image_id'], 'ext' => $product['ext']), $size);

        if(file_exists($image_real))
        {
        	return $image_http;
        }

        return false;
    }

    public function blog_execute()
    {
        $routes = $this->getRoutes();
        $app_id = wa()->getApp();

        $blog_model = new blogBlogModel();
        $post_model = new blogPostModel();
        $page_model = new blogPageModel();

        $blogs = $blog_model->getAvailable(false,array('id','name','url'));

        foreach ($routes as $route) {
            $lastmod = null;
            $this->routing->setRoute($route);
            $default_blog_id = isset($route['blog_url_type']) ? (int)$route['blog_url_type'] : 0;
            $default_blog_id = max(0, $default_blog_id);
            $extend_options = array(
            	'datetime'=>true,
            );
            $extend_data = array(
            	'blog'=>$blogs,
            );
            foreach ($blogs as $blog_id => $blog) {
                if (!$default_blog_id || ($blog_id == $default_blog_id) ) {
                    $search_options = array('blog_id'=>$blog_id);
                    $posts = $post_model->search($search_options, $extend_options, $extend_data)->fetchSearchAll('id,title,url,datetime,blog_id');
                    foreach ($posts as $post) {
                        $post['blog_url'] = $blog['url'];
                        $post_lastmod = strtotime($post['datetime']);
                        $lastmod = max($lastmod, $post_lastmod);
                        if(!empty($post['comment_datetime'])) {
                            $post_lastmod = max($post_lastmod, strtotime($post['comment_datetime']));
                        }

                        $this->addUrl($post['link'], $post_lastmod);
                    }
                }
            }

            // pages
            $main_url = wa()->getRouteUrl($app_id."/frontend", array(), true);
            $domain = $this->routing->getDomain(null, true);
            $sql = "SELECT full_url, url, create_datetime, update_datetime FROM ".$page_model->getTableName().'
                    WHERE status = 1 AND domain = s:domain AND route = s:route';
            $pages = $page_model->query($sql, array('domain' => $domain, 'route' => $route['url']))->fetchAll();
            foreach ($pages as $p) {
                $this->addUrl($main_url.$p['full_url'], $p['update_datetime'] ? $p['update_datetime'] : $p['create_datetime'], self::CHANGE_MONTHLY, 0.6);
            }


            $this->addUrl(wa()->getRouteUrl($app_id."/frontend", array(), true), $lastmod, self::CHANGE_DAILY, 1.0);
        }
    }
}


