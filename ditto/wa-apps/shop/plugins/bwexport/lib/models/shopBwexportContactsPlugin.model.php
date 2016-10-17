<?php
class shopBwexportContactsPluginModel extends waModel
{
	var $table = 'wa_contact';

	public function GetContacts($from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	cont.*
        FROM
        	".$this->table." as cont
        where
        	cont.id > 0
        	".$where."
        ";

        return $this->query($sql)->fetchAll();
    }

	public function GetEmail($id)
    {
        $sql = "
        SELECT
        	mail.email
        FROM
        	wa_contact_emails as mail
        where
        	mail.contact_id = ".$id."
        ";

        return $this->query($sql)->fetch();
    }

	public function GetPhone($id)
    {
        $sql = "
        SELECT
        	data.value
        FROM
        	wa_contact_data as data
        where
        	data.contact_id = ".$id." and
        	data.field = 'phone'
        limit 1
        ";

        return $this->query($sql)->fetch();
    }

	public function GetUserOrderByStatus($user_id, $status, $from = "", $to = "")
    {
        if($from && $to)
        {
			$where = "and ord.create_datetime BETWEEN '".$from."' AND '".$to."'";
        }

        $sql = "
        SELECT
        	sum(ord.total) as sum,
        	count(ord.id) as count,
        	ord.contact_id
        FROM
        	shop_order as ord
        where
        	ord.state_id = '".$status."' and
        	ord.contact_id = ".$user_id."
        	".$where."
        group by ord.state_id
        ";

        return $this->query($sql)->fetch();
    }

}

