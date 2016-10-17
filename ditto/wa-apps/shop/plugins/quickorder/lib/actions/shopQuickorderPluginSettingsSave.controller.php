<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopQuickorderPluginSettingsSaveController extends waJsonController
{

    public function execute()
    {
        $fields = waRequest::post('fields', array());
        $values = waRequest::post('values', array());
        $required = waRequest::post('required', array());
        $settings =  array();
        // Дефолтные настройки
        $default_settings = include shopQuickorderPlugin::path('config.php', true);
        $settings = waRequest::post('settings');
        $settings['button_name'] = $settings['button_name'] ? $settings['button_name'] : $default_settings['button_name'];
        $settings['cart_button_name'] = $settings['cart_button_name'] ? $settings['cart_button_name'] : $default_settings['cart_button_name'];
        $settings['comment'] = $settings['comment'] ? $settings['comment'] : $default_settings['comment'];
        $settings['form_text'] = $settings['form_text'] ? $settings['form_text'] : $default_settings['form_text'];
        $settings['order_text'] = $settings['order_text'] ? $settings['order_text'] : $default_settings['order_text'];
        $settings['js_code'] = $settings['js_code'] ? $settings['js_code'] : $default_settings['js_code'];
        $settings['enable_frontend_product_hook'] = isset($settings['enable_frontend_product_hook']) ? intval($settings['enable_frontend_product_hook']) : '0';
        $settings['enable_frontend_cart_hook'] = isset($settings['enable_frontend_cart_hook']) ? intval($settings['enable_frontend_cart_hook']) : '0';
        $settings['enable_user_comment'] = isset($settings['enable_user_comment']) ? intval($settings['enable_user_comment']) : '0';
        $settings['style'] = isset($settings['style']) ? $settings['style'] : 'default';
        $data = array();

        foreach ($fields as $id => $v) {
            // Если передан адрес
            if (strpos($values[$id], "::")) {
                $parts = explode("::", $values[$id]);
                $data[$id]["field_type"] = "address";
                $data[$id]["field_name"] = htmlspecialchars($v, ENT_COMPAT, "UTF-8");
                $data[$id]["field_value"] = $parts[1];
                $data[$id]["required"] = $required[$id];
            } else {
                $data[$id]["field_type"] = "contact";
                $data[$id]["field_name"] = htmlspecialchars($v, ENT_COMPAT, "UTF-8");
                $data[$id]["field_value"] = $values[$id];
                $data[$id]["required"] = $required[$id];
            }
        }
        // Путь к файлу полей
        $config_file = shopQuickorderPlugin::path('fields.php');
        // Записываем новые настройки
        if (!waUtils::varExportToFile($data, $config_file)) {
            $this->errors['messages'][] = _wp('Cannot save the fields');
        }
        
        // Путь к файлу настроек
        $config_settings_file = shopQuickorderPlugin::path('config.php');
        // Записываем новые настройки
        if (!waUtils::varExportToFile($settings, $config_settings_file)) {
            $this->errors['messages'][] = _wp('Cannot save settings fields'); 
        }
    }

}