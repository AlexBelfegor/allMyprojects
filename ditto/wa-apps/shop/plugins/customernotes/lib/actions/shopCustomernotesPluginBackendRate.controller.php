<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 5/26/15
 * Time: 11:50 PM
 */

class shopCustomernotesPluginBackendRateController extends waJsonController
{
    /**
     * @var waView $view
     */
    private $view;

    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private $plugin;

    function __construct()
    {
        $this->view = waSystem::getInstance()->getView();
        $this->plugin = wa()->getPlugin('customernotes');
    }

    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $type = waRequest::request('type', 'up', 'string');
            $order_id = waRequest::request('order_id', 0, 'int');

            $rate = $type === 'up' ? 1 : -1;

            $rm = new shopCustomernotesNotesModel();
            $note = $rm->rateCustomer($order_id, $rate);

            $this->view->assign('note', $note);
            $this->view->assign('order_id', $order_id);
            $this->response = array(
                'note' =>  $note,
                'note_template' => $this->view->fetch($this->plugin->getPluginPath() . '/templates/oneNote.html'),
            );
        }
    }
}

