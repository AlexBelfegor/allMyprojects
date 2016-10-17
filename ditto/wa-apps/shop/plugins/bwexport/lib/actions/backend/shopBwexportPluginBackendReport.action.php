<?php

class shopBwexportPluginBackendReportAction extends waViewAction
{
    var $plugin_id = "bwexport";
    var $file_csv = "order.csv";

    public function execute()
    {
        $routing = wa()->getRouting();
        $plugins = wa()->getConfig()->getPlugins();

        //path
        $path_real = wa()->getDataPath('plugins/'.$this->plugin_id.'/data/', true);
        $path_http = wa()->getDataUrl('plugins/'.$this->plugin_id.'/data/',true);

        //check file
        if(file_exists($path_real.$this->file_csv))
        {
	        $this->view->assign('file', $path_http.$this->file_csv);
	        $this->view->assign('file_exists', true);

	        $files = waFiles::listdir($path_real);

	        $info = Array();
	        foreach ($files as $file)
	        {
	        	if($file != $this->file_csv) continue;

	            $file_path = $path_real.'/'.$file;
	            $info[] = array
	            (
	                'name'  => $file,
	                'mtime' => filemtime($file_path),
	                'size'  => filesize($file_path),
	            );
	        }
            usort($info, create_function('$a, $b', 'return (max(-1, min(1, $a["mtime"] - $b["mtime"])));'));
            $this->view->assign('info', $info);
        }

        $timeframe = waRequest::request('timeframe', '');
        $from = waRequest::request('from', '');
        $to = waRequest::request('to', '');

        $this->view->assign('timeframe', $timeframe);
        $this->view->assign('from', $from);
        $this->view->assign('to', $to);

        $this->view->assign('path_real', $path_real);
        $this->view->assign('path_http', $path_http);
    }

}
