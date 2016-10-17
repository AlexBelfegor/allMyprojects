<?php

class shopKmgtmPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        $domains = wa()->getRouting()->getDomains();
        $domains_info = array();

        foreach($domains as $d) {
            $domains_info[$d]['domain'] = $d;
            $domain_config_path = wa('site')->getConfig()->getConfigPath('domains/'.$d.'.php', true, 'site');
            $domains_info[$d]['ua_exist'] = false;
            if (file_exists($domain_config_path)) {
                /**
                 * @var $domain_config array
                 */
                $domain_config = include($domain_config_path);
                // if google analytics
                if (isset($domain_config['google_analytics']) && is_array($domain_config['google_analytics']) && !empty($domain_config['google_analytics']['universal']) && !empty($domain_config['google_analytics']['code'])) {
                    $domains_info[$d]['ua_exist'] = $domain_config['google_analytics']['code'];
                }
            }
        }

        $this->view->assign('domains', $domains_info);
        /**
         * @var shopProductbrandsPlugin $plugin
         */
        $plugin = wa()->getPlugin('kmgtm');
        $this->view->assign('settings', $plugin->getSettings());
    }
}
