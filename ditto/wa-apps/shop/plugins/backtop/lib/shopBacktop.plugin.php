<?php

class shopBacktopPlugin extends shopPlugin
{

    public function frontendHeader()
    {
	    if (waRequest::isMobile())
	    {
	    	return false;
	    }

        if (wa()->getRequest()->param('action') == 'cart') {return false;}

        $plugin_id = array('shop', 'backtop');

        $sett = new waAppSettingsModel();
        $status = $sett->get($plugin_id, 'status');

        if (!$status) {return;}

       $view = wa()->getView();
       $content = $view->fetch($this->path.'/templates/BackTop.html');
       return $content;
    }

    public function frontendHead()
    {
	    if (waRequest::isMobile())
	    {
	    	return false;
	    }


        if (wa()->getRequest()->param('action') == 'cart') {return false;}

        $plugin_id = array('shop', 'backtop');

        $sett = new waAppSettingsModel();
        $settings = $sett->get($key = array('shop', 'backtop'));
        $status = $sett->get($plugin_id, 'status');

        if (!$status) {return;}

      return " <script>
                $(function() { $.backtopSet = ".json_encode($settings)."});</script>
                <script src='" . wa()->getAppStaticUrl('shop') . "plugins/backtop/js/BackTop.js'></script>
                <link rel='stylesheet' href='" . wa()->getAppStaticUrl('shop') . "plugins/backtop/css/BackTop.css" . "'>";
    }
}
