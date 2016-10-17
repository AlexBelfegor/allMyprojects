<?php

class shopSitemapPluginFrontendSitemapHumanAction extends waViewAction
{
    public function __construct($params = null)
    {
        parent::__construct($params);

        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new shopFrontendLayout());
        }

        $this->getResponse()->setTitle(waRequest::param('title'));
        $this->getResponse()->setMeta('keywords', waRequest::param('meta_keywords'));
        $this->getResponse()->setMeta('description', waRequest::param('meta_description'));
    }

    public function execute()
    {
	    $data = Array();

	    $view = wa()->getView();
        $plugin = wa()->getPlugin('sitemap');

        $sitemap = new shopSitemapHuman();

        $data = $sitemap->execute();
        $view->assign('sitemap', $data);

        $this->setTemplate($plugin->getTemplatePath());
    }
}
