<?php

class shopBwredirectPlugin extends shopPlugin
{
    private $templatepaths = array();

    public function redirect()
    {
	    $uri = (isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:"");

	    if($uri)
	    {
	    	$model = new shopBwredirectPluginModel();

	    	$redirect = $model->CheckUrlForRedirect($uri);

	    	if($redirect)
	    	{
				//header('HTTP/1.1 301 Moved Permanently');
	            header("Location: ".$redirect["url_to"], TRUE, $redirect["redirect"]);
	            exit;
	    	}
        }
    }

    private function getTemplatePaths()
    {
        if (!$this->templatepaths) {
            $this->templatepaths = array(
                'original' => $this->path . '/templates/PopupTemplate.html'
            );
        }
        return $this->templatepaths;
    }

    public function getTemplatePath()
    {
        foreach ($this->getTemplatePaths() as $filepath) {
            if (file_exists($filepath)) {
                return $filepath;
            }
        }
        return '';
    }

	public function getCronJob($name = null)
	{
        $config_path = $this->path.'/lib/config/cron.php';

        if (file_exists($config_path))
        {
            $data = include($config_path);
        }

	    return $name?(isset($data[$name])?$data[$name]:null):$data;
	}

}

