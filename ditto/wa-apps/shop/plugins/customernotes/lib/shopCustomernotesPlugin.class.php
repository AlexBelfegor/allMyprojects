<?php

class shopCustomernotesPlugin extends shopPlugin
{
    /**
     * @var waView $view
     */
    private static $view;

    private static function getView()
    {
        if (!empty(self::$view)) {
            $view = self::$view;
        } else {
            $view = waSystem::getInstance()->getView();
        }
        return $view;
    }


    public function backendOrder($order) {
        $view = self::getView();
        $rm = new shopCustomernotesNotesModel();

        $view->assign('contact_id', $order['contact_id']);
        $view->assign('order_id', $order['id']);
        $view->assign('notes', $rm->getNotesByContactId($order['contact_id']));
        $view->assign('settings', $this->getSettings());

        return array(
            'info_section' => $view->fetch($this->path . '/templates/orderInfoSection.html'),
        );
    }

    public function getPluginUrl() {
        return $this->getPluginStaticUrl(false);
    }

    public function getPluginPath() {
        return $this->path;
    }
}