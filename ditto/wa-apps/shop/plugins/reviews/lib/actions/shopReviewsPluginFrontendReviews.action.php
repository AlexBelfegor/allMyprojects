<?php

class shopReviewsPluginFrontendReviewsAction extends waViewAction
{
    const SHOP_REVIEW_ID = 1;

    public function __construct($params = null)
    {
        parent::__construct($params);

        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new shopFrontendLayout());
        }

        $this->getResponse()->setTitle("Отзывы о интернет-магазине мужской и женской обуви ditto");
        $this->getResponse()->setMeta('keywords', "отзывы, ответы, вопросы, задать, написать, добавить, сайт, ditto, магазин, в интернете, покупателей, клиентов, посмотреть, подробнее");
        $this->getResponse()->setMeta('description', "Посмотреть отзывы, задать вопрос на сайте ditto.ua – мы каждый день улучшаем наш магазин и учитываем обратную связь от клиентов. Спасибо за Ваш отзыв!");
    }

    public function execute()
    {
        $page = waRequest::get('page', 1, waRequest::TYPE_INT);

        $product_reivews_model = new shopReviewsPluginModel();
        $reviews_per_page = $this->getConfig()->getOption('reviews_per_page_total');

        if ($page < 1)	$page = 1;

        $offset = ($page - 1) * $reviews_per_page;

        $reviews = $product_reivews_model->getList($offset,$reviews_per_page,array());

        // TODO: move to model
        $category_model = new shopCategoryModel();

        $product_ids = array();
        foreach ($reviews as $i => $review)
        {
        	$product_ids[] = $review['product_id'];
        }

        $product_ids = array_unique($product_ids);
        $product_model = new shopProductModel();
        $products = $product_model->getByField('id', $product_ids, 'id');
        $image_size = "100x100";

        foreach ($reviews as &$review)
        {
            if (isset($products[$review['product_id']]))
            {
                $product = $products[$review['product_id']];
                $review['product_name'] = $product['name'];
                $review['product_url'] = $product['url'];
                $category = $category_model->getById($product['category_id']);
                $review['category_url'] = $category['full_url'];
	        	$review['comments'] = $product_reivews_model->GetAdminAnswers($review['id']);

                //if()
                //$c['photo'] = waContact::getPhotoUrl($c['id'], $c['photo'], 50, 50);

                if ($product['image_id'])
                {
                    $review['product_url_crop_small'] = shopImage::getUrl(array('id' => $product['image_id'],'product_id' => $product['id'],'ext' => $product['ext']),$image_size);
                }
                else
                {
                    $review['product_url_crop_small'] = null;
                }
            }

            unset($review);
        }

        $pages_count = ceil((float)$product_reivews_model->count() / $reviews_per_page);

        $this->view->assign(array(
        	'pages_count' => $pages_count,
            'count' => count($reviews),
            'offset' => $offset,
            'reviews' => $reviews,
            'shop_id' => self::SHOP_REVIEW_ID,
            'current_author' => shopProductReviewsModel::getAuthorInfo(wa()->getUser()->getId())
        ));
        //print_r($reviews);
        $this->setThemeTemplate('reviews_all.html');
    }
}
