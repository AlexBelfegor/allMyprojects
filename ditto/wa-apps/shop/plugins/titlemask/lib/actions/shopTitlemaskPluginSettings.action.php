<?php
/**
 * @author Плагины Вебасист <info@wa-apps.ru>
 * @link http://wa-apps.ru/
 */
class shopTitlemaskPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        $plugin = wa('shop')->getPlugin('titlemask');
        $this->view->assign('settings', $plugin->getSettings());
    }

}
