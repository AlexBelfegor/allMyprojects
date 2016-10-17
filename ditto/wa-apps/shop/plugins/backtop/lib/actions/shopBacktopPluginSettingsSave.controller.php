<?php

class shopBacktopPluginSettingsSaveController extends waJsonController {
    
    public function execute()
    {
        $plugin_id = array('shop', 'backtop');
        
        try {
            $app_settings_model = new waAppSettingsModel();
            $shop_falling = waRequest::post('settings');
            
            $app_settings_model->set($plugin_id, 'status', (int) $shop_falling['status']);
            $app_settings_model->set($plugin_id, 'bg',  $shop_falling['bg']);
            $app_settings_model->set($plugin_id, 'bg2',  $shop_falling['bg2']);
            $app_settings_model->set($plugin_id, 'border_color', $shop_falling['border_color']);
            $app_settings_model->set($plugin_id, 'border_size', $shop_falling['border_size']);
            $app_settings_model->set($plugin_id, 'border_radius', $shop_falling['border_radius']);
            $app_settings_model->set($plugin_id, 'button_width', $shop_falling['button_width']);
            $app_settings_model->set($plugin_id, 'button_height', $shop_falling['button_height']);
            $app_settings_model->set($plugin_id, 'opacity', $shop_falling['opacity']);
            $app_settings_model->set($plugin_id, 'text_size', $shop_falling['text_size']);
            $app_settings_model->set($plugin_id, 'text', $shop_falling['text']);
            $app_settings_model->set($plugin_id, 'link_color', $shop_falling['link_color']);
            $app_settings_model->set($plugin_id, 'link_hover', $shop_falling['link_hover']);
            $app_settings_model->set($plugin_id, 'position_ver',$shop_falling['position_ver']);
            $app_settings_model->set($plugin_id, 'position_hor', $shop_falling['position_hor']);
            $app_settings_model->set($plugin_id, 'pos_ver', $shop_falling['pos_ver']);
            $app_settings_model->set($plugin_id, 'pos_hor', $shop_falling['pos_hor']);
            

            $this->response['message'] = "Сохранено";
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }
}