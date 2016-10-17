<?php
class shopCallbackPluginModel extends waModel
{
    /*add user to contact list*/
    public function GetUser($mail)
    {
        $sql = "SELECT email FROM wa_contact_emails where email = '".$mail."' limit 1";

        return $this->query($sql)->fetchAll();
    }

    public function AddNewUser($mail,$id_category = 0)
    {
    	$sql = "INSERT INTO wa_contact
	    	(name, firstname, middlename, lastname, is_user, about, create_datetime, create_app_id, create_method, create_contact_id) VALUES
	    	('".$mail."', '".$mail."', '-', '-', '0', '".$mail."', '".date("Y-m-d H:i:s")."', 'contacts', 'add', '1')
	    ";

		$id = $this->query($sql)->lastInsertId();

	    $this->exec("INSERT INTO wa_contact_emails
	    	(contact_id, email, ext) VALUES
	    	('".$id."', '".$mail."', 'work')
	    ");

	    if($id_category)
	    {
	    	$this->exec("INSERT INTO wa_contact_categories (category_id, contact_id) VALUES ('".$id_category."', '".$id."')");
	  	}
    }

    /*search data*/
    public function GetSearchWord($string,$limit)
    {
        $sql = "
        SELECT
        	val.id,
        	val.meta_keywords,
        	val.name,
        	val.url,
        	val.price,
        	val.compare_price,
        	val.category_id,
        	val.image_id,
        	val.ext
        FROM
        	shop_product as val
        WHERE
        	val.name like '%".$string."%' and
        	val.status = 1
        order by val.name asc
        limit ".$limit."
        ";

        return $this->query($sql)->fetchAll();
    }

    //cron update products for discount
    public function ClearCliProductsForDiscount($badge)
    {
    	$this->exec("UPDATE shop_product SET badge = '' WHERE badge = '".$badge."' and status = 1");
    }

    public function GetCliProductsForDiscount()
    {
        $sql = "
        SELECT
        	val.id,
        	val.badge,
        	val.compare_price_selectable
        FROM
        	shop_product as val
        WHERE
        	val.status = 1 and
        	val.compare_price_selectable > 0
        ";

        return $this->query($sql)->fetchAll();
    }

    public function UpdateCliProductsDiscount($id, $badge)
    {
    	$this->exec("UPDATE shop_product SET badge = '".$badge."' WHERE id = ".$id." and status = 1");
    }

    //cron update products for discount
    public function GetCliProductsForIstock($id = 0)
    {
        $where = "";

        if($id > 0)
        {
        	$where = "prod.id = ".$id." and";
        }

        $sql = "
        SELECT
        	prod.id,
        	prod.id_1c,
        	prod.name,
        	prod.sku_id,
        	prod.count,
        	prod.category_id,
        	prod.sku_type,
        	cat.name
        FROM
        	shop_product as prod,
        	shop_category as cat
        WHERE
        	".$where."
        	cat.id = prod.category_id and
        	cat.status = 1
        ";

        return $this->query($sql)->fetchAll();
    }

    public function GetCliProductsForIstockSkus($id)
    {
        $sql = "
        SELECT
        	skus.id,
        	skus.name,
        	skus.count,
        	skus.available,
        	skus.price
        FROM
        	shop_product_skus as skus
        WHERE
        	skus.product_id = ".$id." and
        	skus.count > 0 and
        	skus.price > 0 and
        	skus.available = 1
        ";

        return $this->query($sql)->fetchAll();
    }

    public function DeleteCliProductsFeaturesSkus($product_id, $feature_id)
    {
    	$this->exec("DELETE FROM shop_product_features WHERE product_id = ".$product_id." and feature_id = ".$feature_id."");
    }

    public function DeleteCliProductsFeaturesSkusSelectable($product_id, $feature_id)
    {
    	$this->exec("DELETE FROM shop_product_features_selectable WHERE product_id = ".$product_id." and feature_id = ".$feature_id."");
    }

    public function GetCliProductsFeaturesValue($feature_id,$sku_name)
    {
        $sql = "
        SELECT
        	val.id
        FROM
        	shop_feature_values_varchar as val
        WHERE
        	val.value = '".$sku_name."' and
        	val.feature_id = ".$feature_id."
        ";

        return $this->query($sql)->fetch();
    }

    public function AddNewCliProductsFeaturesSkus($product_id, $sku_id, $feature_id, $value_id)
    {
    	$this->exec("INSERT INTO shop_product_features (product_id, sku_id, feature_id, feature_value_id) VALUES ('".$product_id."', '".$sku_id."', '".$feature_id."', '".$value_id."')");
    }

    public function CheckNewCliProductsFeaturesSkus($product_id, $feature_id, $value_id)
    {
        $sql = "
        SELECT
        	val.product_id
        FROM
        	shop_product_features_selectable as val
        WHERE
        	val.product_id = ".$product_id." and
        	val.feature_id = ".$feature_id." and
        	val.value_id = ".$value_id."
        ";

        return $this->query($sql)->fetch();
    }

    public function AddNewCliProductsFeaturesSkusSelectable($product_id, $feature_id, $value_id)
    {
    	$this->exec("INSERT INTO shop_product_features_selectable (product_id, feature_id, value_id) VALUES ('".$product_id."', '".$feature_id."', '".$value_id."')");
    }

    public function UpdateCliProductStatus($product_id, $status, $sku_type)
    {
    	$this->exec("UPDATE shop_product SET status = '".$status."', sku_type = '".$sku_type."' WHERE id = ".$product_id."");
    }

    public function UpdateProductCount($product_id, $count)
    {
    	$this->exec("UPDATE shop_product SET count = '".$count."' WHERE id = ".$product_id."");
    }

	public function GetUserData($id)
    {
        $sql = "
        SELECT
        	data.*
        FROM
        	wa_contact_data as data
        where
        	data.contact_id = ".$id."
        ";

        return $this->query($sql)->fetchAll();
    }

}

