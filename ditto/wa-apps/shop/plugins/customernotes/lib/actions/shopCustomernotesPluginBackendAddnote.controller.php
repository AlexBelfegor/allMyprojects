<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 5/26/15
 * Time: 11:50 PM
  */

class shopCustomernotesPluginBackendAddnoteController extends waJsonController
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
            $note = waRequest::request('customernotesNote', '', 'string');
            $order_id = waRequest::request('customernotesNoteOrderId', 0, 'int');

            $nm = new shopCustomernotesNotesModel();

            $data = $nm->noteCustomerByOrderId($order_id, $note);

            $this->view->assign('note', $data);
            $this->view->assign('order_id', $order_id);
            $this->response = array(
                'note' =>  $data,
                'note_template' => $this->view->fetch($this->plugin->getPluginPath() . '/templates/oneNote.html'),
            );
        }
    }
}