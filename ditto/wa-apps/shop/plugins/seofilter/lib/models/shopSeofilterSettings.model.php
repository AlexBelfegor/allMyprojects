<?php

class shopSeofilterSettingsModel extends waModel
{
    const GENERAL_STOREFRONT = 'general';
    public static function getAllowSettings() { return self::$allow_settings; }

    protected static $allow_settings = array(
        'storefront_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1',
        'description'
    );
    protected $table = 'shop_seofilter_settings';
    protected $id = 'storefront';

    public function getAllByStorefront($storefront)
    {
        return $this->query("SELECT * FROM `".$this->table."` WHERE `storefront` = s:storefront", array('storefront' => $storefront))->fetchAll();
    }

    public static function getCategoryByRoute($route)
    {
        $m_settings = new self();

        $sql = "SELECT c.id, c.name, c.depth FROM shop_category c LEFT JOIN shop_category_routes cr ON c.id = cr.category_id WHERE (cr.route IS NULL OR cr.route = '".$route."') ORDER BY c.left_key";
        return $m_settings->query($sql)->fetchAll();
    }

    public static function setSettings($settings)
    {
        $m_settings = new self();

        $storefronts = self::getStorefronts();
        $allow_pages = self::getAllowSettings();

        foreach ($storefronts as $storefront) {

            if (isset($settings[$storefront])) {

                $setting = $settings[$storefront];

                foreach ($allow_pages as $allow_setting) {

                    if ( isset($setting[$allow_setting]) ) {
                        $value = $setting[$allow_setting];
                    }
                    else {
                        $value = '';
                    }

                    $m_settings->replace(
                        array(
                            'storefront' => $storefront,
                            'name' => $allow_setting,
                            'value' => $value
                        )
                    );

                }
            }
        }
    }

    public static function getSeofilterSettings($storefront = null)
    {
        $m_settings = new self();

        $result = array();
        $storefronts = self::getStorefronts();

        foreach ($storefronts as $_storefront)
        {
            if (!is_null($storefront) and $storefront != $_storefront)
            {
                continue;
            }

            $result[$_storefront] = array();

            foreach (self::$allow_settings as $allow_setting)
            {
                $result[$_storefront][$allow_setting] = '';
            }

            $settings = $m_settings->getAllByStorefront($_storefront);


            foreach ($settings as $setting)
            {
                if (isset($result[$_storefront][$setting['name']]))
                {
                    $result[$_storefront][$setting['name']] = $setting['value'];
                }
            }

            if (!is_null($storefront))
            {
                return $result[$_storefront];
            }
        }

        return $result;
    }

    public static function getGeneralSeofilterSettings()
    {
        return self::getSeofilterSettings(self::GENERAL_STOREFRONT);
    }

    public static function getStorefrontSeofilterSettings()
    {
        return self::getSeofilterSettings(self::getCurrentStorefront());
    }

    public static function getStorefronts()
    {
        $routing = wa()->getRouting();

        $storefronts = array(self::GENERAL_STOREFRONT);

        $domains = $routing->getByApp('shop');

        foreach ($domains as $domain => $domain_routes)
        {
            foreach ($domain_routes as $route)
            {
                $storefronts[] = $domain.'/'.$route['url'];
            }
        }

        return $storefronts;
    }

    public static function getCurrentStorefront()
    {
        $routing = wa()->getRouting();
        $domain = $routing->getDomain();
        $route = $routing->getRoute();
        $storefronts = self::getStorefronts();
        $currentRouteUrl = $domain.'/'.$route['url'];

        return in_array($currentRouteUrl, $storefronts) ? $currentRouteUrl : null;
    }
}