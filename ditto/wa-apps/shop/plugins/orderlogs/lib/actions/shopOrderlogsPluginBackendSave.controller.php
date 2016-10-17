<?php

class shopOrderlogsPluginBackendSaveController extends shopOrderSaveController {

	private $order_data_old;
	private $order_data_new;

	public function execute() {
		// Получаем ID заказа
		$order_id = waRequest::get('id', null, waRequest::TYPE_INT);
		// Получаем старую информацию о заказе
		$this->order_data_old = $this->getModel()->getOrder($order_id);

		/*$filename = "/text2s.log";
		ob_start();
		var_dump($order_data_old);
		$res = ob_get_contents(); 

		$res = $this->view->fetch('BackendSave');
		file_put_contents($_SERVER['DOCUMENT_ROOT'].$filename, $res); */

		// Производим сохранения заказа
		parent::execute();
		// Проверка на ошибки
		if( $this->errors )
			return;

		// Получаем измененые данные о заказе
		$this->order_data_new = $this->getModel()->getOrder($order_id);

		$pl = array();
		// Ищем новые товары
		$pl['add_products'] 	= $this->getNewProducts();
		// Ищем удаленные товары
		$pl['del_products'] 	= $this->getDelProducts();
		// Ищем измененные товары
		$pl['edit_products'] 	= $this->getEditProducts();
		// Ищем измененную информацию о покупателе
		$pl['edit_client']		= $this->getEditClient();
		// Ищем изменненую информацию в параметрах заказа
		$pl['edit_order'] 		= $this->getEditOrder();

		/*if( !$this->checkInfo($pl) )
			return;*/

		$tpl = $this->getTemplate('Log.html');
		$output = $this->replace($pl, $tpl);

		// Изменяем информацию
		$order_log_model = new shopOrderlogsPluginOrderLogModel();
		$order_log_model->editText($order_id, $output);
	}

	/**
	* Выявляем товары, которые были добавлены в заказ
	* @return array добавленые товары
	*/
	private function getNewProducts() {
		if(! $this->order_data_old || ! $this->order_data_new )
			return false;
		// Получаем товары из массивом
		$old_products = $this->order_data_old['items'];
		$new_products = $this->order_data_new['items'];
		// Получаем массив с добавлеными товарами
		$add_products = array_diff_key($new_products, $old_products);

		// Если массив имеет элементы,
		// то значениями этих элементов будут новые товары
		$add_products_list = array();
		if( count($add_products) ) {
			// Берем из новых товаром только нужные параметры
			foreach ($add_products as $item_id => $item_data) {
				$add_products_list[] = $item_data['name'] . " (id: $item_data[product_id])";
			} // END FOREACH
		}

		return $this->renderHTML($add_products_list, 'list', 'Добавленные товары:');
	}

	/**
	* Выявляем товары, которые были удалены из заказа
	* @return html удаленные товары
	*/
	private function getDelProducts() {
		if(! $this->order_data_old || ! $this->order_data_new )
			return false;
		// Получаем товары из массивом
		$old_products = $this->order_data_old['items'];
		$new_products = $this->order_data_new['items'];

		// Получаем массив с удаленными товарами
		$del_products = array_diff_key($old_products, $new_products);

		// Если массив имеет элементы,
		// то значениями этих элементов будут удаленные товары
		$del_products_list = array();
		if( count($del_products) ) {
			// Берем из удаленных товаром только нужные параметры
			foreach ($del_products as $item_id => $item_data) {
				$del_products_list[] = $item_data['name'] . " (id: $item_data[product_id])";
			} // END FOREACH
		}

		return $this->renderHTML($del_products_list, 'list', 'Удаленные товары:');
	}

