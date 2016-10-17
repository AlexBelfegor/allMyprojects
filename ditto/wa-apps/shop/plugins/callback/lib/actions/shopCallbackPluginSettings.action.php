<?php

class shopCallbackPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        $plugin = wa()->getPlugin('callback');
        $cron_callback_update_products_discount = $plugin->getCronJob('cron_callback_update_products_discount');
        $cron_callback_update_products_stock = $plugin->getCronJob('cron_callback_update_products_stock');

        $this->view->assign('cron_callback_update_products_discount', $cron_callback_update_products_discount);
        $this->view->assign('cron_callback_update_products_stock', $cron_callback_update_products_stock);
    }

}
