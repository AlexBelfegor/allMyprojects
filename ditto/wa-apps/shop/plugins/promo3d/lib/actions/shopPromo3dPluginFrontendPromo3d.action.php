<?php

class shopPromo3dPluginFrontendPromo3dAction extends waViewAction
{
    public function execute()
    {
        $id_product = waRequest::param('promo');

        $model = new shopProductPromoPluginModel();

        //$data = $model->getByField('product_id', $id_product);
        $data = $model->GetProductById($id_product);

        $path = wa()->getDataUrl('plugins/promo3d/templates/actions/frontend/',true);

        if(!count($data))
        {
        	$this->redirect("/");
        }

        $this->view->assign('data', $data);
        $this->view->assign('path', $path);

        $this->view->assign('id_product', $id_product);
    }

}