	/**
	* Выявляем товары, у которых были изменены значения
	* @return html товары с измененными значениями
	*/
	private function getEditProducts() {
		if(! $this->order_data_old || ! $this->order_data_new )
			return false;
		// Получаем товары из массивом
		$old_products = $this->order_data_old['items'];
		$new_products = $this->order_data_new['items'];

		// Избавляемся от товаром, которые были добавлены
		### ПЕРЕПИСАТЬ
		$add_products = array_diff_key($new_products, $old_products);
		foreach($add_products as $item_id => $item_value)
			unset($new_products[$item_id]);

		$no_check_params = array(
			'sku_image_id',
			'file_size'
		);

		// Проходим по каждому товару
		$edit_products_arr = array();
		foreach($new_products as $item_id => $product) {
			// Проходим по каждому параметру товара
			foreach ($product as $param_key => $param_value) {
				// Пропускаем ненужные параметры
				if( in_array($param_key, $no_check_params) )
					continue;
				// Если параметр был изменен, то добавляем его в массим изменений
				if( $old_products[$item_id][$param_key] != $param_value ) {
					if( $param_key == 'name')
						continue;

					if(! is_array($edit_products_arr[$item_id]))
						$edit_products_arr[$item_id] = array();

					// Добавляем название товара (для вывода в таблице)
					if(! $edit_products_arr[$item_id]['name'] ) {
						$edit_products_arr[$item_id]['name'] = $product['name'];
					}

					// Если $param_key = sku_id
					// Получаем название параметра из БД
					if($param_key == "sku_id") {
						$old_sku_data = $this->getModel('product_skus')
								->getSku($old_products[$item_id][$param_key]);
						$new_sku_data = $this->getModel('product_skus')
								->getSku($param_value);

						$edit_products_arr[$item_id][$param_key] = array(
							# старое значение
							'old' => $old_sku_data['name'],
							# новое значение
							'new' => $new_sku_data['name']
						);
						continue;
					}

					$edit_products_arr[$item_id][$param_key] = array(
							# старое значение
							'old' => $old_products[$item_id][$param_key],
							# новое значение
							'new' => $param_value
						);
				} // END IF
			} // END FOREACH Param

		} // END FOREACH Product

		return $this->renderHTML($edit_products_arr, 'table_product', 'Измененные товары:');
	}

	/**
	* Выявляем изменения в информации о покупателе
	* @return array измененые параметры покупателя
	*/
	private function getEditClient() {
		if(! $this->order_data_old || ! $this->order_data_new )
			return false;
		// Получаем данные, которые будем проверять
		$old_contact_arr = $this->order_data_old['contact'];
		$new_contact_arr = $this->order_data_new['contact'];

		$old_params_arr = $this->order_data_old['params'];
		$new_params_arr = $this->order_data_new['params'];

		# Параметры, которые ненужно проверять
		$no_check_params = array(
				'auth_code', 
				'auth_pin',
				'payment_id',
				'payment_plugin',
				'shipping_id',
				'shipping_plugin',
				'shipping_rate_id',
				'ip',
				'landing',
				'referer_host',
				'storefront',
				'user_agent',
				'registered',
				'photo_50x50'
			);

		// Проверяем изменения в контактной информации
		$edit_client_arr = array();
		foreach ($new_contact_arr as $contact_key => $contact_value) {
			// Пропускаем ненужные параметры
			if( in_array($contact_key, $no_check_params) )
				continue;
			if( $old_contact_arr[$contact_key] != $contact_value ) {
				$edit_client_arr[$contact_key] = array(
					# старое значение
					'old' 	=> $old_contact_arr[$contact_key],
					# новое значение
					'new'	=> $contact_value
				);
			} // END IF
		}

		// Проверяем изменения в параметрах
		foreach ($new_params_arr as $param_key => $param_value) {
			// Пропускаем ненужные параметры
			if( in_array($param_key, $no_check_params) )
				continue;
			if( $old_params_arr[$param_key] != $param_value ) {
				$edit_client_arr[$param_key] = array(
					# старое значение
					'old'	=> $old_params_arr[$param_key],
					# новое значение
					'new'	=> $param_value
				);
			} // END IF
		}

		return $this->renderHTML($edit_client_arr, 'table', 'Измененная информация о покупателе:');
	}

