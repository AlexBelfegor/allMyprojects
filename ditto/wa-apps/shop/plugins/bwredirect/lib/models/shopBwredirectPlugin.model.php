<?php
class shopBwredirectPluginModel extends waModel
{
	var $table = 'shop_bwredirect';

	public function ClearRedirectLinkTable()
    {
        $sql = "TRUNCATE TABLE ".$this->table."";

        return $this->query($sql);
    }

	public function CheckUrlForRedirect($uri)
    {
        $sql = "
        SELECT
        	val.url_to,
        	val.redirect
        FROM
        	".$this->table." as val
        where
        	val.url_from = '".$uri."' and
        	val.status = 0
        limit 1
        ";

        return $this->query($sql)->fetch();
    }

	public function UpdateRedirectLink($uri, $data)
    {
    	$sql = "UPDATE ".$this->table." SET url_to = '".$data["url_to"]."', redirect = '".$data["redirect"]."' WHERE url_from = '".$uri."'";

    	$this->exec($sql);
    }

	public function AddRedirectLink($data)
    {
    	$sql = "INSERT INTO ".$this->table."
	    	(url_from, url_to, redirect, datetime) VALUES
	    	('".$data["url_from"]."', '".$data["url_to"]."', '".$data["redirect"]."', '".date("Y-m-d H:i:s")."')
	    ";

		$id = $this->query($sql)->lastInsertId();
    }

}

