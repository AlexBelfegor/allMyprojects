<?php

//error_reporting(E_ALL);
//ini_set('display_errors', true);

class shopEarnberryPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        //var_dump();

        $wa = wa();
        $domain = $wa->getRouting()->getDomain(null, true);
        $domain_config_path = $wa->getConfig()->getConfigPath('domains/'.$domain.'.php', true, 'site');

        if (file_exists($domain_config_path)) {
            $settings = include $domain_config_path;
            if (isset($settings['google_analytics']) && strlen(trim($settings['google_analytics'])) > 0)
            {
                $googleAnalyticsCode = $settings['google_analytics'];
                $gaCode = <<<HTML

HTML;

                $this->view->assign('ga_code', $gaCode);
                $this->view->assign('earnberry_tip_to_change_ga', true);
            }
        }
        $possibleTotalCalcVariants = array(
            '_revenue' => array('name' => 'Чистая прибыль', 'desc' => 'Прибыль расчитывается как <b>Цена - Закупочная цена</b>'),
            '_marge_percent' => array('name' => 'Процент оборота', 'desc' => 'Прибыль рассчитывается как <b>Цена * (Процент оборота/100)</b>.'),
            '_price_margin' => array('name' => 'Наценка в процентах', 'desc' => 'Прибыль расчитывается как <b>(Цена - Цена/1+(Наценка/100))</b>. ')
        );
        $this->view->assign('total_calc_variants', $possibleTotalCalcVariants);
        $this->view->assign('earnberry_settings', $this->plugin()->getEarnberrySettings());
    }
    private function plugin()
    {
        static $plugin;
        if (!$plugin) {
            $plugin = wa()->getPlugin('earnberry');
        }
        return $plugin;
    }
}