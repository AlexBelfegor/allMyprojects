<?php

require_once dirname(__FILE__) . '/vendor/SlabFilter.class.php';
require_once dirname(__FILE__) . '/vendor/SlabFilterValue.class.php';
require_once dirname(__FILE__) . '/vendor/SlabFiltersCollection.class.php';

/**require_once __DIR__.'/lib/vendor/xhprof_lib/utils/xhprof_lib.php';
require_once __DIR__.'/lib/vendor/xhprof_lib/utils/xhprof_runs.php';*/

class shopFeatures3000Plugin extends shopPlugin
{
    public function addPluginStatic($p)
    {
    	$defaultThemeStylesScripts = '';
    	if ($this->getSettings('enabled_theme_template') == false) 
    	{
    		$defaultThemeStylesScripts = '
    		<link rel="stylesheet" href="'.$this->getPluginStaticUrl().'css/jquery.slider.min.css" type="text/css" charset="utf-8">
    		<script type="text/javascript" src="'.$this->getPluginStaticUrl().'js/filter3000_theme_default.js"></script>
    		<script type="text/javascript" src="'.$this->getPluginStaticUrl().'js/jquery.slider.min.js"></script>
    		';
    	}
    	return $defaultThemeStylesScripts.'<script type="text/javascript" src="'.$this->getPluginStaticUrl().'js/filter3000.js"></script>';
    }   
}