<?php
class shopBwexportReportPluginModel extends waModel
{
	var $table = 'shop_order';

	public function GetStatuses($from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	count(ord.id) as cnt,
        	sum(ord.total) as sum,
        	ord.state_id
        FROM
        	".$this->table." as ord
        where
        	ord.id > 0
        	".$where."
        group by ord.state_id
        ";

        return $this->query($sql)->fetchAll();
    }

	public function GetProfit($status, $from = "", $to = "")
    {
        //purchase_price_selectable - зак
        //base_price_selectable = розн

        //profit = base_price_selectable - purchase_price_selectable
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	sum(ord_item.price - ord_item.purchase_price) as sum
        FROM
        	".$this->table." as ord,
        	shop_order_items as ord_item
        where
        	ord.state_id = '".$status."' and
        	ord.id = ord_item.order_id
        	".$where."
        group by ord.state_id
        ";

        return $this->query($sql)->fetch();
    }

	public function GetAverage($status, $from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	count(ord.id) as cnt,
        	sum(ord.total - ord.shipping) as summ,
        	sum(ord.total - ord.shipping)/count(ord.id) as sum
        FROM
        	".$this->table." as ord
        where
        	ord.state_id = '".$status."'
        	".$where."
        group by ord.state_id
        ";

        return $this->query($sql)->fetch();
    }

	public function GetAveragePrice($status, $from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	sum(ord.total)/count(ord.id) as sum
        FROM
        	".$this->table." as ord
        where
        	ord.state_id = '".$status."'
        	".$where."
        group by ord.state_id
        ";

        return $this->query($sql)->fetch();
    }

	public function GetProfitMinus($status, $from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	sum(ord_item.price - ord_item.purchase_price) as sum,
        	count(ord.id) as count
        FROM
        	".$this->table." as ord,
        	shop_order_items as ord_item
        where
        	ord.state_id = '".$status."' and
        	ord.id = ord_item.order_id and
        	(ord_item.price - ord_item.purchase_price) < 0
        	".$where."
        group by ord.state_id
        ";

        return $this->query($sql)->fetch();
    }

	public function GetRepitOrdersUser($status, $from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        /*echo $sql = "
        SELECT
        	sum(ord.total) as sum,
        	count(ord.id) as count,
        	((select count(*) as cnt from ".$this->table." as s where s.contact_id = ord.contact_id group by s.contact_id) > 1)
        FROM
        	".$this->table." as ord,
        	wa_contact as cont
        where
        	ord.state_id = '".$status."' and
        	ord.contact_id = cont.id and
        	".$where."
        group by ord.state_id
        ";*/

        $sql = "
        SELECT
        	sum(ord.total) as sum,
        	count(ord.id) as count,
        	ord.id
        FROM
        	".$this->table." as ord,
        	wa_contact as cont
        where
        	ord.state_id = '".$status."' and
        	ord.contact_id = cont.id
        	".$where."
        group by ord.contact_id
        ";

        return $this->query($sql)->fetchAll();
    }

}

