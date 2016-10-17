<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 8/7/15
 * Time: 9:00 PM
  */

class shopCustomernotesPluginBackendDeletenoteController extends waJsonController
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
            $note_id = waRequest::request('note_id', '0', 'int');
            $nm = new shopCustomernotesNotesModel();
            $note = $nm->getById($note_id);

            $this->view->assign('contact_id', $note['contact_id']);
            $this->view->assign('order_id', $note['order_id']);
//            $this->view->assign('notes', $nm->getNotesByContactId($note['contact_id']));

            if ($nm->deleteById($note_id)) {
                $this->response = array(
                    'note_id' =>  $note_id,
                    'form_template' => $this->view->fetch($this->plugin->getPluginPath() . '/templates/notesForm.html'),
                );
            }
            else {
                $this->setError(_wp('Note can not be deleted'));
            }
        }
    }
}