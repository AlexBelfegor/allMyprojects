<?php

class shopSitemapPluginFrontendSitemapAction extends waViewAction
{
    public function execute()
    {
        $this->show();
        exit;
    }

    public function show()
    {
        $system = waSystem::getInstance();
        $system->getResponse()->addHeader('Content-Type', 'application/xml; charset=UTF-8');
        $system->getResponse()->sendHeaders();

		echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
';
        /*echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="'.$system->getUrl(true).'wa-content/xml/sitemap.xsl"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';*/

        $sitemap = new shopSitemap();
        $sitemap->execute();
        $sitemap->blog_execute();

        echo '</urlset>';
    }

}
