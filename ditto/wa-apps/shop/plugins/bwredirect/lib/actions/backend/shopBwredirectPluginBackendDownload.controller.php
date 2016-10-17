<?php

class shopBwredirectPluginBackendDownloadController extends waController
{
    private $plugin_id = 'bwredirect';
    var $file_name = "redirect.csv";

    public function execute()
    {
    	$file = wa()->getConfig()->getRootPath()."/sync/".$this->file_name;
    	waFiles::readFile($file, $this->file_name);
    }
}
