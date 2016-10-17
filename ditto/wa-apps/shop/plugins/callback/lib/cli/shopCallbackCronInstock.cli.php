<?php

//http://ditto/cli.php?app=shop&class=CallbackCronInstock
class shopCallbackCronInstockCli extends waCliController
{
    public function execute($params = null)
    {
		@set_time_limit(0);

        $feature_id = 3; // � ����� ������ ������, ��������� ��� ditto.ua

        $model = new shopCallbackPluginModel();

        $data = $model->GetCliProductsForIstock();
        //$data = $model->GetCliProductsForIstock(10);

        foreach($data as $i => $val)
        {
			$update_count = 0;
        	$skus = Array();

        	$skus = $model->GetCliProductsForIstockSkus($val['id']);

       		//������� ����� ��� �������� � $skus
       		$model->DeleteCliProductsFeaturesSkusSelectable($val['id'],$feature_id);
            $model->DeleteCliProductsFeaturesSkus($val['id'],$feature_id);

            $flag_update_status = false;

           	$sku_type = $val['sku_type']; //open features

            if($skus) //���� $skus �������� �� ������ ������ 0 � ���� ������ 0
        	{
        		if(true)
        		{
        			foreach($skus as $sku)
	        		{
	                	$value = $model->GetCliProductsFeaturesValue($feature_id,$sku['name']);

	                	$update_count = $update_count + $sku["count"];

	                	if($value) //��������� ����� ����� ��� �������� � $skus
	                	{
	                		$model->AddNewCliProductsFeaturesSkus($val['id'],$sku['id'],$feature_id,$value['id']);

	                		$ckeck = $model->CheckNewCliProductsFeaturesSkus($val['id'],$feature_id,$value['id']);

                            if(!$ckeck)
                            {
                            	$model->AddNewCliProductsFeaturesSkusSelectable($val['id'],$feature_id,$value['id']);
                            }
	                	}
	        		}

	            	$status = 1; //�������� �����
	            	$sku_type = 1; //����� �������������
	            	$model->UpdateCliProductStatus($val['id'],$status,$sku_type);

	            	if($update_count)
	            	{
	            		$model->UpdateProductCount($val['id'],$update_count);
	            	}
                }
        	}
        	else
        	{
        		$flag_update_status = true;
        	}

        	if($flag_update_status)
        	{
                $status = 0; // ���� ��� ������, ��������� �����
                $model->UpdateCliProductStatus($val['id'],$status,$sku_type);

                $count = 0; //���� ��� ������, ����� ���������� �������� � 0
                $model->UpdateProductCount($val['id'],$count);
        	}
        }

        $this->_log();
        echo "success";
    }

    public function _log()
    {
		$data = array();
		//$f= fopen("D:/web/www/ditto/wa-log/CallbackCronInstock.log","a+");
		$f= fopen("/var/www/alexditto/web/wa-log/CallbackCronInstock.log","a+");
		//$data['cron_time_work'] = date("d.m.Y H:i:s",time());
		$data['cron_server_type'] = true;
		$data['br'] = "------------------------\n";
        $dd = "";
		foreach($data as $key => $val)
		{
			$dd .= $key."=".$val."\n";
		}

		fwrite($f,$dd);
		fclose($f);
    }

}