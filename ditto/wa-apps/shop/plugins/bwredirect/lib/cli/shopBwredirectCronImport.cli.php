<?php

//http://ditto/cli.php?app=shop&class=shopBwredirectCronImport
class shopBwredirectCronImportCli extends waCliController
{
    public function execute()
    {
		@set_time_limit(0);

        $runner = new shopBwredirectPluginRunController();

        $runner->execute(true);

        $this->_log();

        echo "success";
    }

    public function _log()
    {
		$data = array();
		$f= fopen($this->getConfig()->getRootPath()."/wa-log/BwredirectCronImport.log","a+");
		$data['cron_time_work'] = date("d.m.Y H:i:s");
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