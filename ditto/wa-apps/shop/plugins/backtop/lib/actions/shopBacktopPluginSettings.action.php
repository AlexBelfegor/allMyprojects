<?php

class shopBacktopPluginSettingsAction extends waViewAction
{
    
    public function execute()
    {
        $model_settings = new waAppSettingsModel();
        $settings = $model_settings->get($key = array('shop', 'backtop'));
        
        $this->view->assign('settings', $settings);
    }       
}
