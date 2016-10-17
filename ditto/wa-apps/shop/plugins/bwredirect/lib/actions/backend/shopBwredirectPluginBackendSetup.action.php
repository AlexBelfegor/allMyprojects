<?php

class shopBwredirectPluginBackendSetupAction extends waViewAction
{
    private $plugin_id = 'bwredirect';
    var $file_name = "redirect.csv";

    public function execute()
    {
        $routing = wa()->getRouting();
        $plugins = wa()->getConfig()->getPlugins();

        //path
        $path_real = wa()->getConfig()->getRootPath()."/sync/";
        $path_http = "/data/";

        //check file
        if(file_exists($path_real.$this->file_name))
        {
	        $this->view->assign('file', $path_http.$this->file_name);
	        $this->view->assign('file_exists', true);

	        $files = waFiles::listdir($path_real);

	        $info = Array();
	        foreach ($files as $file)
	        {
	            if($file=="example")	continue;

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

        $this->view->assign('path_real', $path_real);
        $this->view->assign('path_http', $path_http);

        //add settings
        $this->settings();
    }

    private function settings()
    {
        $plugin = wa()->getPlugin($this->plugin_id);

        $this->view->assign('cron_update_redirect', $plugin->getCronJob('cron_update_redirect'));
    }
}
