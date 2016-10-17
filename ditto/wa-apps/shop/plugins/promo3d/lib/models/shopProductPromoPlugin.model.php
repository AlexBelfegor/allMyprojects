<?php
class shopProductPromoPluginModel extends waModel
{
    protected $table = 'shop_product_promo';

	public function GetProductById($id)
    {
        $sql = "
        SELECT
        	promo.*,
        	prod.url,
        	cat.full_url
        FROM
        	".$this->table." as promo,
        	shop_product as prod,
        	shop_category as cat
        where
        	promo.product_id = ".$id." and
        	promo.product_id = prod.id and
        	prod.category_id = cat.id
        ";

        return $this->query($sql)->fetch();
    }
}

