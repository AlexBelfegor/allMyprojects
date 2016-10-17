<?php

class shopManagerPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        /**
         * @var shopManagerPlugin $plugin
         */
        $plugin = wa()->getPlugin('manager');
        $this->view->assign('settings', $plugin->getSettings());

        if ($plugin->getSettings('admin_only') && !wa()->getUser()->isAdmin('shop')) {
            $this->template = 'SettingsNoaccess';
        }

        $group_model = new waGroupModel();
        $groups = $group_model->getAll();
        $this->view->assign('groups', $groups);
    }
}