<?php

class shopSitemapPlugin extends shopPlugin
{
    private $templatepaths = array();

    private function getTemplatePaths()
    {
        if (!$this->templatepaths) {
            $this->templatepaths = array(
                'original' => $this->path . '/templates/actions/frontend/FrontendSitemap.html'
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

    public function getTemplatePopupPath($path)
    {
        $filepath = $this->path.'/templates/actions/popup/'.$path;

        if (file_exists($filepath))
        {
        	return $filepath;
        }

        return ;
    }
}