	/**
	* Выявляем изменения в данных заказа
	* @return array измененые параметры заказа
	*/
	private function getEditOrder() {
		if(! $this->order_data_old || ! $this->order_data_new )
			return false;
		$old_order = $this->order_data_old;
		$new_order = $this->order_data_new;

		// Удаляем ненужные данные
		unset(
				$old_order['total'], 	$new_order['total'],
				$old_order['items'], 	$new_order['items'], 
				$old_order['contact'], 	$new_order['contact'], 
				$old_order['params'], 	$new_order['params'],
				$old_order['create_datetime'], 	$new_order['create_datetime'],
				$old_order['update_datetime'], 	$new_order['update_datetime']
			);

		// Проходим по оставшимся параметрам массива
		$edit_order_arr = array();
		foreach ($new_order as $param_key => $param_value) {
			if( $old_order[$param_key] != $param_value ) {
				$edit_order_arr[$param_key] = array(
					# старое значение
					'old'	=> $old_order[$param_key],
					# новое значение
					'new'	=> $param_value
				);
			} // END IF
		}

		return $this->renderHTML($edit_order_arr, 'table', 'Измененная информация о заказе:');
	}


	/**
	* Рендеринг шаблона лога
	* @param array $data_arr - данные для лога
	* @param string $type - тип выводимого шаблона
	* @param string $title - заловок
	* @return html
	*/
	private function renderHTML($data_arr, $type = 'table', $title = '') {
		if( !count($data_arr) )
			return;
		if($title)
			$title = "<h3>{$title}</h3>";
		$output = "";
		switch ($type) {
			case 'table':
				$output .= "
					{$title}
					<table class='orderlogs-table'>
						<tr>
							<th>Название</th>
							<th>Старое значение</th>
							<th>Новое значение</th>
						</tr>
				";

				foreach ($data_arr as $param_name => $param_vals) {
					$output .= "<tr>";
						$output .= "<td>"._wp($param_name)."</td>"; // Русифицировать!!!
						$output .= "<td>$param_vals[old]</td>";
						$output .= "<td>$param_vals[new]</td>";
					$output .= "</tr>";
				}
				$output .= "</table>";
			break;

			case 'table_product':
				$output .= "
					{$title}
					<table class='orderlogs-table'>
						<tr>
							<th>Название</th>
							<th>Старое значение</th>
							<th>Новое значение</th>
						</tr>
				";
				foreach ($data_arr as $product_value) {
					if( isset($product_value['name']) ) {
						$output .= "<tr><td colspan='3'>$product_value[name]</td></tr>";
						unset($product_value['name']);
					}
					foreach ($product_value as $param_name => $param_vals) {
						$output .= "<tr>";
							$output .= "<td>"._wp($param_name)."</td>"; // Русифицировать!!!
							$output .= "<td>$param_vals[old]</td>";
							$output .= "<td>$param_vals[new]</td>";
						$output .= "</tr>";
					}
				}
				$output .= "</table>";
			break;

			case 'list'	:
			default 	:
				$output .= "{$title}<ul class='orderlogs-list'>";
				foreach ($data_arr as $name) {
					$output .= "<li>$name</li>";
				}
				$output .= "</ul>";
			break;
		}

		return $output;
	}

	/**
	* Получает шаблон из файла
	* @param string $filename - название файла
	* @return html
	*/
	private function getTemplate($filename) {
		$app_config = wa()->getConfig()->getAppConfig('shop');
		$path_template = $app_config->getAppPath('plugins/orderlogs/templates/');

		if(file_exists($path_file = $path_template . $filename))
			return file_get_contents($path_file);
		return false;
	}

	/**
	* Производит замену плейсхолдеров в шаблоне
	* @param array $pl
	* @param string $out
	* @return html
	*/
	private function replace($pl, $output) {
		if(! is_array($pl))
			return false;
		$keys = array();
		$vals = array();
		foreach ($pl as $key => $val) {
			$keys[] = "[+{$key}+]";
			$vals[] = $val;
		}

		return str_replace($keys, $vals, $output);
	}

	/**
	* Проверка на изменения в заказе
	* @param $check_arr
	* @return boolean
	*/
	private function checkInfo($check_arr) {
		if(! is_array($check_arr) )
			return false;
		$checked_arr = array_filter($check_arr, $this->filter($v));

		if( count($checked_arr) )
			return true;
		return false;
	}

	/**
	* Фильтруем данные для функции array_filter
	* @param $v - 
	* @return boolean
	*/
	private function filter($v) {
		if(! $v)
				return false;
	}

}