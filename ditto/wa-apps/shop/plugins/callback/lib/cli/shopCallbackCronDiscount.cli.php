<?php

//http://ditto/cli.php?app=shop&class=CallbackCronDiscount
class shopCallbackCronDiscountCli extends waCliController
{
    public function execute($params = null)
    {
		@set_time_limit(0);
        $model = new shopCallbackPluginModel();

        $model->ClearCliProductsForDiscount("lowprice");

        $data = $model->GetCliProductsForDiscount();

        foreach($data as $val)
        {
        	$model->UpdateCliProductsDiscount($val['id'],"lowprice");
        }

        //$this->_log();
    }

    public function _log()
    {
		$data = array();
		//$f= fopen("D:/web/www/ditto/wa-log/CallbackCronDiscount.log","a+");
		$f= fopen("/var/www/clients/client2/web61/web/wa-log/CallbackCronDiscount.log","a+");
		$data['cron_time_work'] = @date("d.m.Y H:i:s");
		$data['cron_server_type'] = true;
		$data['br'] = "------------------------\n";

		foreach($data as $key => $val)
		{
			$dd .= $key."=".$val."\n";
		}

		fwrite($f,$dd);
		fclose($f);
    }
}