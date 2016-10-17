<?php

class shopReviews
{
    public static $model = false;
    public function __construct(){}

    public static function GetLastComment()
    {
    	$model = new shopReviewsPluginModel();
    	$last_review = $model->GetLastReviews(1);

        return $last_review;
    }

}