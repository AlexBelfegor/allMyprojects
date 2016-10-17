<?php

return array(
	'name'			=> 'История изменений заказа',
	'description'	=> 'Собирает историю изменений в заказе и сохраняет ее в логи',
	'version'		=> '0.2',
	'vendor'		=> 1028182,
	'handlers'		=> array(
		'backend_order_edit'	=> 'backendOrderlogs'
	),
	'img'			=> 'img/icon.png'
);