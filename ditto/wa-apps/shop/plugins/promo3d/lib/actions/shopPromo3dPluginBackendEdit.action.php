<?php

class shopPromo3dPluginBackendEditAction extends waViewAction
{
    public function execute()
    {
        $this->setLayout(new shopBackendLayout());

       	$model = new shopProductPromoPluginModel();

      	$product_id = waRequest::request('id', 0, 'int');

        $data = $model->getByField('product_id', $product_id);

        $form = waRequest::post();

        if(count($form) && $product_id)
        {
        	$promo_big = waRequest::file('promo_big');

            if ($promo_big->uploaded()) {

                try {
                    // create waImage
                    $promo_big->waImage();
                } catch(Exception $e) {
                    throw new Exception(_w("File isn't an image"));
                }
                $path = wa()->getDataPath('plugins/promo3d/templates/actions/frontend/'.$product_id.'.3d/', true);

                $file_name = $product_id.".3d.100.".$promo_big->extension;
                if (!file_exists($path) || !is_writable($path)) {
                    throw new waException(sprintf(_wp('File could not be saved due to the insufficient file write permissions for the %s folder.'),'wa-data/public/shop/data/'));
                } elseif (!$promo_big->moveTo($path, $file_name)) {
                    throw new waException(_wp('Failed to upload file.'));
                }
            }

        	$promo_all = waRequest::file('promo_all');

            if ($promo_all->uploaded()) {

                try {
                    // create waImage
                    $promo_all->waImage();
                } catch(Exception $e) {
                    throw new Exception(_w("File isn't an image"));
                }
                $path = wa()->getDataPath('plugins/promo3d/templates/actions/frontend/', true);

                $file_name = $product_id.".".$promo_all->extension;
                if (!file_exists($path) || !is_writable($path)) {
                    throw new waException(sprintf(_wp('File could not be saved due to the insufficient file write permissions for the %s folder.'),'wa-data/public/shop/data/'));
                } elseif (!$promo_all->moveTo($path, $file_name)) {
                    throw new waException(_wp('Failed to upload file.'));
                }
            }

        	if(!count($data))
        	{
        		$model->insert(array('product_id' => $product_id, 'datetime' => date("Y.m.d H:i:s")));
            }

            if(isset($form['promo_check']) && count($data))
            {
                $update = array(
                    'status' => $form['promo_check'],
                );

                $model->updateById($data['id'], $update);
            }

            $url = "?action=products#/product/".$product_id."/";

        	$this->redirect($url);
        }

        $this->view->assign('backend_products', wa()->event('backend_products'));
    }
}
