<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopQuickorderPluginSettingsAction extends waViewAction
{

    public function execute()
    {
        // Все поля
        $fields = waContactFields::getAll();
        foreach ($fields as $k => $field) {
            // Удаляем зависимые поля и консолидированное поле адреса
            if ($field instanceof waContactConditionalField || $field->getId() == 'address') {
                unset($fields[$k]);
            }
        }
        // Поля адреса
        $address = waContactFields::get('address');
        $address = $address->getFields();
        // Настройки
        $settings = include shopQuickorderPlugin::path('config.php');
        // Сохраненные поля
        $active_fields = shopQuickorderPlugin::getFields();

        $this->view->assign('fields', $fields);
        $this->view->assign('address', $address);
        $this->view->assign('active_fields', $active_fields);
        $this->view->assign('settings', $settings);
        $this->view->assign('plugin_url', wa()->getPlugin('quickorder')->getPluginStaticUrl());
    }

}