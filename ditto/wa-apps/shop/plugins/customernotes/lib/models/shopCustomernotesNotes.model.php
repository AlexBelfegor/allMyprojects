<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 2/21/15
 * Time: 6:24 PM
  */

class shopCustomernotesNotesModel extends waModel {

    protected $table = 'shop_customernotes_notes';

    public function getCustomerRating($order_id, $rate_id = 0) {
        $om = new shopOrderModel();
        $order = $om->getById($order_id);

        $user_id = wa()->getUser()->getId();

        $row = $this->query(
            "SELECT rate, note FROM ".$this->table." WHERE order_id = i:order_id AND create_contact_id = i:create_contact_id",
            array(
                'order_id' => $order_id,
                'create_contact_id' => $user_id,
            ))->fetch();

        $rating = array(
            'id' => $rate_id,
            'up' => $this->query(
                    "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id AND rate = 1",
                    array('id' => $order['contact_id'])
            )->fetchField(),
            'down' => $this->query(
                "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id AND rate = -1",
                array('id' => $order['contact_id'])
            )->fetchField(),
            'sum' => $this->query(
                "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id",
                array('id' => $order['contact_id'])
            )->fetchField(),
            'order' => $row['rate'],
            'order_id' => $order_id,
            'rate' => $row['rate'],
            'note' => $row['note'],
        );

        foreach ($rating as $key => $r) {
            if (empty($r) && $key !== 'note' and $key !== 'id') {
                $rating[$key] = 0;
            }
        }
        $rating['down'] = abs($rating['down']);

        return $rating;
    }

    public function rateCustomer($order_id, $rate) {

        $user_id = wa()->getUser()->getId();

        $currentRate = $this->query(
            "SELECT * FROM ".$this->table." WHERE order_id = i:order_id AND create_contact_id = i:create_contact_id",
            array(
                'order_id' => $order_id,
                'create_contact_id' => $user_id,
            ))->fetch();

        $om = new shopOrderModel();
        $order = $om->getById($order_id);

        if (empty($currentRate)) {
            $data = array(
                'contact_id'   => $order['contact_id'],
                'create_datetime'   => date('Y-m-d H:i:s'),
                'update_datetime'   => date('Y-m-d H:i:s'),
                'create_contact_id' => $user_id,
                'order_id'          => $order_id,
                'rate'              => $rate,
            );
            $data['id'] = $this->insert($data);
        }
        else {
            $data = $currentRate;
            $data['rate'] = intval($rate);
            $data['update_datetime'] = date('Y-m-d H:i:s');
            $this->updateByField('order_id', $order_id, $data);
        }

        return $this->getCustomerRating($order['id'], $data['id']);
    }

    public function noteCustomerByOrderId($order_id, $note)
    {
        $user_id = wa()->getUser()->getId();

        $currentRate = $this->query(
            "SELECT * FROM ".$this->table." WHERE order_id = i:order_id AND create_contact_id = i:create_contact_id",
            array(
                'order_id' => $order_id,
                'create_contact_id' => $user_id,
            ))->fetch();

        $om = new shopOrderModel();
        $order = $om->getById($order_id);

        if (empty($currentRate)) {
            $data = array(
                'contact_id'   => $order['contact_id'],
                'create_datetime'   => date('Y-m-d H:i:s'),
                'create_contact_id' => wa()->getUser()->getId(),
                'order_id'          => $order_id,
                'rate'              => 0,
                'note'              => $note,
            );
            $data['id'] = $this->insert($data);
        }
        else {
            $data = $currentRate;
            $data['update_datetime'] = date('Y-m-d H:i:s');
            $data['note'] = $note;
            $this->updateByField('order_id', $order_id, $data);
        }

        $data['note'] = htmlspecialchars($data['note']);
        $data['order_id'] = $order_id;

        return $data;
    }

    public function getNotesByContactId($contact_id) {
        return $this->select('id, note, rate, order_id')
            ->where('note IS NOT NULL AND contact_id = '.(int)$contact_id)
            ->order('update_datetime DESC')
            ->fetchAll();
    }
}