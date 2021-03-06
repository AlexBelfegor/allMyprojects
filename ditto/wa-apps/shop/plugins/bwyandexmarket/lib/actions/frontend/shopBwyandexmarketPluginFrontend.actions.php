<?php

class shopBwyandexmarketPluginFrontendActions extends waActions
{
    public function catalogAction()
    {
        /**
         * @var shopYandexmarketPlugin $plugin
         */
        $plugin = wa()->getPlugin('bwyandexmarket');

        $profile_helper = new shopImportexportHelper('bwyandexmarket');

        list($path, $profile_id) = $plugin->getInfoByHash(waRequest::param('hash'));
        if ($profile_id) {
            $profile = $profile_helper->getConfig($profile_id);
            if (!$profile) {
                throw new waException('Profile not found', 404);
            }
            $lifetime = ifset($profile['config']['lifetime'], 0);
            if ($lifetime && (!file_exists($path) || (time() - filemtime($path) > $lifetime)))
            {
                waRequest::setParam('profile_id', $profile_id);

                $runner = new shopBwyandexmarketPluginRunController();
                $_POST['processId'] = null;

                $moved = false;
                $ready = false;
                do {
                    ob_start();
                    if (empty($_POST['processId'])) {
                        $_POST['processId'] = $runner->processId;
                    } else {
                        sleep(1);
                    }
                    if ($ready) {
                        $_POST['cleanup'] = true;
                        $moved = true;
                    }
                    $runner->execute();
                    $out = ob_get_clean();
                    $result = json_decode($out, true);
                    $ready = !empty($result) && is_array($result) && ifempty($result['ready']);
                } while (!$ready || !$moved);
                //TODO check errors
            }
        }

        waFiles::readFile($path, waRequest::get('download') ? 'bwyandexmarket.xml' : null);
    }

    public function dtdAction()
    {
        waFiles::readFile(shopBwyandexmarketPlugin::path('shops.dtd'));
    }
}
