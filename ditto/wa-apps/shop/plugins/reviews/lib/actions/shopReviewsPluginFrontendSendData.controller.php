<?php
//ALTER TABLE `shop_product_reviews` ADD `yes` INT NOT NULL DEFAULT '0' AFTER `ip` ,
//ADD `no` INT NOT NULL DEFAULT '0' AFTER `yes` ;

class shopReviewsPluginFrontendSendDataController extends waJsonController
{
    const SHOP_REVIEW_ID = 1;

    public function execute()
    {
        $type = waRequest::get('type');

        switch($type)
        {
        	case "reviewcount":
        	{
            	$response = $this->AddReviewCount();
        	} break;

        	case "reviewadd":
        	{
            	$response = $this->AddReview();
        	} break;
        }

        $this->response = $response;
    }

    public function AddReviewCount()
    {
		$response['status'] = 'fail';

		$id_review = intval(waRequest::get('id_review'));
		$status = waRequest::get('status');

		$review = new shopProductReviewsModel();
		$review_data = $review->getById($id_review);

		if($review_data)
		{
			$product_reivews_model = new shopReviewsPluginModel();

			switch($status)
			{
				case "yes":
				{
					if(!isset($_SESSION['reviews'][$id_review]['yes']))
					{
						$_SESSION['reviews'][$id_review]['yes'] = true;
						$response['status'] = 'ok';
						$product_reivews_model->UpdateCntReview( $id_review, "yes", 1);
					}
				}break;
				case "no":
					{
					if(!isset($_SESSION['reviews'][$id_review]['no']))
					{
						$_SESSION['reviews'][$id_review]['no'] = true;
						$response['status'] = 'ok';
						$product_reivews_model->UpdateCntReview( $id_review, "no", 1);
					}
				}break;
			}
		}

		return $response;
    }

    protected function AddReview()
    {
        $response['status'] = 'fail';

        if (waRequest::getMethod() == 'post')
        {
            $post = waRequest::post();

            //csrf check
            if (trim(waRequest::cookie('_csrf', 2)) == trim(waRequest::post('_csrf', 1)))
            {
		    	if(isset($post['text']))
		    	{
			        $name = waRequest::post('name');
			        $title = waRequest::post('title');
			        $text = waRequest::post('text');
                    $mark = waRequest::post('mark');
                    $product_id = waRequest::post('product_id');

			        if(!$name)
			        {
	                	$response['error']['name'] = "Поле имя должно быть заполнено";
			        }

		            if(isset($_POST['captcha']))
		            {
		            	if (!wa()->getCaptcha()->isValid()) {
		            	    $response['error']['capcha'] = "Поле Капча заполнено не верно";
		            	}
                    }

			        if (!$text)
			        {
			            $response['error']['text'] = "Поле Текст обязательно для заполнения";
			        }

			        if (!$response['error'])
			        {
			        	$model = new shopProductReviewsModel();
			        	if(!$product_id)$product_id = self::SHOP_REVIEW_ID;
			        	$data['parent_id'] = 0;
			        	$data['product_id'] = $product_id;
			        	$data['name'] = $name;
			        	$data['title'] = $name;
			        	$data['text'] = $text;
			        	$data['rate'] = isset($mark)?$mark:5;
			        	$data['status'] = "deleted";

	                    $id = $model->add($data, $data['parent_id']);

			        	$response['status'] = "ok";
			        }
		        }
            }
        }

		return $response;
    }
}

