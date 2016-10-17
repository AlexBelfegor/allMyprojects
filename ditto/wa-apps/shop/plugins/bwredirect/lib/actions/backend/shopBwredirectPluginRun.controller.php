<?php

class shopBwredirectPluginRunController extends waJsonController
{
    private $plugin_id = 'bwredirect';
    private $collection;
    var $data = Array();
    var $file_name = "redirect.csv";
    var $path_to_csv = "";
    var $path_to_folder = "";
    var $url = "";

    private function init($cron)
    {
		//start
		error_reporting(E_ALL);
		@set_time_limit(0);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set("memory_limit","1024M");
		ini_set("max_execution_time",1440);

        //init time
        $this->data['start'] = time();
        $this->data['memory'] = memory_get_peak_usage();
        $this->data['memory_avg'] = memory_get_usage();

		//server name
		$this->url = 'http://'.str_replace('//','/',($_SERVER['SERVER_NAME']));

        //path
       	$this->path_to_folder = $this->getConfig()->getRootPath()."/sync/";

        if($cron)
        {
        	$this->path_to_csv = $this->path_to_folder.$this->file_name;
        }
        else
        {
	        $this->path_to_csv = (isset($_FILES["file"]["tmp_name"])?$_FILES["file"]["tmp_name"]:"");
        }

        //path to images
        $this->path_to_image = $_SERVER['DOCUMENT_ROOT'];
    }

    public function execute($cron = false)
    {
       	//init
        $this->init($cron);

        if($this->path_to_csv)
        {
			//backup csv
			$this->backup();

			$path_to_csv = $this->getConfig()->getRootPath();
			$path_to_ex = dirname(__FILE__).'/../../classes/excell';
			date_default_timezone_set('Europe/Moscow');
			require_once $path_to_ex.'/PHPExcel/IOFactory.php';

			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$objReader = PHPExcel_IOFactory::createReader('CSV')
			    ->setDelimiter(';')
			    ->setEnclosure('')
			    ->setLineEnding("\r\n")
			    ->setInputEncoding("windows-1251")
			    ->setSheetIndex(0);
			$objPHPExcel = $objReader->load($this->path_to_csv);

			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,false);

			unset($sheetData[0]);

			if(count($sheetData))
			{
		        $model = new shopBwredirectPluginModel();

				$model->ClearRedirectLinkTable();

				foreach($sheetData as $i => $line)
				{
	        		if(count($line) && count($line) == 3)
	        		{
	       				$data = Array();

	        			$url_from = $line["0"];
	        			$data["url_to"] = $line["1"];
	        			$data["redirect"] = $line["2"];

	        			$redirect = $model->CheckUrlForRedirect($url_from);

	       				if($redirect)
	       				{
	                       	$model->UpdateRedirectLink($url_from,$data);
	       				}
	       				else
	       				{
		        			$data["url_from"] = $url_from;
	                       	$model->AddRedirectLink($data);
	       				}
	        		}
				}

    			if(!$cron)
    			{
    				if(file_exists($this->path_to_folder.$this->file_name))
	    			{
	    				@unlink($this->path_to_folder.$this->file_name);
	    			}

                    @copy($this->path_to_csv,$this->path_to_folder.$this->file_name);
				}
	        }
    	}

		if(!$cron)
		{
			$url = "?action=importexport#/".$this->plugin_id."/";
       		$this->redirect($url);
       	}
    }

    private function backup()
    {
		if(file_exists($this->path_to_folder.$this->file_name))
		{
			$file_backup = $this->getConfig()->getRootPath()."/promo/backup/redirect/".date("y-m-d-H-i-s")."_redirect.csv";

			@copy($this->path_to_folder.$this->file_name,$file_backup);
		}
    }
}
