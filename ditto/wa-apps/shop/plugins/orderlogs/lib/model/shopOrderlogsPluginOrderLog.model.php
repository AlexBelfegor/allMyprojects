<?php

class shopOrderlogsPluginOrderLogModel extends shopOrderLogModel {

	public function editText($order_id, $text) {
		// Получаем 'последнюю' запись из лога
		$log_id = $this->query("SELECT id FROM ".$this->table." WHERE order_id = $order_id AND action_id = 'edit' ORDER BY id DESC LIMIT 1")
			 ->fetchField('id');
		$this->updateById($log_id, array('text'=>$text) );
		return true;
	}
}