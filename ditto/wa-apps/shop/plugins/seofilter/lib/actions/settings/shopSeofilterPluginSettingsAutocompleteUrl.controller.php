<?php

class shopSeofilterPluginSettingsAutocompleteUrlController extends waJsonController
{
    public function execute()
    {
        $value = waRequest::get('value');
        $m_settings = new shopSeofilterFeaturesModel();

        $url = $m_settings->transliterate($value, false);

        $this->response = array(
            'url' => $url
        );
    }
}
