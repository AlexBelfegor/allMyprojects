<?php

class shopPromo3dPlugin extends shopPlugin
{
    /*frontend*/
    public function promo_show($product)
    {
	    $result = array();

	    $view = wa()->getView();

        $url = wa()->getRouteUrl('/frontend/product/', array('product_url' => $product['url'], 'category_url' => $product['category_url']));

        $view->assign('url', $url."promo3d/");

        $view->assign('product', $product);

        $model = new shopProductPromoPluginModel();

        $data = $model->getByField('product_id', $product['id']);

        $view->assign('data', $data);

	    $block = $view->fetch($this->path.'/templates/actions/frontend/FrontendPromo3dView.html');

        $result['cart'] = $block;

	    return $result;
    }

    /*backend*/
    public function toolbar_section($product)
    {
	    $result = array();

	    $view = wa()->getView();

        $model = new shopProductPromoPluginModel();

        $data = $model->getByField('product_id', $product['id']);

        $promo = !!$data;

        $view->assign('promo', $promo);

        $view->assign('data', $data);

        $view->assign('product', $product);

	    $block = $view->fetch($this->path.'/templates/actions/backend/BackendProductView.html');

        $result['toolbar_section'] = $block;

	    return $result;
    }

    public function CheckPromo3d($id)
    {
        $model = new shopProductPromoPluginModel();

        $data = $model->getByField(Array('product_id' => $id,'status' => 0));

        if(count($data))
        {
        	return 1;
        }

	    return 0;
    }

}

