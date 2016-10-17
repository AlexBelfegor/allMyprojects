<?php

class shopReviewsPlugin extends shopPlugin
{
    public function reviews_show()
    {
	    if (waRequest::isMobile())
	    {
	    	return false;
	    }

	    $view = wa()->getView();
        $model = new shopProductReviewsModel();
        $product_model = new shopProductModel();

        $product = $product_model->getByField('url', waRequest::param('product_url'));

        $reviews_data = $model->getFullTree($product['id']);
        $view->assign('reviews_data', $reviews_data);

	    $data['block'] = $view->fetch($this->path.'/templates/actions/frontend/FrontendReviews.html');

	    return $data;
    }
}

