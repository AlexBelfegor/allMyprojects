<?php

class shopBwexportPluginReportController extends waJsonController
{
    private $plugin_id = 'bwexport';
    private $file_csv = 'order.csv';
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
        //get date
        $timeframe = waRequest::request('timeframe', '');

        $from = "";
        $to = "";

        switch($timeframe)
        {
        	case "custom":
        	{
		        $from = waRequest::request('from', '');
		        $to = waRequest::request('to', '');
		        $from = date("Y-m-d",$from);
				$to = date("Y-m-d",$to);
        	}break;
        	case "all":{}break;
        	case "30":
        	case "60":
        	case "90":
        	case "365":
        	{
            	$from = date("Y-m-d",time() - (60*60*24*$timeframe));
            	$to = date("Y-m-d",time());
        	}break;
        }

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
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, "Статусы");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, "Заказы");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, "Продажи");
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, "Прибыль");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, "Средний чек");
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, "Средняя стоимость");
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, "Кол-во с отрицательной ст.");
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$z, "Кол-во повторных заказов");
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$z, "% от всех заказов");


		//get data
		$model = new shopBwexportReportPluginModel();
		$data = $model->GetStatuses($from,$to);

        $z++;

		//body
		$all_statuses_cnt = 0;
		$all_statuses_cnt_before = 0;
		$all_statuses_sum = 0;
		$all_statuses_profit = 0;
		$all_statuses_average = 0;
		$all_statuses_average_price = 0;
		$all_statuses_profit_minus = 0;
		$all_statuses_repit_orders = 0;
		$all_statuses_percent_orders = 0;

		//get all statuses before
		foreach($data as $status)
		{
        	$all_statuses_cnt_before = $all_statuses_cnt_before + $status['cnt'];
        }

		foreach($data as $i => $val)
		{
			//Прибыль = стоимость розн – закупочная цена(каждого товара)
			$profit = $model->GetProfit($val['state_id'],$from,$to);

            //Средний чек = (продажа – доставка)/ количество заказов
			$average = $model->GetAverage($val['state_id'],$from,$to);

            //Средняя стоимость = продажа / количество товаров
			$average_price = $model->GetAveragePrice($val['state_id'],$from,$to);

            //кол-во заказов у которых прибыль отрицательная
			$profit_minus = $model->GetProfitMinus($val['state_id'],$from,$to);

            //кол-во повторных заказов
			$repit = Array();
			$repit_orders['count'] = 0;
			$repit_data = $model->GetRepitOrdersUser($val['state_id'],$from,$to);

            foreach($repit_data as $contant)
            {
            	if($contant['count'] > 1)
            	{
            		$repit_orders['count'] = $repit_orders['count'] + 1;
            	}
            }

            //% от всех заказов
			$percent = 0;
			$percent = round($val['cnt']/$all_statuses_cnt_before*100,2);

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$z, ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, $val['state_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, $val['cnt']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, round($val['sum']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, round($profit['sum']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, round($average['sum']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, round($average_price['sum']));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, round($profit_minus['count']));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$z, round($repit_orders['count']));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$z, $percent." %");

			$all_statuses_cnt = $all_statuses_cnt + $val['cnt'];
			$all_statuses_sum = $all_statuses_sum + round($val['sum']);
			$all_statuses_profit = $all_statuses_profit + round($profit['sum']);
			$all_statuses_average = $all_statuses_average + round($average['sum']);
			$all_statuses_average_price = $all_statuses_average_price + round($average_price['sum']);
			$all_statuses_profit_minus = $all_statuses_profit_minus + round($profit_minus['count']);
			$all_statuses_repit_orders = $all_statuses_repit_orders + round($repit_orders['count']);

			$z++;
		}

		if($all_statuses_cnt)
		{
			$all_statuses = $all_statuses + $val['cnt'];
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$z, ($i+2));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, "all");
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, $all_statuses_cnt);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, $all_statuses_sum);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, $all_statuses_profit);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, $all_statuses_average);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, $all_statuses_average_price);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, $all_statuses_profit_minus);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$z, $all_statuses_repit_orders);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$z, "-");
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
