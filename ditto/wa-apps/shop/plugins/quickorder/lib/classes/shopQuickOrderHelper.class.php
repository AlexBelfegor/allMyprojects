<?php

class shopQuickOderHelper
{
    public static function getQuickOrderForm($productId)
    {
        $plugin = wa()->getPlugin('features3000');
        $view = wa()->getView();

        $view->assign('quickOrderProductId', $productId);
        $html = $view->fetch($plugin->getTemplatePath('QuickOrderFrom.html'));
        return $html;
    }
}