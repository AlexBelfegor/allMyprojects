<?php

class shopBwexportPluginRunController extends waJsonController
{
    private $plugin_id = 'bwexport';
    var $data = Array();
    private $collection;

    private function init()
    {
		//start
		error_reporting(E_ALL);
		@set_time_limit(0);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set("memory_limit","2024M");
		ini_set("max_execution_time",6440);

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
        $this->init();

		//init
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
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, "Наименование"); //Наименование
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, "Артикул"); //Артикул
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, "Тип товаров"); //Тип товаров
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, "Цена"); //Цена
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, "Ссылка на витрину"); //Ссылка на витрину
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, "Бренд"); //Бренд
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, "Сезон"); //Сезон
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$z, "Производство"); //Производство
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$z, "Высота танкетки"); //Высота танкетки
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$z, "Высота голенища"); //Высота голенища
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$z, "Материал верха"); //Материал верха
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$z, "Материал подкладки"); //Материал подкладки
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$z, "Материал подошвы"); //Материал подошвы
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$z, "Вставки"); //Вставки
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$z, "Материал стельки"); //Материал стельки
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$z, "Высота платформы"); //Высота платформы
		$objPHPExcel->getActiveSheet()->setCellValue('R'.$z, "Цвет"); //Цвет
		$objPHPExcel->getActiveSheet()->setCellValue('S'.$z, "Высота сапога"); //Высота сапога
		$objPHPExcel->getActiveSheet()->setCellValue('T'.$z, "Пол"); //Пол
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$z, "Высота каблука"); //Высота каблука
		$objPHPExcel->getActiveSheet()->setCellValue('V'.$z, "Каблук"); //Каблук

		$objPHPExcel->getActiveSheet()->setCellValue('W'.$z, "Коллекция"); //Коллекция
		$objPHPExcel->getActiveSheet()->setCellValue('X'.$z, "Тип подошвы"); //Тип подошвы
		$objPHPExcel->getActiveSheet()->setCellValue('Y'.$z, "Тип материала"); //Тип материала
		$objPHPExcel->getActiveSheet()->setCellValue('Z'.$z, "Вид обуви"); //Вид обуви
		$objPHPExcel->getActiveSheet()->setCellValue('AA'.$z, "Стиль"); //Стиль
		$objPHPExcel->getActiveSheet()->setCellValue('AB'.$z, "Год коллекции"); //Год коллекции

		$objPHPExcel->getActiveSheet()->setCellValue('AC'.$z, "Изображения"); //Изображения
		$objPHPExcel->getActiveSheet()->setCellValue('AD'.$z, "ФТП"); //Изображения

		//get data
		$this->getData();
        $z++;

		//body
		foreach($this->data['products'] as $product)
		{
			$url = $this->url.wa()->getRouteUrl('/frontend/product', array('product_url' => $product['url']));

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$z, $product['id']); //id
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$z, $product['name']); //Наименование
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$z, isset($product['skus'][-1]['sku'])?$product['skus'][-1]['sku']:""); //Артикул
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$z, $product['type_name']); //Тип товаров
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$z, $product['price']); //Цена
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$z, $url); //Ссылка на витрину
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$z, isset($product['features']['brand'])?$product['features']['brand']:""); //Бренд
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$z, isset($product['features']['sizon'])?$product['features']['sizon']:""); //Сезон
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$z, isset($product['features']['Proizvodstvo'])?$product['features']['Proizvodstvo']:""); //Производство
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$z, isset($product['features']['vysota_tanketki'])?$product['features']['vysota_tanketki']:""); //Высота танкетки
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$z, isset($product['features']['vysota_golenishcha'])?$product['features']['vysota_golenishcha']:""); //Высота голенища
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$z, isset($product['features']['Material_verkha'])?$product['features']['Material_verkha']:""); //Материал верха
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$z, isset($product['features']['Material_podkladki'])?$product['features']['Material_podkladki']:""); //Материал подкладки
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$z, isset($product['features']['material_podoshvy'])?$product['features']['material_podoshvy']:""); //Материал подошвы
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$z, isset($product['features']['Vstavki'])?$product['features']['Vstavki']:""); //Вставки
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$z, isset($product['features']['Stelka'])?$product['features']['Stelka']:""); //Материал стельки
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$z, isset($product['features']['vysota_platformy'])?$product['features']['vysota_platformy']:""); //Высота платформы
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$z, isset($product['features']['color'])?$product['features']['color']:""); //Цвет
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$z, isset($product['features']['vysota_sapoga'])?$product['features']['vysota_sapoga']:""); //Высота сапога
			$objPHPExcel->getActiveSheet()->setCellValue('T'.$z, isset($product['features']['sex'])?$product['features']['sex']:""); //Пол
			$objPHPExcel->getActiveSheet()->setCellValue('U'.$z, isset($product['features']['vysota_kabluka'])?$product['features']['vysota_kabluka']:""); //Высота каблука
			$objPHPExcel->getActiveSheet()->setCellValue('V'.$z, isset($product['features']['vysota_kabluka1'])?$product['features']['vysota_kabluka1']:""); //Каблук

			$objPHPExcel->getActiveSheet()->setCellValue('W'.$z, isset($product['features']['kollektsiya'])?$product['features']['kollektsiya']:""); //Коллекция
			$objPHPExcel->getActiveSheet()->setCellValue('X'.$z, isset($product['features']['tip_podoshvy'])?$product['features']['tip_podoshvy']:""); //Тип подошвы
			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$z, isset($product['features']['tip_materiala'])?$product['features']['tip_materiala']:""); //Тип материала
			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$z, isset($product['features']['vid_obuvi'])?$product['features']['vid_obuvi']:""); //Вид обуви
			$objPHPExcel->getActiveSheet()->setCellValue('AA'.$z, isset($product['features']['stil'])?$product['features']['stil']:""); //Стиль
			$objPHPExcel->getActiveSheet()->setCellValue('AB'.$z, isset($product['features']['god_kollektsii'])?$product['features']['god_kollektsii']:""); //Год коллекции

			$objPHPExcel->getActiveSheet()->setCellValue('AC'.$z, isset($product['images'][0])?$this->url.$product['images'][0]:""); //Изображения
			$objPHPExcel->getActiveSheet()->setCellValue('AD'.$z, isset($product['images'][0])?$this->path_to_image.$product['images'][0]:""); //Изображения

			$z++;
		}

		//save
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');

		$objWriter->setUseBOM(false);
		$objWriter->setDelimiter(";");
		$objWriter->setEnclosure("");
		$objWriter->setLineEnding("\r\n");
		$objWriter->save($this->path."price.csv");
    }

    private function getCollection()
    {
        if (!$this->collection) {
            $options = array();
            $this->collection = new shopProductsCollection("", $options);
        }

        return $this->collection;
    }

    private function getData()
    {
        static $products;
        static $product_feature_model;
        static $feature_model;
        static $tags_model;
        static $size;

        $this->getCollection();

        if (!isset($this->data['products']))
        {
	        $offset = 0;
	        $fields = '*, images';
	        $limit = $this->getCollection()->count();
	        //$limit = 100;
	    	$products = $this->getCollection()->getProducts($fields, $offset, $limit, false);

            $i = 0;

	        /*$tax_model = new shopTaxModel();
	        if ($taxes = $tax_model->getAll()) {
	            $this->data['taxes'] = array();
	            foreach ($taxes as $tax) {
	                $this->data['taxes'][$tax['id']] = $tax['name'];
	            }
	        }*/

	        foreach ($products as $i => &$product)
	        {
	            $category_id = isset($product['category_id']) ? intval($product['category_id']) : null;

                $shop_product = new shopProduct($product);

                if (!isset($product['features']))
                {
                    if (!$product_feature_model)
                    {
                        $product_feature_model = new shopProductFeaturesModel();
                    }

                    $product['features'] = $product_feature_model->getValues($product['id']);
                }

                foreach ($product['features'] as $code => &$feature)
                {
                   	$feature = str_replace('×', 'x', $feature);
                    unset($feature);
                }

                if (!isset($product['tags']))
                {
                    if (!$tags_model)
                    {
                        $tags_model = new shopProductTagsModel();
                    }

                    $product['tags'] = implode(',', $tags_model->getTags($product['id']));
                }

                if (isset($product['images']))
                {
                    if (!$size)
                    {
                        /**
                         * @var shopConfig $config
                         */
                        $config = $this->getConfig();
                        $size = $config->getImageSize('big');
                    }

                    foreach ($product['images'] as & $image)
                    {
                        $image = shopImage::getUrl($image, $size);
                    }

                    $product['images'] = array_values($product['images']);
                }

                $product['type_name'] = $shop_product->type['name'];

                $skus = $shop_product->skus;

                if ($product['sku_id'])
                {
                    #default SKU reorder
                    if (isset($skus[$product['sku_id']]))
                    {
                        $sku = $skus[$product['sku_id']];
                        $sku['stock'][0] = $sku['count'];
                        $product['skus'] = array(-1 => $sku);
                        unset($skus[$product['sku_id']]);
                    }
                }

                /*if (!empty($product['tax_id']))
                {
                    $product['tax_name'] = ifset($this->data['taxes'][$product['tax_id']]);
                }*/

                if (!isset($product['features']))
                {
                    $product['features'] = array();
                }
	        }

	        $this->data['products'] = $products;
    	}
    }
}
