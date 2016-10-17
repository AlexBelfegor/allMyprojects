<?php
class shopCallbackCategoryPluginModel extends waModel
{
    var $table = "shop_callback";

    public function GetCategoryLink($id)
    {
        $sql = "
        SELECT
        	data.*
        FROM
        	".$this->table." as data
        WHERE
        	data.category_id = ".$id."
        limit 1
        ";

        return $this->query($sql)->fetch();
    }

    public function AddCategoryLink($id, $link_category)
    {
    	$sql = "INSERT INTO ".$this->table."
	    	(category_id, link_category, datetime) VALUES
	    	('".$id."', '".$link_category."', '".date("Y-m-d H:i:s")."')
	    ";

		return $this->query($sql)->lastInsertId();
    }

    public function UpdateCategoryLink($id, $link_category)
    {
    	$this->exec("UPDATE ".$this->table." SET link_category = '".$link_category."' WHERE category_id = ".$id."");
    }

	// - - - - - - -
	//category links
	// - - - - - - -
    public function GetCategoryLinksSidebar($id)
    {
        $sql = "
        SELECT
        	data.*
        FROM
        	shop_callback_category_link as data
        WHERE
        	data.category_id = ".$id."
        ";

        return $this->query($sql)->fetchAll();
    }

    public function AddCategoryLinkSidebar($category_id, $link_id, $link, $name)
    {
    	$sql = "INSERT INTO shop_callback_category_link
	    	(category_id, link_id, link, name, datetime) VALUES
	    	('".$category_id."', '".$link_id."', '".$link."', '".$name."', '".date("Y-m-d H:i:s")."')
	    ";

		return $this->query($sql)->lastInsertId();
    }


}

