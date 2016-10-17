<?php

class shopBwexportPluginContactsController extends waJsonController
{
    private $plugin_id = 'bwexport';
    private $file_csv = 'contacts.csv';
    var $data = Array();
    private $collection;

    private function init()
    {
		//start
		@set_time_limit(0);
		ini_set("memory_limit","1024M");
		ini_set("max_execution_time",1440);

        //init time
        $this->data['start'] = time();
        $this->data['memory'] = memory_get_peak_usage();
        $this->data['memory_avg'] = memory_get_usage();

		//server name
		$this->url = 'http://'.str_replace('//','/',($_SERVER['SERVER_NAME']));

        //path
        $this->path = wa()->getDataPath('plugins/'.$this->plugin_id.'/data/', true);

        //path to images
        $this->path_to_image = $_SERVER['DOCUMENT_ROOT'];
    }

    public function execute()
    {
        //init
        $this->init();

		//init excell
		$path_to_ex = dirname(__FILE__).'/../../classes/excell';
		date_default_timezone_set('Europe/Moscow');
		require_once $path_to_ex.'/PHPExcel/IOFactory.php';

		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

		//header
		$z = 1;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$z, "#"); //id
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, "ФИО");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, "Логин");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, "E-mail");
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, "Телефон");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, "Последний раз заходил");
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, "Кол-во выполненных заказов");
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, "Сумма заказов");

		//get data
		$model = new shopBwexportContactsPluginModel();
		$data = $model->GetContacts();

        $z++;

		//body
		foreach($data as $i => $val)
		{
			$email = $model->GetEmail($val["id"]);
            $phone = $model->GetPhone($val["id"]);

            //get order data
            $order = $model->GetUserOrderByStatus($val["id"],'completed');

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$z, ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, $val['name']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, $val['login']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, $email['email']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, $phone['value']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, $val['last_datetime']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, $order['count']?$order['count']:0);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, $order['sum']?$order['sum']:0);

			$z++;
		}

		//save
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');

		$objWriter->setUseBOM(true);
		$objWriter->setDelimiter(";");
		$objWriter->setEnclosure("");
		$objWriter->setLineEnding("\r\n");
		$objWriter->save($this->path.$this->file_csv);
    }

}
