<?php

class shopBwexportPluginBackendDownloadController extends waController
{
    private $plugin_id = 'bwexport';
    private $file_name = 'price.csv';

    public function execute()
    {
        $file = waRequest::request('file', '');

        if($file)
        {
        	$this->file_name = $file;
        }

        $file = wa()->getDataPath('plugins/'.$this->plugin_id.'/data/'.$this->file_name, true);
        waFiles::readFile($file, $this->file_name);
    }
}
