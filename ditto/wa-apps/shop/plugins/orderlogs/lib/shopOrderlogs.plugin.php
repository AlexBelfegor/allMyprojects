<?php

class shopOrderlogsPlugin extends shopPlugin {

	public function backendOrderlogs($order_data) {
		// Получаем ID заказа
		$order_id = $order_data['id'];

		$output = <<<TEXTJS
		<script>
			$(document).ready(function(){
				$("#order-edit-form").attr("action", "?plugin=orderlogs&action=save&id={$order_id}");
			});
		</script>
TEXTJS;

		return $output;
	}

}